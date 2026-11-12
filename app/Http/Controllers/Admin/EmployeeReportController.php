<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeeReportExport;
use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReportController extends Controller
{
    public function index()
    {
        $data['title'] = 'Employees Daily Reports ';
        $data['employees'] = Employee::where('status', 'Active')->orderBy('employee_name')->get();
        $data['supervisors'] = Employee::where('is_supervisor', 1)->orWhere('is_supervisor', '1')->orderBy('employee_name')->get();
        $data['projects'] = Project::active()->orderBy('project_name')->get();
        return view('admin.employee_report.index', $data);
    }

    public function list(Request $request)
    {
        $startDate = $request->start_date
            ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->end_date
            ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay()
            : now()->endOfMonth();
        if ($endDate->lt($startDate)) {
            return response()->json([
                'draw' => (int) $request->draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
        $months = [];
        $cursor = $startDate->copy()->startOfMonth();
        $lastMonth = $endDate->copy()->startOfMonth();
        while ($cursor->lte($lastMonth)) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }
        $page = (int) $request->input('page', 0);
        if (!isset($months[$page])) {
            return response()->json([
                'draw' => (int) $request->draw,
                'recordsTotal' => count($months),
                'recordsFiltered' => count($months),
                'data' => [],
            ]);
        }
        $selectedMonth = $months[$page];
        $monthStart = $selectedMonth->copy()->startOfMonth();
        if ($monthStart->lt($startDate)) {
            $monthStart = $startDate->copy();
        }
        $monthEnd = $selectedMonth->copy()->endOfMonth();
        if ($monthEnd->gt($endDate)) {
            $monthEnd = $endDate->copy();
        }

        // Do not generate rows for future dates, so "Not Filled" is not shown for those dates.
        $today = now()->endOfDay();
        if ($monthEnd->gt($today)) {
            $monthEnd = $today->copy();
        }

        $query = DailyReport::with([
            'employeeDetails',
            'projectDetails'
        ])->whereBetween('DrEntryDate', [
            $monthStart->format('Y-m-d'),
            $monthEnd->format('Y-m-d'),
        ]);

        if ($request->filled('employee_id')) {
            $query->where('EmpId', $request->employee_id);
        }

        if ($request->filled('supervisor_id')) {
            $query->whereHas('employeeDetails', function ($q) use ($request) {
                $q->where('supervisor_id', $request->supervisor_id);
            });
        }
        $reports = $query
            ->orderBy('DrEntryDate')
            ->orderBy('StartingTime')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->DrEntryDate)->format('Y-m-d');
            });
        $employeeName = '';
        if ($request->filled('employee_id')) {
            $employee = Employee::find($request->employee_id);
            $employeeName = $employee ? $employee->employee_name : '';
        }

        $rows = [];
        $index = 1;
        $currentDate = $monthStart->copy();

        // Return no rows when the selected range is entirely in the future.
        if ($currentDate->gt($monthEnd)) {
            return response()->json([
                'draw' => (int) $request->draw,
                'recordsTotal' => count($months),
                'recordsFiltered' => count($months),
                'data' => [],
            ]);
        }

        while ($currentDate->lte($monthEnd)) {
            $dateKey = $currentDate->format('Y-m-d');
            if ($currentDate->isSunday()) {
                $rows[] = [
                    'DT_RowIndex'     => '',
                    'employee_name'   => '',
                    'project_name'    => '',
                    'WorkDescription' => '',
                    'StartingTime'    => '',
                    'EndingTime'      => '',
                    'TotalHours'      => '',
                    'DrEntryDate'     => $currentDate->format('d-m-Y'),
                    'CreationDate'    => '',
                    'group_date'      => $currentDate->format('d-m-Y'),
                    'day_type'        => 'sunday',
                    'is_dummy'        => true,
                ];
            } else {
                if (isset($reports[$dateKey])) {
                    foreach ($reports[$dateKey] as $report) {
                        $rows[] = [
                            'DT_RowIndex'     => $index++,
                            'employee_name'   => optional($report->employeeDetails)->employee_name,
                            'project_name'    => optional($report->projectDetails)->project_name,
                            'WorkDescription' => $report->WorkDescription,
                            'StartingTime'    => $report->StartingTime
                                ? Carbon::parse($report->StartingTime)->format('h:i A')
                                : '',
                            'EndingTime'      => $report->EndingTime
                                ? Carbon::parse($report->EndingTime)->format('h:i A')
                                : '',
                            'TotalHours'      => $report->TotalHours,
                            'DrEntryDate'     => Carbon::parse($report->DrEntryDate)->format('d-m-Y'),
                            'CreationDate'    => $report->CreationDate
                                ? Carbon::parse($report->CreationDate)->format('d-m-Y')
                                : '',
                            'group_date'      => Carbon::parse($report->DrEntryDate)->format('d-m-Y'),
                            'day_type'        => $currentDate->isSaturday() ? 'saturday' : 'normal',
                            'is_dummy'        => false,
                        ];
                    }
                } else {
                    $rows[] = [
                        'DT_RowIndex'     => '',
                        'employee_name'   => $employeeName,
                        'project_name'    => '',
                        'WorkDescription' => 'Not Filled',
                        'StartingTime'    => '',
                        'EndingTime'      => '',
                        'TotalHours'      => '',
                        'DrEntryDate'     => $currentDate->format('d-m-Y'),
                        'CreationDate'    => '',
                        'group_date'      => $currentDate->format('d-m-Y'),
                        'day_type'        => $currentDate->isSaturday() ? 'saturday' : 'normal',
                        'is_dummy'        => false,
                    ];
                }
            }

            $currentDate->addDay();
        }
        return response()->json([
            'draw' => (int) $request->draw,
            'recordsTotal' => count($months),
            'recordsFiltered' => count($months),
            'data' => $rows,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,employee_id'],
            'start_date' => ['nullable', 'date_format:d-m-Y'],
            'end_date' => ['nullable', 'date_format:d-m-Y'],
        ]);

        $startDate = $request->start_date
            ? Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay()
            : now()->startOfMonth();
        $endDate = $request->end_date
            ? Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay()
            : now()->endOfMonth();

        $employee = Employee::find($request->employee_id);
        $employeeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $employee?->employee_name ?? 'employee');
        $fileName = 'Employee_Report_' . trim($employeeName, '_') . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(
            new EmployeeReportExport((int) $request->employee_id, $startDate, $endDate),
            $fileName
        );
    }
}
