<?php

namespace App\Exports;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyReportsExport implements FromCollection, WithHeadings
{
    protected $filters;
    protected $employeeId;

    public function __construct($filters = [], $employeeId = null)
    {
        $this->filters = $filters;
        $this->employeeId = $employeeId;
    }

    public function collection()
    {
        $query = DailyReport::where('EmpId', $this->employeeId)
            ->whereHas('project', fn($q) => $q->active())
            ->with('project');

        if (!empty($this->filters['month'])) {
            $query->whereMonth('DrEntryDate', $this->filters['month']);
        }
        if (!empty($this->filters['from_date'])) {
            $query->whereDate('DrEntryDate', '>=', $this->filters['from_date']);
        }
        if (!empty($this->filters['to_date'])) {
            $query->whereDate('DrEntryDate', '<=', $this->filters['to_date']);
        }
        if (!empty($this->filters['project_id'])) {
            $query->where('ProjectID', $this->filters['project_id']);
        }

        $reports = $query->get();

        return $reports->map(function ($work) {
            return [
                'DR Date'       => $work->DrEntryDate?->format('Y-m-d') ?? '-',
                'Fill Date'     => $work->CreationDate?->format('Y-m-d') ?? '-',
                'Project'       => $work->project->project_name ?? 'N/A',
                'Description'   => $work->WorkDescription ?? '',
                'Start Time'    => $work->StartingTime ? date('H:i', strtotime($work->StartingTime)) : '',
                'End Time'      => $work->EndingTime ? date('H:i', strtotime($work->EndingTime)) : '',
                'Total Hours'   => $work->TotalHours ? substr($work->TotalHours, 0, 5) : '00:00',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'DR Date',
            'Fill Date',
            'Project',
            'Description',
            'Start Time',
            'End Time',
            'Total Hours',
        ];
    }
}
