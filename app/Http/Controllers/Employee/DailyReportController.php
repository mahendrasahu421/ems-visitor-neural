<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Mail\DailyReportMail;
use App\Exports\DailyReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class DailyReportController extends Controller
{
    public function index()
    {
        $data['title'] = 'EMS / Daily Report';
        $data['projects'] = Project::active()
            ->select('project_id', 'project_name')
            ->orderBy('project_name')
            ->get();
        return view('employee.daily-report.index', $data);
    }

    public function export(Request $request)
    {
        $filters = [
            'month'      => $request->month,
            'from_date'  => $request->from_date,
            'to_date'    => $request->to_date,
            'project_id' => $request->project_id,
        ];

        $employeeId = Auth::guard('employee')->id();
        $fileName = 'DailyReports_' . date('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new DailyReportsExport($filters, $employeeId), $fileName);
    }





    public function store(Request $request)
    {
        $request->validate([
            'report_date'   => [
                'required',
                'date',
                'after_or_equal:' . now()->subDays(2)->format('Y-m-d'),
                'before_or_equal:' . now()->format('Y-m-d'),
            ],
            'project_id.*'  => [
                'nullable',
                $this->activeClientProjectRule(),
            ],
            'description.*' => [
                'nullable',
                'string',
                'not_regex:/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}]/u',
            ],
            'start_time.*'  => 'nullable|date_format:H:i',
            'end_time.*'    => 'nullable|date_format:H:i',
        ], [
            'description.*.not_regex' => 'Emojis are not allowed in the Work Description.',
        ]);

        $employeeId  = Auth::guard('employee')->id();
        $savedReports = [];

        $hasAnyInput = false;
        $errorMsg = null;

        foreach ($request->project_id ?? [] as $index => $projectId) {

            $description = trim($request->description[$index] ?? '');
            $start       = $request->start_time[$index] ?? null;
            $end         = $request->end_time[$index] ?? null;
            if ($projectId || $description || $start || $end) {
                $hasAnyInput = true;

                if (!$projectId) {
                    $errorMsg = 'Please select Project.';
                    break;
                }

                if (!$description) {
                    $errorMsg = 'Please enter Work Description.';
                    break;
                }

                if (!$start) {
                    $errorMsg = 'Please select Start Time.';
                    break;
                }

                if (!$end) {
                    $errorMsg = 'Please select End Time.';
                    break;
                }

                // Start Time and End Time cannot be the same.
                if ($start === $end) {
                    $errorMsg = 'Start Time and End Time cannot be same.';
                    break;
                }

                // End Time must be after Start Time.
                [$sh, $sm] = explode(':', $start);
                [$eh, $em] = explode(':', $end);
                $diffCheck = ($eh * 60 + $em) - ($sh * 60 + $sm);

                if ($diffCheck <= 0) {
                    $errorMsg = 'End Time must be after Start Time.';
                    break;
                }
            }

            if (!$projectId && !$description && !$start && !$end) {
                continue;
            }

            [$sh, $sm] = explode(':', $start);
            [$eh, $em] = explode(':', $end);

            $diff = ($eh * 60 + $em) - ($sh * 60 + $sm);
            if ($diff < 0) $diff = 0;

            $hours = sprintf('%02d:%02d', floor($diff / 60), $diff % 60);

            $report = DailyReport::create([
                'EmpId'           => $employeeId,
                'ProjectID'       => $projectId,
                'WorkDescription' => $description,
                'StartingTime'    => $start,
                'EndingTime'      => $end,
                'TotalHours'      => $hours,
                'DrEntryDate'     => $request->report_date,
                'CreationDate'    => now(),
            ]);

            $savedReports[] = $report->toArray();
        }

        if (!$hasAnyInput) {
            return redirect()->back()
                ->with('error', 'Please fill Daily Report details.')
                ->withInput();
        }

        if ($errorMsg) {
            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput();
        }

        $employeeEmail = Auth::guard('employee')->user()->employee_email;
        $adminEmails   = \App\Models\User::where('role_id', 2)->pluck('email')->toArray();

        if ($employeeEmail) {
            Mail::to($employeeEmail)->send(new DailyReportMail($savedReports));
        }

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new DailyReportMail($savedReports));
        }

        return redirect()->back()->with('success', 'Daily Report saved successfully.');
    }



    public function list()
    {
        $data['title'] = 'EMS / Daily Report List';
        $data['projects'] = Project::active()
            ->select('project_id as id', 'project_name')
            ->orderBy('project_name')
            ->get();
        $data['worksheets'] = DailyReport::whereHas('project', function ($query) {
            $query->active();
        })->with('project')->get();

        return view('employee.daily-report.list', $data);
    }

    public function ajaxList(Request $request)
    {
        $employeeId = Auth::guard('employee')->id();

        $search = $request->input('search.value') ?? null;
        $limit  = $request->input('length', 10);
        $start  = $request->input('start', 0);

        $query = DailyReport::where('EmpId', $employeeId)
            ->whereHas('project', function ($q) {
                $q->active();
            })
            ->with('project');

        // ✅ Filters
        if ($request->month) {
            $query->whereMonth('DrEntryDate', $request->month);
        }

        if ($request->from_date) {
            $query->whereDate('DrEntryDate', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('DrEntryDate', '<=', $request->to_date);
        }

        if ($request->project_id) {
            $query->where('ProjectID', $request->project_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('WorkDescription', 'like', "%{$search}%")
                    ->orWhere('DrEntryDate', 'like', "%{$search}%")
                    ->orWhere('CreationDate', 'like', "%{$search}%");
            });
        }

        $totalRecord = $query->count();

        $reports = $query->skip($start)->take($limit)->get();

        $rows = [];

        foreach ($reports as $index => $work) {
            $rows[] = [
                'DT_RowIndex'     => $start + $index + 1,
                'DrEntryDate'  => $work->DrEntryDate
                    ? \Carbon\Carbon::parse($work->DrEntryDate)->format('d-m-Y')
                    : '-',

                'CreationDate' => $work->CreationDate
                    ? \Carbon\Carbon::parse($work->CreationDate)->format('d-m-Y')
                    : '-',
                'project_name'    => $work->project ? $work->project->project_name : 'N/A',
                'WorkDescription' => $work->WorkDescription,
                'StartingTime'    => $work->StartingTime ? date('H:i', strtotime($work->StartingTime)) : '',
                'EndingTime'      => $work->EndingTime ? date('H:i', strtotime($work->EndingTime)) : '',
                'TotalHours'      => $work->TotalHours ? substr($work->TotalHours, 0, 5) : '00:00',
            ];
        }

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecord,
            'recordsFiltered' => $totalRecord,
            'data'            => $rows
        ]);
    }

    public function project()
    {
        $data['title'] = 'EMS / Daily Report';
        $data['projects'] = Project::active()
            ->select('project_id', 'project_name')
            ->orderBy('project_name')
            ->get();
        return view('employee.daily-report.project', $data);
    }

    public function projectAjax(Request $request)
    {
        $employeeId = Auth::guard('employee')->id();
        $query = DailyReport::where('EmpId', $employeeId)
            ->with('project');

        if ($request->project_id) {
            $query->where('ProjectID', $request->project_id);
        }

        if ($request->from_date) {
            $query->whereDate('DrEntryDate', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('DrEntryDate', '<=', $request->to_date);
        }

        $totalRecord = $query->count();
        $reports = $query->skip($request->start ?? 0)->take($request->length ?? 10)->get();

        $rows = [];
        foreach ($reports as $index => $work) {
            $rows[] = [
                'DT_RowIndex'     => ($request->start ?? 0) + $index + 1,
                'DrEntryDate' => $work->DrEntryDate
                    ? \Carbon\Carbon::parse($work->DrEntryDate)->format('d-m-Y')
                    : '-',

                'project_name'    => $work->project ? $work->project->project_name : 'N/A',
                'WorkDescription' => $work->WorkDescription,
                'StartingTime'    => $work->StartingTime ? date('H:i', strtotime($work->StartingTime)) : '',
                'EndingTime'      => $work->EndingTime ? date('H:i', strtotime($work->EndingTime)) : '',
                'TotalHours'      => $work->TotalHours ?? '00:00',
            ];
        }

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecord,
            'recordsFiltered' => $totalRecord,
            'data'            => $rows
        ]);
    }

    private function activeClientProjectRule()
    {
        $rule = Rule::exists('client_projects', 'project_id');
        $statusColumn = $this->clientProjectStatusColumn();

        return $statusColumn ? $rule->where($statusColumn, 1) : $rule;
    }

    private function clientProjectStatusColumn(): ?string
    {
        if (Schema::hasColumn('client_projects', 'status')) {
            return 'status';
        }

        if (Schema::hasColumn('client_projects', 'DelStatus')) {
            return 'DelStatus';
        }

        return null;
    }
}
