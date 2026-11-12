<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectTicket;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $title = 'Client Dashboard';

        $projects = $client ? $client->matchingProjects()->get() : collect();
        $projectCount = $projects->count();

        $openTicketCount = ProjectTicket::where('client_id', $client?->id)
            ->whereIn('status', ['Open', 'In Progress', 'Pending'])
            ->count();

        $resolvedTicketCount = ProjectTicket::where('client_id', $client?->id)
            ->whereIn('status', ['Resolved', 'Closed'])
            ->count();

        $totalTicketCount = ProjectTicket::where('client_id', $client?->id)->count();

        $recentTickets = ProjectTicket::with('project')
            ->where('client_id', $client?->id)
            ->latest()
            ->take(5)
            ->get();

        $invoices = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('invoice') && $client && $client->invoice_client_id) {
            $invoices = \Illuminate\Support\Facades\DB::table('invoice as i')
                ->leftJoin('companymaster as co', 'i.CompanyID', '=', 'co.CompanyID')
                ->select('i.*', 'co.CompanyName')
                ->where('i.ClientID', $client->invoice_client_id)
                ->orderByDesc('i.id')
                ->take(5)
                ->get();
        }

        return view('client.dashboard', compact(
            'title',
            'client',
            'projects',
            'projectCount',
            'openTicketCount',
            'resolvedTicketCount',
            'totalTicketCount',
            'recentTickets',
            'invoices'
        ));
    }
}
