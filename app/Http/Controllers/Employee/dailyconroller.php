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

class DailyReportController extends Controller
{
    public function index()
    {
        $data['title'] = 'EMS / Daily Report';
        $data['projects'] = Project::active()->select('project_id as id', 'project_name')->get();
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
            'report_date'   => 'required|date',
            'project_id.*'  => 'nullable|exists:client_projects,project_id',
            'description.*' => 'nullable|string',
            'start_time.*'  => 'nullable|date_format:H:i',
            'end_time.*'    => 'nullable|date_format:H:i',
        ]);

        $rows = $request->project_id ?? [];
        $employeeId = Auth::guard('employee')->id();
        $savedReports = [];
        $validRows = 0;

        foreach ($rows as $index => $projectId) {
            if (empty($projectId)) continue;
            $validRows++;

            $start = $request->start_time[$index] ?? null;
            $end   = $request->end_time[$index] ?? null;
            $hours = "00:00";
            if ($start && $end) {
                [$sh, $sm] = explode(':', $start);
                [$eh, $em] = explode(':', $end);
                $diff = ($eh * 60 + $em) - ($sh * 60 + $sm);
                if ($diff < 0) $diff = 0;
                $hours = sprintf('%02d:%02d', floor($diff / 60), $diff % 60);
            }
            $report = DailyReport::create([
                'EmpId'            => $employeeId,
                'ProjectID'        => $projectId,
                'WorkDescription'  => $request->description[$index] ?? '',
                'StartingTime'     => $start,
                'EndingTime'       => $end,
                'TotalHours'       => $hours,
                'DrEntryDate'      => $request->report_date,
                'CreationDate'     => now(),
            ]);

            $savedReports[] = $report->toArray();
        }

        if ($validRows == 0) {
            return redirect()->back()
                ->with('error', 'Please fill at least one entry.')
                ->withInput();
        }
        $employeeEmail = Auth::guard('employee')->user()->employee_email;
        $adminEmails   = \App\Models\User::where('role_id', 2)->pluck('email')->toArray();

        if (!empty($employeeEmail)) {
            Mail::to($employeeEmail)->send(new DailyReportMail($savedReports));
        }

        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new DailyReportMail($savedReports));
        }

        return redirect()->back()->with('success', 'Daily Report saved & emailed successfully.');
    }


    public function list()
    {
        $data['title'] = 'EMS / Daily Report List';
        $data['projects'] = Project::active()->select('project_id as id', 'project_name')->get();
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
        $data['projects'] = Project::active()->select('project_id as id', 'project_name')->get();
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
}
