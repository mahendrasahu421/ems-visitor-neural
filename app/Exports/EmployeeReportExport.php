<?php

namespace App\Exports;

use App\Models\DailyReport;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeReportExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        private readonly int $employeeId,
        private readonly Carbon $startDate,
        private readonly Carbon $endDate
    ) {
    }

    public function collection(): Collection
    {
        $employee = Employee::find($this->employeeId);
        $employeeName = $employee?->employee_name ?? '';

        $startDate = $this->startDate->copy()->startOfDay();
        $endDate = $this->endDate->copy()->endOfDay();
        $today = now()->endOfDay();

        if ($endDate->gt($today)) {
            $endDate = $today;
        }

        if ($endDate->lt($startDate)) {
            return collect();
        }

        $reports = DailyReport::with(['employeeDetails', 'projectDetails'])
            ->where('EmpId', $this->employeeId)
            ->whereBetween('DrEntryDate', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->orderBy('DrEntryDate')
            ->orderBy('StartingTime')
            ->get()
            ->groupBy(fn ($report) => Carbon::parse($report->DrEntryDate)->format('Y-m-d'));

        $rows = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');

            if ($currentDate->isSunday()) {
                $rows->push([
                    'Date' => $currentDate->format('d-m-Y'),
                    'Day' => $currentDate->format('l'),
                    'Employee' => $employeeName,
                    'Project' => '',
                    'Work Description' => '',
                    'Start Time' => '',
                    'End Time' => '',
                    'Total Hours' => '',
                    'Submitted Date' => '',
                    'Status' => 'Weekly Off',
                ]);
            } elseif (isset($reports[$dateKey])) {
                foreach ($reports[$dateKey] as $report) {
                    $rows->push([
                        'Date' => Carbon::parse($report->DrEntryDate)->format('d-m-Y'),
                        'Day' => Carbon::parse($report->DrEntryDate)->format('l'),
                        'Employee' => optional($report->employeeDetails)->employee_name ?: $employeeName,
                        'Project' => optional($report->projectDetails)->project_name ?: 'General Work',
                        'Work Description' => $report->WorkDescription,
                        'Start Time' => $report->StartingTime
                            ? Carbon::parse($report->StartingTime)->format('h:i A')
                            : '',
                        'End Time' => $report->EndingTime
                            ? Carbon::parse($report->EndingTime)->format('h:i A')
                            : '',
                        'Total Hours' => $report->TotalHours ?: '',
                        'Submitted Date' => $report->CreationDate
                            ? Carbon::parse($report->CreationDate)->format('d-m-Y')
                            : '',
                        'Status' => 'Submitted',
                    ]);
                }
            } else {
                $rows->push([
                    'Date' => $currentDate->format('d-m-Y'),
                    'Day' => $currentDate->format('l'),
                    'Employee' => $employeeName,
                    'Project' => '',
                    'Work Description' => 'Not Filled',
                    'Start Time' => '',
                    'End Time' => '',
                    'Total Hours' => '',
                    'Submitted Date' => '',
                    'Status' => 'Not Submitted',
                ]);
            }

            $currentDate->addDay();
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Day',
            'Employee',
            'Project',
            'Work Description',
            'Start Time',
            'End Time',
            'Total Hours',
            'Submitted Date',
            'Status',
        ];
    }
}
