<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Mail\ProjectTicketNotificationMail;
use App\Models\ProjectTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class EmployeeTicketController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = (int) Auth::guard('employee')->user()->employee_id;
        $status = $request->input('status');

        $tickets = $this->visibleTickets($employeeId)
            ->with(['client', 'project', 'assignedEmployee'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('employee.tickets.index', [
            'title' => 'My Tickets',
            'tickets' => $tickets,
            'selectedStatus' => $status,
            'counts' => [
                'Open' => (clone $this->visibleTickets($employeeId))->where('status', 'Open')->count(),
                'In Progress' => (clone $this->visibleTickets($employeeId))->where('status', 'In Progress')->count(),
                'Resolved' => (clone $this->visibleTickets($employeeId))->where('status', 'Resolved')->count(),
                'Closed' => (clone $this->visibleTickets($employeeId))->where('status', 'Closed')->count(),
            ],
        ]);
    }

    public function show(ProjectTicket $ticket)
    {
        $employeeId = (int) Auth::guard('employee')->user()->employee_id;
        abort_unless($this->canAccessTicket($ticket, $employeeId), 403);

        return view('employee.tickets.show', [
            'title' => $ticket->ticket_no,
            'ticket' => $ticket->load(['client', 'project', 'assignedEmployee', 'replies.employee']),
            'employeeStatusOptions' => $this->employeeStatusOptions(),
        ]);
    }

    public function update(Request $request, ProjectTicket $ticket)
    {
        $employeeId = (int) Auth::guard('employee')->user()->employee_id;
        abort_unless($this->canAccessTicket($ticket, $employeeId), 403);

        $validated = $request->validate([
            'employee_status' => ['required', 'in:Started,In Progress,Completed'],
            'employee_note' => ['nullable', 'string', 'max:5000'],
            'work_hours' => ['nullable', 'integer', 'min:0', 'max:999'],
            'work_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
        ]);

        $workMinutes = ((int) ($validated['work_hours'] ?? 0) * 60) + (int) ($validated['work_minutes'] ?? 0);

        if ($validated['employee_status'] !== 'Started' && $workMinutes <= 0) {
            return back()
                ->withErrors(['work_minutes' => 'Please enter the time spent on this ticket.'])
                ->withInput();
        }

        DB::transaction(function () use ($ticket, $employeeId, $validated, $workMinutes) {
            $ticket->update([
                'employee_status' => $validated['employee_status'],
                'employee_note' => $validated['employee_note'],
                'employee_responded_at' => now(),
                'status' => $validated['employee_status'] === 'Completed' ? 'Resolved' : 'In Progress',
                'closed_at' => $validated['employee_status'] === 'Completed' ? ($ticket->closed_at ?? now()) : null,
            ]);

            $ticket->replies()->create([
                'employee_id' => $employeeId,
                'status' => $validated['employee_status'],
                'note' => $validated['employee_note'],
                'work_minutes' => $workMinutes,
            ]);
        });

        $this->sendEmployeeResponseNotification($ticket->fresh(['client', 'project', 'assignedEmployee', 'replies.employee']));

        return redirect()
            ->route('employee.tickets.show', $ticket)
            ->with('success', 'Ticket response updated successfully.');
    }

    private function visibleTickets(int $employeeId)
    {
        $projectIds = $this->mappedProjectIds($employeeId);

        return ProjectTicket::query()
            ->where(function ($query) use ($employeeId, $projectIds) {
                $query->where('assigned_employee_id', $employeeId);

                if ($projectIds->isNotEmpty()) {
                    $query->orWhereIn('project_id', $projectIds->all());
                }
            });
    }

    private function canAccessTicket(ProjectTicket $ticket, int $employeeId): bool
    {
        if ((int) $ticket->assigned_employee_id === $employeeId) {
            return true;
        }

        if (!$ticket->project_id) {
            return false;
        }

        return $this->mappedProjectIds($employeeId)
            ->contains((int) $ticket->project_id);
    }

    private function mappedProjectIds(int $employeeId)
    {
        if (!Schema::hasTable('project_employee_mappings')) {
            return collect();
        }

        return DB::table('project_employee_mappings')
            ->where('employee_id', $employeeId)
            ->where('status', 'active')
            ->pluck('project_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function employeeStatusOptions(): array
    {
        return [
            'Started' => 'Started',
            'In Progress' => 'In Progress',
            'Completed' => 'Completed',
        ];
    }

    private function sendEmployeeResponseNotification(ProjectTicket $ticket): void
    {
        try {
            $recipients = $this->adminEmails()
                ->push($ticket->client?->email)
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values();

            if ($recipients->isEmpty()) {
                return;
            }

            Mail::to($recipients->all())->send(new ProjectTicketNotificationMail($ticket, 'employee_response'));
        } catch (\Throwable $e) {
            Log::warning('Project ticket employee response email failed: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);
        }
    }

    private function adminEmails()
    {
        if (!Schema::hasTable('users')) {
            return collect();
        }

        return User::query()
            ->when(Schema::hasColumn('users', 'role_id'), fn ($query) => $query->whereIn('role_id', [1, 2]))
            ->when(Schema::hasColumn('users', 'status'), fn ($query) => $query->where('status', 1))
            ->whereNotNull('email')
            ->pluck('email');
    }
}
