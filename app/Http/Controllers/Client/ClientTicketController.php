<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ProjectTicketNotificationMail;
use App\Models\ProjectTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ClientTicketController extends Controller
{
    public function index(Request $request)
    {
        $client = Auth::guard('client')->user();

        $query = ProjectTicket::with('project')->where('client_id', $client->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('project_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allTickets = ProjectTicket::where('client_id', $client->id)->get();
        $totalCount = $allTickets->count();
        $openCount = $allTickets->whereIn('status', ['Open', 'In Progress', 'Pending'])->count();
        $resolvedCount = $allTickets->whereIn('status', ['Resolved', 'Closed'])->count();
        $criticalCount = $allTickets->whereIn('priority', ['High', 'Critical'])->count();

        $tickets = $query->latest()->paginate(10)->withQueryString();

        return view('client.tickets.index', [
            'title' => 'Support & Issue Tickets',
            'tickets' => $tickets,
            'totalCount' => $totalCount,
            'openCount' => $openCount,
            'resolvedCount' => $resolvedCount,
            'criticalCount' => $criticalCount,
            'selectedStatus' => $request->status,
            'selectedPriority' => $request->priority,
            'search' => $request->search,
        ]);
    }

    public function create()
    {
        $client = Auth::guard('client')->user();

        return view('client.tickets.create', [
            'title' => 'Raise Project Issue',
            'projects' => $client->matchingProjects()
                ->active()
                ->orderBy('project_name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:client_projects,project_id'],
            'project_name' => ['nullable', 'string', 'max:255', 'required_without:project_id'],
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:Low,Medium,High,Critical'],
            'description' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt', 'max:4096'],
        ]);

        $project = null;
        if (!empty($validated['project_id'])) {
            $allowedProjectIds = $client->matchingProjects()->active()->pluck('client_projects.project_id')->all();
            if (!in_array((int) $validated['project_id'], $allowedProjectIds, true)) {
                return back()
                    ->withInput()
                    ->with('error', 'Please select one of your assigned projects.');
            }

            $project = $client->matchingProjects()->active()->find($validated['project_id']);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments', 'public');
        }

        $ticket = ProjectTicket::create([
            'ticket_no' => $this->generateTicketNumber(),
            'client_id' => $client->id,
            'project_id' => $project?->project_id,
            'project_name' => $project?->project_name ?? $validated['project_name'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'status' => 'Open',
        ]);

        $this->sendTicketRaisedNotification($ticket);

        return redirect()
            ->route('client.tickets.show', $ticket)
            ->with('success', 'Ticket raised successfully.');
    }

    public function show(ProjectTicket $ticket)
    {
        $client = Auth::guard('client')->user();

        abort_unless($ticket->client_id === $client->id, 403);

        return view('client.tickets.show', [
            'title' => $ticket->ticket_no,
            'ticket' => $ticket->load(['project', 'replies.employee']),
        ]);
    }

    private function sendTicketRaisedNotification(ProjectTicket $ticket): void
    {
        try {
            $ticket->load(['client', 'project.employees', 'assignedEmployee']);
            $recipients = $this->adminEmails()
                ->merge($this->projectEmployeeEmails($ticket))
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values();

            if ($recipients->isEmpty()) {
                return;
            }

            Mail::to($recipients->all())->send(new ProjectTicketNotificationMail($ticket, 'created'));
        } catch (\Throwable $e) {
            Log::warning('Project ticket notification email failed: ' . $e->getMessage(), [
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

    private function projectEmployeeEmails(ProjectTicket $ticket)
    {
        if (!$ticket->project || !Schema::hasTable('project_employee_mappings')) {
            return collect();
        }

        return $ticket->project->employees()
            ->wherePivot('status', 'active')
            ->whereNotNull('employee_email')
            ->where('employee_email', '<>', '')
            ->where(function ($query) {
                $query->whereNull('employees.status')
                    ->orWhere('employees.status', '')
                    ->orWhere('employees.status', 'Active');
            })
            ->pluck('employee_email');
    }

    private function generateTicketNumber(): string
    {
        do {
            $ticketNo = 'TKT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
        } while (ProjectTicket::where('ticket_no', $ticketNo)->exists());

        return $ticketNo;
    }
}
