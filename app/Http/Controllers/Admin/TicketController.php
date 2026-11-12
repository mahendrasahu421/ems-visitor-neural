<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ProjectTicketNotificationMail;
use App\Models\Employee;
use App\Models\ProjectTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $tickets = ProjectTicket::with(['client', 'project', 'assignedEmployee'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.tickets.index', [
            'title' => 'Client Project Tickets',
            'tickets' => $tickets,
            'selectedStatus' => $status,
            'counts' => [
                'Open' => ProjectTicket::where('status', 'Open')->count(),
                'In Progress' => ProjectTicket::where('status', 'In Progress')->count(),
                'Resolved' => ProjectTicket::where('status', 'Resolved')->count(),
                'Closed' => ProjectTicket::where('status', 'Closed')->count(),
            ],
        ]);
    }

    public function show(ProjectTicket $ticket)
    {
        $projectEmployees = $this->projectEmployees($ticket);

        return view('admin.tickets.show', [
            'title' => $ticket->ticket_no,
            'ticket' => $ticket->load(['client', 'project', 'assignedEmployee', 'replies.employee']),
            'projectEmployees' => $projectEmployees,
            'assignableEmployees' => $projectEmployees->isNotEmpty()
                ? $projectEmployees
                : $this->activeEmployees(),
        ]);
    }

    public function update(Request $request, ProjectTicket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Open,In Progress,Resolved,Closed'],
            'assigned_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'employee_id')],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $projectEmployees = $this->projectEmployees($ticket);
        if ($projectEmployees->isNotEmpty() && !empty($validated['assigned_employee_id'])) {
            $projectEmployeeIds = $projectEmployees->pluck('employee_id')->map(fn ($id) => (int) $id)->all();

            if (!in_array((int) $validated['assigned_employee_id'], $projectEmployeeIds, true)) {
                return back()
                    ->withErrors(['assigned_employee_id' => 'Please assign this ticket to an employee mapped with this project.'])
                    ->withInput();
            }
        }

        $previousAssignedEmployeeId = $ticket->assigned_employee_id;
        $assignedEmployeeId = $validated['assigned_employee_id'] ?? null;
        $isClosingStatus = in_array($validated['status'], ['Resolved', 'Closed'], true);

        $ticket->update([
            'status' => $validated['status'],
            'assigned_employee_id' => $assignedEmployeeId,
            'assigned_at' => $assignedEmployeeId
                ? ((int) $previousAssignedEmployeeId !== (int) $assignedEmployeeId ? now() : $ticket->assigned_at)
                : null,
            'admin_note' => $validated['admin_note'],
            'closed_at' => $isClosingStatus ? ($ticket->closed_at ?? now()) : null,
        ]);

        if ($assignedEmployeeId && (int) $previousAssignedEmployeeId !== (int) $assignedEmployeeId) {
            $this->sendAssignmentNotification($ticket->fresh(['client', 'project', 'assignedEmployee']));
        }

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    private function projectEmployees(ProjectTicket $ticket)
    {
        if (!$ticket->project_id || !Schema::hasTable('project_employee_mappings')) {
            return collect();
        }

        return Employee::query()
            ->join('project_employee_mappings as pem', 'employees.employee_id', '=', 'pem.employee_id')
            ->where('pem.project_id', $ticket->project_id)
            ->where('pem.status', 'active')
            ->where(function ($query) {
                $query->whereNull('employees.status')
                    ->orWhere('employees.status', '')
                    ->orWhere('employees.status', 'Active');
            })
            ->select('employees.*', 'pem.project_role')
            ->orderBy('employees.employee_name')
            ->get();
    }

    private function activeEmployees()
    {
        return Employee::query()
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Active');
            })
            ->orderBy('employee_name')
            ->get();
    }

    private function sendAssignmentNotification(ProjectTicket $ticket): void
    {
        try {
            $email = $ticket->assignedEmployee?->employee_email;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            Mail::to($email)->send(new ProjectTicketNotificationMail($ticket, 'assigned'));
        } catch (\Throwable $e) {
            Log::warning('Project ticket assignment email failed: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'assigned_employee_id' => $ticket->assigned_employee_id,
            ]);
        }
    }
}
