<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Client;
use App\Models\ProjectConcernPerson;
use App\Mail\ClientCredentialsMail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

class InvoiceController extends Controller
{
    public function dashboard()
    {
        return view('invoice.dashboard', [
            'title' => 'Invoice Dashboard',
            'stats' => [
                'clients' => $this->countTable('clientmaster', 'DelStatus = 1'),
                'companies' => $this->countTable('companymaster', 'DelStatus = 1'),
                'invoices' => $this->countTable('invoice'),
                'received' => $this->sumTable('recived_payment', 'received_net', 'status = 1'),
                'expenses' => $this->sumTable('expenses', 'expense_cost', 'expense_status = 1'),
            ],
            'recentInvoices' => $this->table('invoice') ? $this->invoiceQuery()->limit(8)->get() : collect(),
        ]);
    }

    public function clients()
    {
        return view('invoice.clients', [
            'title' => 'Invoice Clients',
            'clientCounts' => [
                'total' => $this->countTable('clientmaster'),
                'active' => $this->countTable('clientmaster', 'DelStatus = 1'),
                'inactive' => $this->countTable('clientmaster', 'DelStatus = 0'),
            ],
        ]);
    }

    public function clientsList(Request $request)
    {
        if (!$this->table('clientmaster')) {
            return $this->emptyDataTable($request);
        }

        $query = DB::table('clientmaster as c')
            ->leftJoin('countrymaster as co', 'c.Country', '=', 'co.CountryID')
            ->leftJoin('statemaster as s', 'c.StateID', '=', 's.StateID')
            ->select('c.*', 'co.CountryName', 's.StateName')
            ->orderByDesc('c.ClientID');

        $statusFilter = (string) $request->input('status_filter', '');
        if ($statusFilter === 'active') {
            $query->where('c.DelStatus', 1);
        } elseif ($statusFilter === 'inactive') {
            $query->where('c.DelStatus', 0);
        }

        return $this->dataTableResponse(
            $request,
            $query,
            ['c.ClientID', 'c.ClientName', 'c.EmailAddress', 'c.ConcernOwner', 'c.MobileNo', 's.StateName', 'co.CountryName', 'c.gst_number', 'c.website', 'c.Address', 'c.Fax'],
            ['c.ClientID', 'c.ClientName', 'c.ConcernOwner', 's.StateName', 'c.DelStatus', null],
            function ($client) {
                $isActive = (int) $client->DelStatus === 1;
                $clientName = trim((string) ($client->ClientName ?? ''));
                $avatar = $this->clientAvatarHtml($clientName !== '' ? $clientName : 'Client');
                $email = trim((string) ($client->EmailAddress ?? ''));
                $website = trim((string) ($client->website ?? ''));
                $gst = trim((string) ($client->gst_number ?? ''));

                $metaItems = [];
                if ($email !== '') {
                    $metaItems[] = '<a href="mailto:' . e($email) . '" class="text-muted text-decoration-none d-inline-flex align-items-center hover-primary" title="Email"><i class="ti ti-mail text-primary me-1"></i>' . e($email) . '</a>';
                }
                if ($website !== '') {
                    $siteUrl = str_starts_with($website, 'http') ? $website : 'https://' . $website;
                    $metaItems[] = '<a href="' . e($siteUrl) . '" target="_blank" class="text-muted text-decoration-none d-inline-flex align-items-center hover-primary" title="Website"><i class="ti ti-world text-info me-1"></i>' . e($website) . '</a>';
                }
                if ($gst !== '') {
                    $metaItems[] = '<span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace fs-11" title="GSTIN"><i class="ti ti-receipt-tax text-warning me-1"></i>' . e($gst) . '</span>';
                }

                $metaHtml = count($metaItems) > 0
                    ? '<div class="d-flex flex-wrap align-items-center gap-2 mt-1 fs-12">' . implode('', $metaItems) . '</div>'
                    : '';

                $clientCol = '<div class="d-flex align-items-center">' .
                    $avatar .
                    '<div class="ms-1">' .
                    '<div class="fw-bold text-dark fs-14">' . e($clientName !== '' ? $clientName : '-') . '</div>' .
                    $metaHtml .
                    '</div></div>';

                $person = trim((string) ($client->ConcernOwner ?? ''));
                $mobile = trim((string) ($client->MobileNo ?? ''));
                $contactCol = '<div>';
                if ($person !== '') {
                    $contactCol .= '<div class="fw-semibold text-dark fs-13 d-flex align-items-center"><i class="ti ti-user me-1 text-muted fs-14"></i>' . e($person) . '</div>';
                } else {
                    $contactCol .= '<span class="text-muted fs-13">-</span>';
                }
                if ($mobile !== '') {
                    $contactCol .= '<div class="mt-1 fs-12"><a href="tel:' . e($mobile) . '" class="text-muted text-decoration-none d-inline-flex align-items-center hover-success"><i class="ti ti-phone me-1 text-success fs-13"></i>' . e($mobile) . '</a></div>';
                }
                $contactCol .= '</div>';

                $state = trim((string) ($client->StateName ?? ''));
                $country = trim((string) ($client->CountryName ?? ''));
                $address = trim((string) ($client->Address ?? ''));
                $locCol = '<div>';
                if ($state !== '' || $country !== '') {
                    $locCol .= '<div class="d-flex align-items-center text-dark fs-13 fw-medium"><i class="ti ti-map-pin me-1 text-danger fs-14"></i>' . e($state ?: ($country ?: '-')) . '</div>';
                    if ($country !== '' && $state !== '') {
                        $locCol .= '<div class="mt-1"><span class="badge bg-light text-secondary border fs-11">' . e($country) . '</span></div>';
                    }
                } elseif ($address !== '') {
                    $locCol .= '<div class="text-muted fs-12 text-truncate" style="max-width: 180px;" title="' . e($address) . '">' . e($address) . '</div>';
                } else {
                    $locCol .= '<span class="text-muted fs-13">-</span>';
                }
                $locCol .= '</div>';

                $statusCol = $isActive
                    ? '<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12 d-inline-flex align-items-center"><span class="client-status-dot bg-success me-1"></span>Active</span>'
                    : '<span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12 d-inline-flex align-items-center"><span class="client-status-dot bg-danger me-1"></span>Inactive</span>';

                $ledgerUrl = route('invoice.ledger', ['client_id' => $client->ClientID]);
                $editUrl = route('invoice.clients.edit', $client->ClientID);
                $clientJson = htmlspecialchars(json_encode([
                    'id' => $client->ClientID,
                    'name' => $clientName,
                    'person' => $person,
                    'email' => $email,
                    'mobile' => $mobile,
                    'website' => $website,
                    'gst' => $gst,
                    'cin' => trim((string) ($client->Fax ?? '')),
                    'address' => $address,
                    'state' => $state,
                    'country' => $country,
                    'status' => $isActive ? 'Active' : 'Inactive',
                    'editUrl' => $editUrl,
                    'ledgerUrl' => $ledgerUrl,
                ]), ENT_QUOTES, 'UTF-8');

                $actionCol = '<div class="d-flex align-items-center gap-1 justify-content-end">' .
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-info rounded-circle btn-view-client" data-client="' . $clientJson . '" title="View Details"><i class="ti ti-eye"></i></button>' .
                    '<a href="' . e($ledgerUrl) . '" class="btn btn-sm btn-icon btn-outline-success rounded-circle" title="View Ledger"><i class="ti ti-report-money"></i></a>' .
                    '<a href="' . e($editUrl) . '" class="btn btn-sm btn-icon btn-outline-primary rounded-circle" title="Edit Client"><i class="ti ti-edit"></i></a>' .
                    '</div>';

                return [
                    'ClientID' => '<span class="badge bg-light text-secondary border font-monospace px-2 py-1 fs-12">#' . $client->ClientID . '</span>',
                    'client' => $clientCol,
                    'contact' => $contactCol,
                    'location' => $locCol,
                    'status' => $statusCol,
                    'action' => $actionCol,
                ];
            }
        );
    }

    public function createClient()
    {
        return view('invoice.clients-form', [
            'title' => 'Create Invoice Client',
            'client' => null,
            'countries' => $this->options('countrymaster', 'CountryID', 'CountryName'),
        ]);
    }

    public function fetchClientStates(Request $request)
    {
        $countryId = (int) $request->input('country_id');

        if ($countryId <= 0) {
            return response()->json(['error' => 'Country ID not provided.']);
        }

        if (!$this->countryIsIndia($countryId)) {
            $stateId = $this->internationalStateId($countryId);

            return response()->json($stateId > 0 ? [
                [
                    'StateID' => $stateId,
                    'StateName' => 'International',
                    'CountryID' => $countryId,
                ]
            ] : []);
        }

        $states = DB::table('statemaster')
            ->where('CountryID', $countryId)
            ->get();

        return response()->json($states);
    }

    public function storeClient(Request $request)
    {
        [$data, $error] = $this->clientPayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        $passwordError = $this->portalPasswordError($request);
        if ($passwordError !== '') {
            return back()->withInput()->with('error', $passwordError);
        }

        if (trim((string) $request->input('password', '')) === '') {
            return back()->withInput()->with('error', 'Password is required for client portal login.');
        }

        try {
            $portalClient = null;

            $invoiceClientId = DB::transaction(function () use ($data, $request, &$portalClient) {
                $invoiceClientId = DB::table('clientmaster')->insertGetId(array_merge([
                    'CompanyID' => 11,
                    'ProductCategoryType' => null,
                    'CategoryType' => null,
                    'ClientCode' => '',
                    'Priority' => null,
                    'Source' => null,
                    'client_logo' => '',
                    'pinNo' => '',
                    'PhoneNo' => '',
                    'DelStatus' => 1,
                    'created_at' => now(),
                ], $data), 'ClientID');

                $portalClient = $this->syncPortalClient((int) $invoiceClientId, $data, $request);

                return (int) $invoiceClientId;
            });
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Client could not be saved: ' . $e->getMessage());
        }

        $message = 'Client saved successfully.';
        $mailWarning = $this->sendPortalLoginIfRequested($request, $portalClient, trim((string) $request->input('password', '')));
        if ($mailWarning !== '') {
            $message .= ' ' . $mailWarning;
        }

        return redirect()->route('invoice.clients')->with('success', $message);
    }

    public function editClient(int $id)
    {
        $client = DB::table('clientmaster')->where('ClientID', $id)->first();
        abort_unless($client, 404);

        return view('invoice.clients-form', [
            'title' => 'Edit Invoice Client',
            'client' => $client,
            'countries' => $this->options('countrymaster', 'CountryID', 'CountryName'),
        ]);
    }

    public function updateClient(Request $request, int $id)
    {
        [$data, $error] = $this->clientPayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        $passwordError = $this->portalPasswordError($request);
        if ($passwordError !== '') {
            return back()->withInput()->with('error', $passwordError);
        }

        try {
            $portalClient = null;

            DB::transaction(function () use ($id, $data, $request, &$portalClient) {
                DB::table('clientmaster')
                    ->where('ClientID', $id)
                    ->update($data);

                $portalClient = $this->syncPortalClient($id, $data, $request);
            });
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Client could not be updated: ' . $e->getMessage());
        }

        $message = 'Client updated successfully.';
        $mailWarning = $this->sendPortalLoginIfRequested($request, $portalClient, trim((string) $request->input('password', '')));
        if ($mailWarning !== '') {
            $message .= ' ' . $mailWarning;
        }

        return redirect()->route('invoice.clients')->with('success', $message);
    }

    public function companies()
    {
        return view('invoice.companies', [
            'title' => 'Invoice Companies',
        ]);
    }

    public function companiesList(Request $request)
    {
        if (!$this->table('companymaster')) {
            return $this->emptyDataTable($request);
        }

        $query = DB::table('companymaster')->orderByDesc('CompanyID');

        return $this->dataTableResponse(
            $request,
            $query,
            ['CompanyID', 'CompanyName', 'CompanyCode', 'EmailAddress', 'MobileNo', 'GSTNumber', 'PanNo', 'Bank1BankName', 'Bank1IFSC'],
            ['CompanyID', 'CompanyName', 'EmailAddress', 'GSTNumber', 'Bank1BankName', 'DelStatus', null],
            function ($company) {
                return [
                    'CompanyID' => $company->CompanyID,
                    'company' => '<strong>' . e($company->CompanyName) . '</strong><br><span class="text-muted">' . e($company->CompanyCode) . '</span>',
                    'contact' => e($company->EmailAddress) . '<br>' . e($company->MobileNo),
                    'gst_pan' => e($company->GSTNumber) . '<br>' . e($company->PanNo),
                    'bank' => e($company->Bank1BankName) . '<br>' . e($company->Bank1IFSC),
                    'status' => $this->activeBadge((int) $company->DelStatus === 1),
                    'action' => $this->iconButton(route('invoice.companies.edit', $company->CompanyID), 'ti ti-edit', 'Edit'),
                ];
            }
        );
    }

    public function createCompany()
    {
        return view('invoice.companies-form', [
            'title' => 'Create Invoice Company',
            'company' => null,
        ]);
    }

    public function storeCompany(Request $request)
    {
        $data = $this->companyPayload($request);

        DB::table('companymaster')->insert($this->tablePayload('companymaster', $data));

        return redirect()->route('invoice.companies')->with('success', 'Company saved successfully.');
    }

    public function editCompany(int $id)
    {
        $company = DB::table('companymaster')->where('CompanyID', $id)->first();
        abort_unless($company, 404);

        return view('invoice.companies-form', [
            'title' => 'Edit Invoice Company',
            'company' => $company,
        ]);
    }

    public function updateCompany(Request $request, int $id)
    {
        $data = $this->companyPayload($request);

        DB::table('companymaster')->where('CompanyID', $id)->update($this->tablePayload('companymaster', $data));

        return redirect()->route('invoice.companies')->with('success', 'Company updated successfully.');
    }

    public function projects()
    {
        return view('invoice.projects', [
            'title' => 'Invoice Projects',
        ]);
    }

    public function projectMapping()
    {
        if (!$this->table('client_projects')) {
            return view('invoice.projects-mapping', [
                'title' => 'Project Mapping',
                'projects' => collect(),
            ]);
        }

        $teamCountSql = $this->table('project_employee_mappings')
            ? '(SELECT COUNT(*) FROM project_employee_mappings pem WHERE pem.project_id = p.project_id)'
            : '0';

        $projects = DB::table('client_projects as p')
            ->leftJoin('clientmaster as c', 'p.client_id', '=', 'c.ClientID')
            ->leftJoin('work_type_master as w', 'p.work_type_id', '=', 'w.WorkTypeID')
            ->select(
                'p.project_id',
                'p.project_name',
                'p.engagement_type',
                'p.project_start_date',
                'p.project_end_date',
                'c.ClientName',
                DB::raw('COALESCE(w.WorkTypeName, p.work_type, p.engagement_type) as work_type_display'),
                DB::raw($teamCountSql . ' as team_count')
            )
            ->orderByDesc('p.project_id')
            ->get();

        return view('invoice.projects-mapping', [
            'title' => 'Project Mapping',
            'projects' => $projects,
        ]);
    }

    public function projectsList(Request $request)
    {
        if (!$this->table('client_projects')) {
            return $this->emptyDataTable($request);
        }

        $query = DB::table('client_projects as p')
            ->leftJoin('clientmaster as c', 'p.client_id', '=', 'c.ClientID')
            ->leftJoin('work_type_master as w', 'p.work_type_id', '=', 'w.WorkTypeID')
            ->select('p.*', 'c.ClientName', 'w.WorkTypeName')
            ->orderByDesc('p.project_id');

        return $this->dataTableResponse(
            $request,
            $query,
            ['p.project_id', 'p.project_name', 'c.ClientName', 'w.WorkTypeName', 'p.engagement_type', 'p.total_value', 'p.total_value_with_tax'],
            ['p.project_id', 'p.project_name', 'c.ClientName', 'w.WorkTypeName', 'p.total_value', 'p.total_value_with_tax', 'p.status', null],
            function ($project) {
                $projectStatus = (int) data_get($project, 'status', data_get($project, 'DelStatus', 1));

                return [
                    'project_id' => $project->project_id,
                    'project_name' => e($project->project_name),
                    'client' => e($project->ClientName ?? '-'),
                    'type' => e($project->WorkTypeName ?? $project->engagement_type ?? '-'),
                    'value' => 'Rs. ' . number_format((float) ($project->total_value ?? 0), 2),
                    'with_tax' => 'Rs. ' . number_format((float) ($project->total_value_with_tax ?? 0), 2),
                    'status' => $this->activeBadge($projectStatus === 1),
                    'action' => '<div class="btn-group">' .
                        $this->iconButton(route('invoice.projects.show', $project->project_id), 'ti ti-eye', 'View', 'btn-outline-primary') .
                        $this->iconButton(route('invoice.projects.employees', $project->project_id), 'ti ti-users', 'Map Employees', 'btn-outline-success') .
                        $this->iconButton(route('invoice.projects.edit', $project->project_id), 'ti ti-edit', 'Edit', 'btn-outline-warning') .
                        '</div>',
                ];
            }
        );
    }

    public function createProject()
    {
        return view('invoice.projects-form', [
            'title' => 'Create Invoice Project',
            'project' => null,
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
            'workTypes' => $this->workTypeOptions(),
            'milestones' => collect(),
            'concernPersons' => collect(),
        ]);
    }

    public function storeProject(Request $request)
    {
        [$data, $milestones, $error] = $this->legacyProjectPayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        $projectId = DB::table('client_projects')->insertGetId($this->tablePayload('client_projects', array_merge($data, [
            'created_at' => now(),
        ])));

        $this->replaceProjectMilestones($projectId, $milestones, $data);
        $this->saveConcernPersonsFromRequest($request, $projectId, (int) $data['client_id'], (string) $data['project_name']);

        return redirect()
            ->route('invoice.projects.employees', $projectId)
            ->with('success', 'Project saved successfully. Please map employees now.');
    }

    public function editProject(int $id)
    {
        $project = DB::table('client_projects')->where('project_id', $id)->first();
        abort_unless($project, 404);

        $concernPersons = ProjectConcernPerson::where('project_id', $id)->with('loginAccount')->get();

        return view('invoice.projects-form', [
            'title' => 'Edit Invoice Project',
            'project' => $project,
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
            'workTypes' => $this->workTypeOptions(),
            'milestones' => $this->table('project_payment_milestones')
                ? DB::table('project_payment_milestones')->where('project_id', $id)->orderBy('sort_order')->get()
                : collect(),
            'concernPersons' => $concernPersons,
        ]);
    }

    public function showProject(int $id)
    {
        $project = $this->projectDetailQuery()
            ->where('p.project_id', $id)
            ->first();
        abort_unless($project, 404);

        $milestones = collect();
        if ($this->table('project_payment_milestones')) {
            $query = DB::table('project_payment_milestones as m')
                ->leftJoin('invoice as i', 'm.milestone_id', '=', 'i.milestone_id')
                ->where('m.project_id', $id)
                ->select(
                    'm.*',
                    'i.id as linked_inv_id',
                    'i.sent_at as inv_sent_at',
                    'i.status as invoice_status'
                );
            if (Schema::hasColumn('project_payment_milestones', 'sort_order')) {
                $query->orderBy('m.sort_order');
            }
            $milestones = $query->orderBy('m.milestone_id')->get();
        }

        $totalAmount = $milestones->sum(fn($m) => (float) (($m->amount_with_tax ?? 0) > 0 ? $m->amount_with_tax : $m->amount));
        $totalPaid = $milestones->sum(fn($m) => (float) ($m->paid_amount ?? 0));
        $teamMembers = $this->projectTeamMembers($id);
        $concernPersons = ProjectConcernPerson::where('project_id', $id)->with('loginAccount')->get();

        return view('invoice.projects-show', [
            'title' => 'View Project',
            'project' => $project,
            'milestones' => $milestones,
            'teamMembers' => $teamMembers,
            'concernPersons' => $concernPersons,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalPending' => max(0, $totalAmount - $totalPaid),
        ]);
    }

    public function mapProjectEmployees(int $id)
    {
        $project = $this->projectDetailQuery()
            ->where('p.project_id', $id)
            ->first();
        abort_unless($project, 404);

        if (!$this->table('project_employee_mappings')) {
            return back()->with('error', 'Project employee mapping table is missing. Please run the migration first.');
        }

        $employees = $this->employeeOptionsForMapping();
        $mappedEmployees = $this->projectTeamMembers($id);

        return view('invoice.projects-map-employees', [
            'title' => 'Map Project Employees',
            'project' => $project,
            'employees' => $employees,
            'mappedEmployees' => $mappedEmployees,
        ]);
    }

    public function storeProjectEmployees(Request $request, int $id)
    {
        $project = DB::table('client_projects')->where('project_id', $id)->first();
        abort_unless($project, 404);

        if (!$this->table('project_employee_mappings')) {
            return back()->with('error', 'Project employee mapping table is missing. Please run the migration first.');
        }

        $employeeIds = collect($request->input('employee_ids', []))
            ->map(fn($employeeId) => (int) $employeeId)
            ->filter(fn($employeeId) => $employeeId > 0)
            ->unique()
            ->values();

        if ($employeeIds->isEmpty()) {
            return back()->withInput()->with('error', 'Please select at least one employee.');
        }

        $validEmployeeIds = DB::table('employees')
            ->whereIn('employee_id', $employeeIds)
            ->pluck('employee_id')
            ->map(fn($employeeId) => (int) $employeeId)
            ->values();

        if ($validEmployeeIds->count() !== $employeeIds->count()) {
            return back()->withInput()->with('error', 'Some selected employee records are invalid.');
        }

        $roles = $request->input('project_role', []);
        $allocations = $request->input('allocation_percent', []);
        $startDates = $request->input('start_date', []);
        $endDates = $request->input('end_date', []);

        DB::transaction(function () use ($id, $validEmployeeIds, $roles, $allocations, $startDates, $endDates) {
            DB::table('project_employee_mappings')->where('project_id', $id)->delete();

            foreach ($validEmployeeIds as $employeeId) {
                $allocation = $allocations[$employeeId] ?? null;
                $allocation = $allocation === null || $allocation === '' ? null : max(0, min(100, (int) $allocation));

                DB::table('project_employee_mappings')->insert($this->tablePayload('project_employee_mappings', [
                    'project_id' => $id,
                    'employee_id' => $employeeId,
                    'project_role' => trim((string) ($roles[$employeeId] ?? '')) ?: null,
                    'allocation_percent' => $allocation,
                    'start_date' => ($startDates[$employeeId] ?? '') ?: null,
                    'end_date' => ($endDates[$employeeId] ?? '') ?: null,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        });

        return redirect()
            ->route('invoice.projects.show', $id)
            ->with('success', 'Project employees mapped successfully.');
    }

    public function updateProject(Request $request, int $id)
    {
        [$data, $milestones, $error] = $this->legacyProjectPayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        DB::table('client_projects')->where('project_id', $id)->update($this->tablePayload('client_projects', $data));
        $this->replaceProjectMilestones($id, $milestones, $data);
        $this->saveConcernPersonsFromRequest($request, $id, (int) $data['client_id'], (string) $data['project_name']);

        return redirect()->route('invoice.projects')->with('success', 'Project updated successfully.');
    }

    public function storeProjectConcernPerson(Request $request, int $id)
    {
        $project = DB::table('client_projects')->where('project_id', $id)->first();
        if (!$project) {
            return response()->json(['ok' => false, 'message' => 'Project not found.'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'mobile' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:100',
            'can_login' => 'nullable',
            'password' => 'nullable|string|min:6',
            'send_email' => 'nullable',
            'is_primary' => 'nullable',
        ]);

        $name = trim($request->input('name'));
        $email = strtolower(trim($request->input('email')));
        $mobile = trim($request->input('mobile', ''));
        $designation = trim($request->input('designation', ''));
        $isPrimary = $request->boolean('is_primary');
        $canLogin = $request->boolean('can_login');
        $sendEmail = $request->boolean('send_email');
        $rawPassword = trim((string) $request->input('password', ''));

        if ($canLogin && empty($rawPassword)) {
            $rawPassword = Str::random(8);
        }

        $clientMaster = DB::table('clientmaster')->where('ClientID', $project->client_id)->first();
        $clientCompanyName = $clientMaster->ClientName ?? null;

        $loginClientId = null;
        if ($canLogin) {
            $clientUser = $this->createOrUpdateClientLogin($name, $email, $mobile, $rawPassword, $project->client_id, $clientCompanyName);
            $loginClientId = $clientUser?->id;
        }

        $credentialsSentAt = null;
        if ($canLogin && $sendEmail && !empty($rawPassword)) {
            try {
                Mail::to($email)->send(new ClientCredentialsMail(
                    recipientName: $name,
                    loginEmail: $email,
                    rawPassword: $rawPassword,
                    projectName: $project->project_name,
                    clientCompanyName: $clientCompanyName,
                    designation: $designation
                ));
                $credentialsSentAt = now();
            } catch (\Throwable $e) {
                Log::warning('Failed sending credentials email: ' . $e->getMessage());
            }
        }

        if ($isPrimary) {
            ProjectConcernPerson::where('project_id', $id)->update(['is_primary' => false]);
        }

        $person = ProjectConcernPerson::create([
            'project_id' => $id,
            'client_id' => $project->client_id,
            'login_client_id' => $loginClientId,
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'designation' => $designation,
            'is_primary' => $isPrimary,
            'can_login' => $canLogin,
            'credentials_sent_at' => $credentialsSentAt,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Concern person added successfully.',
            'person' => $person->load('loginAccount'),
            'credentials_sent' => (bool) $credentialsSentAt,
        ]);
    }

    public function sendConcernPersonCredentials(Request $request, int $id, int $personId)
    {
        $project = DB::table('client_projects')->where('project_id', $id)->first();
        if (!$project) {
            return response()->json(['ok' => false, 'message' => 'Project not found.'], 404);
        }

        $person = ProjectConcernPerson::where('project_id', $id)->where('id', $personId)->first();
        if (!$person) {
            return response()->json(['ok' => false, 'message' => 'Concern person not found.'], 404);
        }

        $clientMaster = DB::table('clientmaster')->where('ClientID', $project->client_id)->first();
        $clientCompanyName = $clientMaster->ClientName ?? null;

        $rawPassword = trim((string) $request->input('password', ''));
        if (empty($rawPassword)) {
            $rawPassword = Str::random(8);
        }

        $clientUser = $this->createOrUpdateClientLogin(
            $person->name,
            $person->email,
            $person->mobile,
            $rawPassword,
            $project->client_id,
            $clientCompanyName
        );

        $person->login_client_id = $clientUser->id;
        $person->can_login = true;

        try {
            Mail::to($person->email)->send(new ClientCredentialsMail(
                recipientName: $person->name,
                loginEmail: $person->email,
                rawPassword: $rawPassword,
                projectName: $project->project_name,
                clientCompanyName: $clientCompanyName,
                designation: $person->designation
            ));
            $person->credentials_sent_at = now();
            $person->save();

            return response()->json([
                'ok' => true,
                'message' => 'Login credentials sent successfully to ' . $person->email,
                'sent_at' => now()->format('d-M-Y H:i'),
            ]);
        } catch (\Throwable $e) {
            $person->save();
            return response()->json([
                'ok' => false,
                'message' => 'Account updated, but email sending failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProjectConcernPerson(int $id, int $personId)
    {
        $person = ProjectConcernPerson::where('project_id', $id)->where('id', $personId)->first();
        if (!$person) {
            return response()->json(['ok' => false, 'message' => 'Concern person not found.'], 404);
        }

        $person->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Concern person removed successfully.'
        ]);
    }

    public function invoices()
    {
        return view('invoice.invoices', [
            'title' => 'Invoices',
        ]);
    }

    public function invoicesList(Request $request)
    {
        if (!$this->table('invoice')) {
            return $this->emptyDataTable($request);
        }

        return $this->dataTableResponse(
            $request,
            $this->invoiceQuery(),
            ['raw:(SELECT invoiceNumber FROM workamount WHERE workamount.invoice_id = i.id LIMIT 1)', 'c.ClientName', 'co.CompanyName', 'i.invoiceDate', 'i.totalAmount'],
            [null, 'c.ClientName', 'co.CompanyName', 'i.invoiceDate', 'i.totalAmount', null, 'i.status', null],
            function ($invoice) {
                return [
                    'invoice_no' => e($invoice->invoiceNumber ?? '-'),
                    'client' => e($invoice->ClientName ?? '-'),
                    'company' => e($invoice->CompanyName ?? '-'),
                    'date' => e($invoice->invoiceDate),
                    'total' => 'Rs. ' . number_format((float) ($invoice->totalAmount ?? 0), 2),
                    'paid' => 'Rs. ' . number_format((float) ($invoice->paidAmount ?? 0), 2),
                    'status' => $this->invoiceStatusBadge((int) $invoice->status),
                    'action' => '<div class="btn-group">' .
                        $this->iconButton(route('invoice.invoices.show', $invoice->id), 'ti ti-download', 'Preview / Download', 'btn-outline-primary') .
                        $this->iconButton(route('invoice.invoices.send', $invoice->id), 'ti ti-send', 'Email', 'btn-outline-info') .
                        $this->iconButton(route('invoice.invoices.edit', $invoice->id), 'ti ti-edit', 'Edit', 'btn-outline-warning') .
                        $this->sentAtLabel($invoice->sent_at ?? null) .
                        '</div>',
                ];
            }
        );
    }

    public function createInvoice()
    {
        return view('invoice.invoices-form', [
            'title' => 'Create Invoice',
            'invoice' => null,
            'line' => null,
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
        ]);
    }

    public function invoiceType(Request $request)
    {
        $newInvoice = $request->input('newInvoice');
        $clientId = (int) $request->input('client_id', 0);

        if ($newInvoice === null || $newInvoice === '') {
            return response('Invalid invoice number.');
        }

        $totalRecord = DB::table('invoice')->where('invoice_type', $newInvoice)->count();
        $currentYear = now()->format('y');
        $lastYear = now()->copy()->addYear()->format('y');

        return response($this->invoicePrefixForClient($clientId) . '/' . $currentYear . '-' . $lastYear . '/00' . ($totalRecord + 1));
    }

    public function checkInvoiceNumber(Request $request)
    {
        $oldInvoiceNumber = (string) $request->input('oldInvoiceNumber', '');

        if ($oldInvoiceNumber === '') {
            return response('Invalid invoice number.');
        }

        return response(DB::table('workamount')->where('invoiceNumber', $oldInvoiceNumber)->exists() ? 'Invoice already exists.' : '');
    }

    public function invoiceClientDetails(Request $request)
    {
        $clientId = (int) $request->input('client_id', 0);
        if ($clientId <= 0) {
            return response()->json(['ok' => false, 'message' => 'invalid client_id']);
        }

        $row = $this->clientLocation($clientId);
        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'client not found']);
        }

        $countryName = (string) ($row->CountryName ?? '');
        $stateName = (string) ($row->StateName ?? '');
        if (strtolower(trim($countryName)) !== '' && strtolower(trim($countryName)) !== 'india') {
            $stateName = 'International';
        }

        return response()->json([
            'ok' => true,
            'country_name' => $countryName,
            'state_name' => $stateName,
            'client_name' => (string) ($row->ClientName ?? ''),
            'concern_person' => (string) ($row->ConcernOwner ?? ''),
            'email' => (string) ($row->EmailAddress ?? ''),
            'mobile' => (string) ($row->MobileNo ?? ''),
        ]);
    }

    public function storeInvoice(Request $request)
    {
        [$data, $lines, $error] = $this->legacyInvoicePayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        $invoiceId = DB::table('invoice')->insertGetId([
            'invoice_type' => $data['invoice_type'],
            'ClientID' => $data['client_id'],
            'CompanyID' => $this->companyIdForClient($data['client_id']),
            'milestone_id' => $data['milestone_id'] > 0 ? $data['milestone_id'] : null,
            'invoiceDate' => $data['invoiceDate'],
            'concern' => $data['concern_person'],
            'mobile' => $data['mobile'],
            'terms' => $data['condition'],
            'deliverydate' => $data['deliveryDate'],
            'duedate' => $data['deudate'],
            'duration_days' => implode(', ', array_column($lines, 'duration')),
            'sacCode' => '998313',
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'],
            'finalAmount' => $data['finalAmount'],
            'sgstAmount' => $data['sgstAmount'],
            'cgstAmount' => $data['cgstAmount'],
            'igstAmount' => $data['igstAmount'],
            'bankChargePercent' => $data['bankChargePercent'],
            'bankChargeAmount' => $data['bankChargeAmount'],
            'totalAmount' => $data['totalAmount'],
            'currency' => $data['currency'],
            'exchange_rate' => $data['exchange_rate'],
            'total_amount_foreign' => $data['total_amount_foreign'],
            'status' => 0,
            'creation_date' => now()->toDateString(),
        ]);

        $invoiceNumber = $data['invoice_type'] === 1
            ? $this->invoicePrefixForClient($data['client_id']) . '/' . \Carbon\Carbon::parse($data['invoiceDate'])->format('y') . '-' . \Carbon\Carbon::parse($data['invoiceDate'])->addYear()->format('y') . '/00' . $invoiceId
            : $data['oldInvoiceNumber'];

        foreach ($lines as $line) {
            DB::table('workamount')->insert([
                'invoice_id' => $invoiceId,
                'invoiceNumber' => $invoiceNumber,
                'description' => $line['description'],
                'amount' => $line['amount'],
                'duration' => $line['duration'],
            ]);
        }

        if ($data['milestone_id'] > 0 && $this->table('project_payment_milestones') && Schema::hasColumn('project_payment_milestones', 'invoiced_amount')) {
            DB::table('project_payment_milestones')
                ->where('milestone_id', $data['milestone_id'])
                ->increment('invoiced_amount', $data['finalAmount']);
        }

        return redirect()->route('invoice.invoices')->with('success', 'Invoice created successfully.');
    }

    public function editInvoice(int $id)
    {
        $invoice = DB::table('invoice')->where('id', $id)->first();
        abort_unless($invoice, 404);

        return view('invoice.invoices-form', [
            'title' => 'Edit Invoice',
            'invoice' => $invoice,
            'line' => DB::table('workamount')->where('invoice_id', $id)->first(),
            'lines' => DB::table('workamount')->where('invoice_id', $id)->get(),
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
        ]);
    }

    public function updateInvoice(Request $request, int $id)
    {
        [$data, $lines, $error] = $this->legacyInvoicePayload($request);
        if ($error !== '') {
            return back()->withInput()->with('error', $error);
        }

        DB::table('invoice')->where('id', $id)->update([
            'invoice_type' => $data['invoice_type'],
            'ClientID' => $data['client_id'],
            'CompanyID' => $this->companyIdForClient($data['client_id']),
            'milestone_id' => $data['milestone_id'] > 0 ? $data['milestone_id'] : null,
            'invoiceDate' => $data['invoiceDate'],
            'concern' => $data['concern_person'],
            'mobile' => $data['mobile'],
            'terms' => $data['condition'],
            'deliverydate' => $data['deliveryDate'],
            'duedate' => $data['deudate'],
            'duration_days' => implode(', ', array_column($lines, 'duration')),
            'sacCode' => '998313',
            'subtotal' => $data['subtotal'],
            'discount' => $data['discount'],
            'finalAmount' => $data['finalAmount'],
            'sgstAmount' => $data['sgstAmount'],
            'cgstAmount' => $data['cgstAmount'],
            'igstAmount' => $data['igstAmount'],
            'bankChargePercent' => $data['bankChargePercent'],
            'bankChargeAmount' => $data['bankChargeAmount'],
            'totalAmount' => $data['totalAmount'],
            'currency' => $data['currency'],
            'exchange_rate' => $data['exchange_rate'],
            'total_amount_foreign' => $data['total_amount_foreign'],
        ]);

        $currentNumber = DB::table('workamount')->where('invoice_id', $id)->value('invoiceNumber');
        $invoiceNumber = $data['invoice_type'] === 1
            ? ($data['newInvoiceNumber'] ?: ($currentNumber ?: $this->nextInvoiceNumber($id)))
            : $data['oldInvoiceNumber'];

        DB::table('workamount')->where('invoice_id', $id)->delete();
        foreach ($lines as $line) {
            DB::table('workamount')->insert([
                'invoice_id' => $id,
                'invoiceNumber' => $invoiceNumber,
                'description' => $line['description'],
                'amount' => $line['amount'],
                'duration' => $line['duration'],
            ]);
        }

        return redirect()->route('invoice.invoices')->with('success', 'Invoice updated successfully.');
    }

    public function showInvoice(int $id)
    {
        abort_unless(DB::table('invoice')->where('id', $id)->exists(), 404);

        $legacyPath = base_path('neuralinvoice/view-invoice.php');
        abort_unless(is_file($legacyPath), 404);

        if (session_status() === PHP_SESSION_NONE) {
            $sessionPath = storage_path('framework/sessions');
            if (is_dir($sessionPath) && is_writable($sessionPath)) {
                ini_set('session.save_path', $sessionPath);
            }
        }

        $previousDirectory = getcwd();
        $previousGet = $_GET;
        $_GET['invoice_id'] = $id;
        chdir(dirname($legacyPath));

        ob_start();
        try {
            include $legacyPath;
            $content = ob_get_clean();
        } finally {
            $_GET = $previousGet;
            chdir($previousDirectory ?: base_path());
        }

        $content = str_replace('href="client-invoice-list.php"', 'href="' . route('invoice.invoices') . '"', $content);
        $content = str_replace(
            '<button id="convertToPDF" class="btn btn-success">Download PDF</button>',
            '<button id="convertToPDF" class="btn btn-success">Download PDF</button> <a href="' . route('invoice.invoices.send', $id) . '" class="btn btn-primary">Send Email</a>',
            $content
        );
        $content = str_replace("url: 'update_status.php',", "url: '" . route('invoice.invoices.mark-sent', $id) . "',", $content);
        $content = str_replace(
            "data: { invoice_id: $('#invoice_id').val() },",
            "data: { invoice_id: $('#invoice_id').val(), _token: '" . csrf_token() . "' },",
            $content
        );
        $content = preg_replace_callback('/src="([^"]+)"/', function ($matches) {
            $src = $matches[1];
            if (preg_match('#^(https?:)?//#', $src) || str_starts_with($src, '/') || str_starts_with($src, 'data:')) {
                return $matches[0];
            }

            return 'src="' . url('/neuralinvoice/' . ltrim($src, '/')) . '"';
        }, $content);

        return response($content);
    }

    public function sendInvoiceForm(int $id)
    {
        $invoice = $this->invoiceSendRow($id);
        abort_unless($invoice, 404);

        $mailConfig = $this->legacyMailConfig();
        $companyName = $invoice->CompanyName ?: 'Neural Informatics';
        $companyEmail = $mailConfig['from_address'] ?: ($invoice->CompanyEmail ?: 'billing@neuralinformatics.com');

        return view('invoice.send-invoice', [
            'title' => 'Send Invoice to Client',
            'invoice' => $invoice,
            'companyName' => $companyName,
            'companyEmail' => $companyEmail,
        ]);
    }

    public function sendInvoice(Request $request, int $id)
    {
        $invoice = $this->invoiceSendRow($id);
        abort_unless($invoice, 404);

        $data = $request->validate([
            'email_to' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ]);

        $mailConfig = $this->legacyMailConfig();
        $previewLink = route('invoice.invoices.show', $id);
        $body = trim((string) ($data['body'] ?? ''));
        $fullBody = $body . "\n\nView invoice: <a href='" . $previewLink . "'>" . $previewLink . '</a>';

        $autoload = base_path('neuralinvoice/vendor/autoload.php');
        if (is_file($autoload)) {
            require_once $autoload;
        }

        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            return back()->withInput()->with('error', 'PHPMailer library was not found.');
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->AuthType = 'LOGIN';
            $mail->Username = $mailConfig['username'];
            $mail->Password = $mailConfig['password'];
            $mail->SMTPSecure = ($mailConfig['encryption'] ?? 'tls') === 'tls'
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
                : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int) $mailConfig['port'];
            $mail->CharSet = 'UTF-8';
            if (!empty($mailConfig['debug'])) {
                $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $mail->Debugoutput = function ($debugLine, $level) {
                    error_log('PHPMailer SMTP debug [' . $level . ']: ' . $debugLine);
                };
            }

            $mail->setFrom($mailConfig['from_address'], $mailConfig['from_name']);
            $mail->addAddress($data['email_to']);
            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body = nl2br($fullBody);
            $mail->AltBody = strip_tags($fullBody);
            $mail->send();

            $payload = [];
            if (Schema::hasColumn('invoice', 'sent_at')) {
                $payload['sent_at'] = now();
            }
            if (!empty($payload)) {
                DB::table('invoice')->where('id', $id)->update($payload);
            }

            return redirect()->route('invoice.invoices.send', $id)
                ->with('success', 'Invoice link sent to ' . $data['email_to']);
        } catch (\Throwable $e) {
            $message = 'Mail could not be sent. Mailer Error: ' . ($mail->ErrorInfo ?: $e->getMessage());
            if (stripos($message, 'authenticate') !== false) {
                $message .= ' SMTP authentication failed. Check MAIL_USERNAME and MAIL_PASSWORD. For Gmail, use a valid App Password for the same account.';
            }

            return back()->withInput()->with('error', $message);
        }
    }

    public function markInvoiceSent(Request $request, int $id)
    {
        $invoice = DB::table('invoice')->where('id', $id)->first();

        if (!$invoice || (int) $request->input('invoice_id') !== $id) {
            return response()->json(['success' => false, 'message' => 'Invalid invoice.'], 404);
        }

        if ((int) $invoice->status === 3) {
            return response()->json(['success' => false, 'message' => 'Cancelled invoice cannot be downloaded.']);
        }

        $payload = ['status' => max(1, (int) $invoice->status)];
        if (Schema::hasColumn('invoice', 'sent_at') && empty($invoice->sent_at)) {
            $payload['sent_at'] = now();
        }

        DB::table('invoice')->where('id', $id)->update($payload);

        return response()->json(['success' => true, 'message' => 'Invoice is ready to download.']);
    }

    public function payments(Request $request)
    {
        $clientId = max(0, (int) $request->input('clientId', 0));
        $clients = $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1');
        $paymentInvoices = $this->table('invoice') ? $this->paymentInvoicesQuery($clientId)->get() : collect();

        return view('invoice.payments', [
            'title' => 'Received Payments',
            'clientId' => $clientId,
            'clients' => $clients,
            'paymentInvoices' => $paymentInvoices,
        ]);
    }

    public function paymentsList(Request $request)
    {
        if (!$this->table('recived_payment')) {
            return $this->emptyDataTable($request);
        }

        $query = DB::table('recived_payment as p')
            ->leftJoin('clientmaster as c', 'p.ClientID', '=', 'c.ClientID')
            ->leftJoin('workamount as w', 'p.workAmount_invoice_id', '=', 'w.invoice_id')
            ->select('p.*', 'c.ClientName', 'w.invoiceNumber')
            ->orderByDesc('p.id');

        return $this->dataTableResponse(
            $request,
            $query,
            ['p.id', 'w.invoiceNumber', 'p.taxinvoiceNumber', 'c.ClientName', 'p.recived_amount', 'p.received_net', 'p.received_date'],
            ['p.id', 'w.invoiceNumber', 'c.ClientName', 'p.recived_amount', 'p.tds_amount', 'p.received_net', 'p.received_date', null],
            function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_no' => e($payment->invoiceNumber ?? $payment->taxinvoiceNumber ?? '-'),
                    'client' => e($payment->ClientName ?? '-'),
                    'received' => 'Rs. ' . number_format((float) ($payment->recived_amount ?? 0), 2),
                    'tds' => 'Rs. ' . number_format((float) ($payment->tds_amount ?? 0), 2),
                    'net' => 'Rs. ' . number_format((float) ($payment->received_net ?? 0), 2),
                    'date' => e($payment->received_date),
                    'action' => $this->iconButton(route('invoice.payments.edit', $payment->id), 'ti ti-edit', 'Edit'),
                ];
            }
        );
    }

    public function createPayment()
    {
        return view('invoice.payments-form', [
            'title' => 'Create Payment',
            'payment' => null,
            'invoices' => $this->invoiceQuery()->get(),
        ]);
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'workAmount_invoice_id' => ['required', 'integer'],
            'recived_amount' => ['required', 'numeric', 'min:0'],
            'tds_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_note' => ['nullable', 'string', 'max:255'],
            'received_date' => ['nullable', 'date'],
        ]);

        $invoice = DB::table('invoice')->where('id', $data['workAmount_invoice_id'])->first();
        abort_unless($invoice, 404);

        $tds = (float) ($data['tds_amount'] ?? 0);
        $receivedAmount = round((float) $data['recived_amount'], 2);
        $paymentCredit = round($receivedAmount + $tds, 2);
        if ($paymentCredit <= 0) {
            return back()->withInput()->with('error', 'Enter received amount or TDS amount.');
        }

        if ((int) $invoice->status === 3) {
            return back()->withInput()->with('error', 'Payment cannot be received for a cancelled invoice.');
        }

        $paidBefore = $this->invoicePaidAmount((int) $invoice->id);
        $pendingBefore = max(0, round((float) $invoice->totalAmount - $paidBefore, 2));
        if ($paymentCredit > $pendingBefore + 0.01) {
            return back()->withInput()->with('error', 'Payment is greater than the pending amount. Please check the amount.');
        }

        $invoiceNumber = DB::table('workamount')->where('invoice_id', $invoice->id)->value('invoiceNumber') ?? '';
        $receivedDate = $data['received_date'] ?? now()->toDateString();
        $existingPayment = DB::table('recived_payment')->where('workAmount_invoice_id', $invoice->id)->first();

        DB::transaction(function () use ($existingPayment, $invoice, $invoiceNumber, $receivedAmount, $tds, $paymentCredit, $data, $receivedDate) {
            if ($existingPayment) {
                $currentReceived = (float) ($existingPayment->recived_amount ?? 0);
                $currentTds = (float) ($existingPayment->tds_amount ?? 0);
                $currentCredit = (float) ($existingPayment->received_net ?? 0);
                if ($currentCredit <= 0 && $currentReceived > 0) {
                    $currentCredit = $currentReceived;
                }
                $note = trim((string) ($existingPayment->payment_note ?? '') . ' | ' . (string) ($data['payment_note'] ?? ''), ' | ');

                DB::table('recived_payment')->where('id', $existingPayment->id)->update([
                    'recived_amount' => round($currentReceived + $receivedAmount, 2),
                    'tds_amount' => round($currentTds + $tds, 2),
                    'received_net' => round($currentCredit + $paymentCredit, 2),
                    'payment_note' => $note,
                    'received_date' => $receivedDate,
                    'status' => 1,
                ]);
            } else {
                DB::table('recived_payment')->insert([
                    'ClientID' => $invoice->ClientID,
                    'workAmount_invoice_id' => $invoice->id,
                    'taxinvoiceNumber' => $invoiceNumber,
                    'recived_amount' => $receivedAmount,
                    'tds_amount' => $tds,
                    'received_net' => $paymentCredit,
                    'payment_note' => $data['payment_note'] ?? null,
                    'received_date' => $receivedDate,
                    'status' => 1,
                    'created_at' => now()->toDateString(),
                ]);
            }

            $paid = $this->invoicePaidAmount((int) $invoice->id);

            DB::table('invoice')->where('id', $invoice->id)->update([
                'status' => $paid + 0.01 >= (float) $invoice->totalAmount ? 2 : 1,
            ]);
        });

        return redirect()->route('invoice.payments')->with('success', 'Payment saved successfully.');
    }

    public function cancelInvoice(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => ['required', 'integer'],
            'cancel_reason' => ['required', 'string', 'max:255'],
        ]);

        $invoice = DB::table('invoice')->where('id', $data['invoice_id'])->first();
        if (!$invoice) {
            return back()->with('error', 'Invoice was not found. Please refresh the invoice list and try again.');
        }

        if ((int) $invoice->status === 3) {
            return back()->with('success', 'Invoice is already cancelled.');
        }

        if ($this->invoicePaidAmount((int) $invoice->id) > 0.01) {
            return back()->with('error', 'A paid invoice cannot be cancelled.');
        }

        DB::transaction(function () use ($invoice, $data) {
            $payload = ['status' => 3];
            if (Schema::hasColumn('invoice', 'cancelled_at')) {
                $payload['cancelled_at'] = now();
            }
            if (Schema::hasColumn('invoice', 'cancel_reason')) {
                $payload['cancel_reason'] = $data['cancel_reason'];
            }

            DB::table('invoice')->where('id', $invoice->id)->update($payload);

            $milestoneId = (int) ($invoice->milestone_id ?? 0);
            if ($milestoneId > 0 && $this->table('project_payment_milestones') && Schema::hasColumn('project_payment_milestones', 'invoiced_amount')) {
                DB::table('project_payment_milestones')
                    ->where('milestone_id', $milestoneId)
                    ->update([
                        'invoiced_amount' => DB::raw('GREATEST(0, invoiced_amount - ' . (float) ($invoice->finalAmount ?? 0) . ')'),
                    ]);
            }
        });

        return back()->with('success', 'Invoice cancelled successfully.');
    }

    public function editPayment(int $id)
    {
        $payment = DB::table('recived_payment')->where('id', $id)->first();
        abort_unless($payment, 404);

        return view('invoice.payments-form', [
            'title' => 'Edit Payment',
            'payment' => $payment,
            'invoices' => $this->invoiceQuery()->get(),
        ]);
    }

    public function updatePayment(Request $request, int $id)
    {
        $data = $request->validate([
            'workAmount_invoice_id' => ['required', 'integer'],
            'recived_amount' => ['required', 'numeric', 'min:0'],
            'tds_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_note' => ['nullable', 'string', 'max:255'],
            'received_date' => ['nullable', 'date'],
            'status' => ['required', 'integer'],
        ]);

        $invoice = DB::table('invoice')->where('id', $data['workAmount_invoice_id'])->first();
        abort_unless($invoice, 404);

        $tds = (float) ($data['tds_amount'] ?? 0);
        $net = max(0, (float) $data['recived_amount'] - $tds);
        $invoiceNumber = DB::table('workamount')->where('invoice_id', $invoice->id)->value('invoiceNumber') ?? '';

        DB::table('recived_payment')->where('id', $id)->update([
            'ClientID' => $invoice->ClientID,
            'workAmount_invoice_id' => $invoice->id,
            'taxinvoiceNumber' => $invoiceNumber,
            'recived_amount' => $data['recived_amount'],
            'tds_amount' => $tds,
            'received_net' => $net,
            'payment_note' => $data['payment_note'] ?? null,
            'received_date' => $data['received_date'] ?? now(),
            'status' => $data['status'],
        ]);

        $this->syncInvoicePaymentStatus($invoice->id);

        return redirect()->route('invoice.payments')->with('success', 'Payment updated successfully.');
    }

    public function ledger(Request $request)
    {
        $clientId = max(0, (int) $request->input('client_id', 0));
        $fromDate = $request->input('fromDate') ?: now()->subYear()->toDateString();
        $toDate = $request->input('toDate') ?: now()->toDateString();

        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $clients = $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1');
        $ledger = collect();
        $summary = [
            'opening' => 0.0,
            'invoiced' => 0.0,
            'received' => 0.0,
            'tds' => 0.0,
            'outstanding' => 0.0,
            'closing' => 0.0,
        ];

        if ($clientId > 0) {
            $openingBalance = 0.0;
            $hasInvoiceTable = $this->table('invoice');

            if ($hasInvoiceTable) {
                $invoiceDateColumn = Schema::hasColumn('invoice', 'invoiceDate') ? 'invoiceDate' : 'creation_date';

                $openingInvoiced = (float) DB::table('invoice as i')
                    ->where('i.ClientID', $clientId)
                    ->where('i.status', '<>', 3)
                    ->whereDate('i.' . $invoiceDateColumn, '<', $fromDate)
                    ->sum('i.totalAmount');
                $openingBalance += $openingInvoiced;

                $invoiceRows = DB::table('invoice as i')
                    ->where('i.ClientID', $clientId)
                    ->where('i.status', '<>', 3)
                    ->whereDate('i.' . $invoiceDateColumn, '>=', $fromDate)
                    ->whereDate('i.' . $invoiceDateColumn, '<=', $toDate)
                    ->select(
                        'i.id',
                        'i.' . $invoiceDateColumn . ' as statement_date',
                        'i.totalAmount',
                        'i.status',
                        DB::raw('(SELECT invoiceNumber FROM workamount WHERE workamount.invoice_id = i.id LIMIT 1) as invoiceNumber')
                    )
                    ->orderBy('i.' . $invoiceDateColumn)
                    ->get();

                foreach ($invoiceRows as $row) {
                    $amount = (float) ($row->totalAmount ?? 0);
                    $summary['invoiced'] += $amount;
                    $ledger->push([
                        'date' => $row->statement_date ?: $fromDate,
                        'reference' => $row->invoiceNumber ?: 'Invoice #' . $row->id,
                        'particulars' => 'Invoice raised',
                        'debit' => $amount,
                        'credit' => 0.0,
                        'tds' => 0.0,
                        'status' => $this->ledgerStatusLabel((int) $row->status),
                        'mode' => '-',
                        'sort' => 1,
                    ]);
                }
            }

            if ($hasInvoiceTable && $this->table('recived_payment')) {
                $paymentDateColumn = Schema::hasColumn('recived_payment', 'received_date') ? 'received_date' : 'created_at';

                $openingPayments = DB::table('recived_payment as rp')
                    ->leftJoin('invoice as i', 'rp.workAmount_invoice_id', '=', 'i.id')
                    ->where('rp.ClientID', $clientId)
                    ->where('rp.status', 1)
                    ->where(function ($query) {
                        $query->whereNull('i.status')->orWhere('i.status', '<>', 3);
                    })
                    ->whereDate('rp.' . $paymentDateColumn, '<', $fromDate)
                    ->selectRaw('SUM(CASE WHEN COALESCE(rp.received_net, 0) > 0 THEN rp.received_net ELSE rp.recived_amount END) as received_total')
                    ->first();
                $openingBalance -= (float) ($openingPayments->received_total ?? 0);

                $paymentRows = DB::table('recived_payment as rp')
                    ->leftJoin('invoice as i', 'rp.workAmount_invoice_id', '=', 'i.id')
                    ->where('rp.ClientID', $clientId)
                    ->where('rp.status', 1)
                    ->where(function ($query) {
                        $query->whereNull('i.status')->orWhere('i.status', '<>', 3);
                    })
                    ->whereDate('rp.' . $paymentDateColumn, '>=', $fromDate)
                    ->whereDate('rp.' . $paymentDateColumn, '<=', $toDate)
                    ->select(
                        'rp.*',
                        DB::raw('(SELECT invoiceNumber FROM workamount WHERE workamount.invoice_id = rp.workAmount_invoice_id LIMIT 1) as invoiceNumber')
                    )
                    ->orderBy('rp.' . $paymentDateColumn)
                    ->get();

                foreach ($paymentRows as $row) {
                    $tds = (float) ($row->tds_amount ?? 0);
                    $credit = (float) (($row->received_net ?? 0) > 0 ? $row->received_net : $row->recived_amount);
                    $summary['received'] += $credit;
                    $summary['tds'] += $tds;
                    $ledger->push([
                        'date' => $row->{$paymentDateColumn} ?: $row->created_at,
                        'reference' => $row->taxinvoiceNumber ?: ($row->invoiceNumber ?: 'Payment #' . $row->id),
                        'debit' => 0.0,
                        'credit' => $credit,
                        'tds' => $tds,
                        'status' => 'Received',
                        'particulars' => trim((string) ($row->payment_note ?? '')) !== '' ? 'Payment received - ' . $row->payment_note : 'Payment received',
                        'mode' => '-',
                        'sort' => 2,
                    ]);
                }
            }

            $summary['opening'] = $openingBalance;
            $balance = $openingBalance;
            $ledger = $ledger
                ->sortBy([
                    ['date', 'asc'],
                    ['sort', 'asc'],
                    ['reference', 'asc'],
                ])
                ->values()
                ->map(function ($line) use (&$balance) {
                    $balance += (float) $line['debit'] - (float) $line['credit'];
                    $line['balance'] = $balance;

                    return $line;
                });

            $summary['closing'] = $balance;
            $summary['outstanding'] = $balance;
        }

        return view('invoice.ledger', [
            'title' => 'Client Ledger',
            'clients' => $clients,
            'clientId' => $clientId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'ledger' => $ledger,
            'summary' => $summary,
        ]);
    }

    public function expenses()
    {
        return view('invoice.expenses', [
            'title' => 'Expenses',
        ]);
    }

    public function expensesList(Request $request)
    {
        if (!$this->table('expenses')) {
            return $this->emptyDataTable($request);
        }

        $hasClientColumn = Schema::hasColumn('expenses', 'ClientID');
        $query = DB::table('expenses as e')
            ->leftJoin('expense_categories as c', 'e.expense_category_id', '=', 'c.id')
            ->orderByDesc('e.id');

        if ($hasClientColumn) {
            $query->leftJoin('clientmaster as cm', 'e.ClientID', '=', 'cm.ClientID')
                ->select('e.*', 'c.categories', 'cm.ClientName');
        } else {
            $query->select('e.*', 'c.categories', DB::raw('NULL as ClientName'));
        }

        $searchColumns = ['e.id', 'c.categories', 'e.expense_name', 'e.expense_cost', 'e.expense_date', 'e.expense_mode'];
        $orderColumns = ['e.id', 'c.categories', null, 'e.expense_name', 'e.expense_cost', 'e.expense_date', 'e.expense_mode', null];
        if ($hasClientColumn) {
            array_splice($searchColumns, 2, 0, ['cm.ClientName']);
            $orderColumns[2] = 'cm.ClientName';
        }

        return $this->dataTableResponse(
            $request,
            $query,
            $searchColumns,
            $orderColumns,
            function ($expense) {
                return [
                    'id' => $expense->id,
                    'category' => e($expense->categories ?? '-'),
                    'client' => e($expense->ClientName ?? '-'),
                    'name' => e($expense->expense_name),
                    'cost' => 'Rs. ' . number_format((float) ($expense->expense_cost ?? 0), 2),
                    'date' => e($expense->expense_date),
                    'mode' => e($expense->expense_mode),
                    'action' => $this->iconButton(route('invoice.expenses.edit', $expense->id), 'ti ti-edit', 'Edit'),
                ];
            }
        );
    }

    public function createExpense()
    {
        return view('invoice.expenses-form', [
            'title' => 'Create Expense',
            'expense' => null,
            'categories' => $this->options('expense_categories', 'id', 'categories', 'status = 1'),
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
        ]);
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => ['nullable', 'integer'],
            'ClientID' => ['nullable', 'integer'],
            'category_name' => ['nullable', 'string', 'max:50'],
            'expense_name' => ['required', 'string', 'max:255'],
            'expense_cost' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'expense_mode' => ['required', 'in:Cash,Online'],
            'expense_invoice' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($data['expense_category_id']) && !empty($data['category_name'])) {
            $data['expense_category_id'] = DB::table('expense_categories')->insertGetId([
                'categories' => $data['category_name'],
                'status' => 1,
                'created_at' => now()->toDateString(),
                'updated_at' => now()->toDateString(),
            ]);
        }

        if (empty($data['expense_category_id'])) {
            return back()->withInput()->with('error', 'Please select or add an expense category.');
        }

        unset($data['category_name']);
        $data['ClientID'] = !empty($data['ClientID']) ? (int) $data['ClientID'] : null;

        DB::table('expenses')->insert($this->tablePayload('expenses', array_merge($data, [
            'expense_status' => 1,
            'created_at' => now(),
        ])));

        return redirect()->route('invoice.expenses')->with('success', 'Expense saved successfully.');
    }

    public function editExpense(int $id)
    {
        $expense = DB::table('expenses')->where('id', $id)->first();
        abort_unless($expense, 404);

        return view('invoice.expenses-form', [
            'title' => 'Edit Expense',
            'expense' => $expense,
            'categories' => $this->options('expense_categories', 'id', 'categories', 'status = 1'),
            'clients' => $this->options('clientmaster', 'ClientID', 'ClientName', 'DelStatus = 1'),
        ]);
    }

    public function updateExpense(Request $request, int $id)
    {
        $data = $request->validate([
            'expense_category_id' => ['nullable', 'integer'],
            'ClientID' => ['nullable', 'integer'],
            'category_name' => ['nullable', 'string', 'max:50'],
            'expense_name' => ['required', 'string', 'max:255'],
            'expense_cost' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'expense_mode' => ['required', 'in:Cash,Online'],
            'expense_invoice' => ['nullable', 'string', 'max:255'],
            'expense_status' => ['required', 'integer'],
        ]);

        if (empty($data['expense_category_id']) && !empty($data['category_name'])) {
            $data['expense_category_id'] = DB::table('expense_categories')->insertGetId([
                'categories' => $data['category_name'],
                'status' => 1,
                'created_at' => now()->toDateString(),
                'updated_at' => now()->toDateString(),
            ]);
        }

        if (empty($data['expense_category_id'])) {
            return back()->withInput()->with('error', 'Please select or add an expense category.');
        }

        unset($data['category_name']);
        $data['ClientID'] = !empty($data['ClientID']) ? (int) $data['ClientID'] : null;

        DB::table('expenses')->where('id', $id)->update($this->tablePayload('expenses', $data));

        return redirect()->route('invoice.expenses')->with('success', 'Expense updated successfully.');
    }

    private function dataTableResponse(Request $request, $query, array $searchColumns, array $orderColumns, callable $mapper)
    {
        $totalRecords = (clone $query)->count();
        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    if (str_starts_with($column, 'raw:')) {
                        $q->orWhereRaw(substr($column, 4) . ' like ?', ["%{$search}%"]);
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        $filteredRecords = (clone $query)->count();
        $orderColumnIndex = (int) $request->input('order.0.column', -1);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (array_key_exists($orderColumnIndex, $orderColumns) && $orderColumns[$orderColumnIndex]) {
            $query->reorder()->orderBy($orderColumns[$orderColumnIndex], $orderDirection);
        }

        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        if ($length !== -1) {
            $query->skip($start)->take(max(1, $length));
        }

        $rows = $query->get()
            ->map(fn($row, $index) => $mapper($row, $start, $index))
            ->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows,
        ]);
    }

    private function emptyDataTable(Request $request)
    {
        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ]);
    }

    private function activeBadge(bool $active): string
    {
        return '<span class="badge bg-' . ($active ? 'success' : 'danger') . '">' . ($active ? 'Active' : 'Inactive') . '</span>';
    }

    private function invoiceStatusBadge(int $status): string
    {
        $statusMap = [
            0 => ['Draft', 'secondary'],
            1 => ['Pending', 'warning'],
            2 => ['Paid', 'success'],
            3 => ['Cancelled', 'danger'],
        ];
        [$label, $color] = $statusMap[$status] ?? ['Unknown', 'secondary'];

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    private function ledgerStatusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Draft',
            1 => 'Pending',
            2 => 'Paid',
            3 => 'Cancelled',
            default => 'Unknown',
        };
    }

    private function iconButton(string $url, string $icon, string $title, string $class = 'btn-outline-primary'): string
    {
        return '<a  href="' . e($url) . '" class="btn btn-sm ' . $class . '" title="' . e($title) . '"><i class="' . e($icon) . '"></i></a>';
    }

    private function sentAtLabel($sentAt): string
    {
        if (empty($sentAt)) {
            return '';
        }

        return '<span class="small text-success ms-2 align-self-center"><i class="ti ti-check"></i> Sent: '
            . e(\Illuminate\Support\Carbon::parse($sentAt)->format('d M, H:i')) . '</span>';
    }

    private function invoiceSendRow(int $id)
    {
        if (!$this->table('invoice')) {
            return null;
        }

        return DB::table('invoice as i')
            ->join('clientmaster as cm', 'i.ClientID', '=', 'cm.ClientID')
            ->leftJoin('companymaster as co', 'cm.CompanyID', '=', 'co.CompanyID')
            ->leftJoin('workamount as w', 'i.id', '=', 'w.invoice_id')
            ->where('i.id', $id)
            ->select(
                'i.*',
                'cm.ClientName',
                'cm.EmailAddress',
                'w.invoiceNumber',
                'co.CompanyName',
                'co.EmailAddress as CompanyEmail'
            )
            ->first();
    }

    private function legacyMailConfig(): array
    {
        $config = $this->appMailConfig();
        if (!empty($config['username']) && !empty($config['password'])) {
            return $config;
        }

        $helper = base_path('neuralinvoice/includes/smtp-mailer.php');
        if (is_file($helper)) {
            require_once $helper;
        }

        if (function_exists('ni_mail_config')) {
            return ni_mail_config();
        }

        return array_merge($config, [
            'host' => config('mail.mailers.smtp.host', 'smtp.gmail.com'),
            'port' => (int) config('mail.mailers.smtp.port', 587),
            'username' => (string) config('mail.mailers.smtp.username', ''),
            'password' => $this->cleanMailSecret((string) config('mail.mailers.smtp.password', '')),
            'encryption' => strtolower((string) config('mail.mailers.smtp.encryption', 'tls')),
            'from_address' => (string) config('mail.from.address', 'billing@neuralinformatics.com'),
            'from_name' => (string) config('mail.from.name', 'Neural Invoice'),
            'debug' => false,
        ]);
    }

    private function appMailConfig(): array
    {
        $username = (string) config('mail.mailers.smtp.username', env('MAIL_USERNAME', ''));
        $password = $this->cleanMailSecret((string) config('mail.mailers.smtp.password', env('MAIL_PASSWORD', '')));
        $fromAddress = (string) config('mail.from.address', env('MAIL_FROM_ADDRESS', $username));

        return [
            'mailer' => (string) config('mail.default', env('MAIL_MAILER', 'smtp')),
            'host' => (string) config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com')),
            'port' => (int) config('mail.mailers.smtp.port', env('MAIL_PORT', 587)),
            'username' => $username,
            'password' => $password,
            'encryption' => strtolower((string) config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls'))),
            'from_address' => $fromAddress ?: $username,
            'from_name' => (string) config('mail.from.name', env('MAIL_FROM_NAME', 'Neural Invoice')),
            'debug' => filter_var(env('MAIL_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private function cleanMailSecret(string $value): string
    {
        $value = trim($value);
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        return preg_replace('/\s+/', '', $value);
    }

    private function invoiceQuery()
    {
        if (!$this->table('invoice')) {
            return collect();
        }

        return DB::table('invoice as i')
            ->leftJoin('clientmaster as c', 'i.ClientID', '=', 'c.ClientID')
            ->leftJoin('companymaster as co', 'i.CompanyID', '=', 'co.CompanyID')
            ->select(
                'i.*',
                'c.ClientName',
                'co.CompanyName',
                DB::raw('(SELECT invoiceNumber FROM workamount WHERE workamount.invoice_id = i.id LIMIT 1) as invoiceNumber'),
                DB::raw('(SELECT COALESCE(SUM(received_net), 0) FROM recived_payment WHERE recived_payment.workAmount_invoice_id = i.id AND recived_payment.status = 1) as paidAmount')
            )
            ->orderByDesc('i.id');
    }

    private function paymentInvoicesQuery(int $clientId = 0)
    {
        $query = DB::table('invoice as i')
            ->leftJoin('clientmaster as cm', 'i.ClientID', '=', 'cm.ClientID')
            ->leftJoin('countrymaster as conm', 'cm.Country', '=', 'conm.CountryID')
            ->leftJoin('statemaster as sm', 'cm.StateID', '=', 'sm.StateID')
            ->leftJoin(DB::raw('(
                SELECT workAmount_invoice_id,
                       SUM(recived_amount) AS received_amount_total,
                       SUM(tds_amount) AS tds_amount_total,
                       SUM(CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END) AS paid_total
                FROM recived_payment
                WHERE status = 1
                GROUP BY workAmount_invoice_id
            ) pay'), 'pay.workAmount_invoice_id', '=', 'i.id')
            ->whereIn('i.status', [1, 2])
            ->select(
                DB::raw('(SELECT invoiceNumber FROM workamount WHERE workamount.invoice_id = i.id LIMIT 1) as invoiceNumber'),
                'cm.pinNo',
                'cm.ClientName',
                'conm.CountryName',
                'sm.StateName',
                'i.id',
                'i.invoiceDate',
                'i.concern',
                'i.mobile',
                'i.deliverydate',
                'i.duedate',
                'i.duration_days',
                'i.ClientID',
                'i.sacCode',
                'i.subtotal',
                'i.discount',
                'i.finalAmount',
                'i.sgstAmount',
                'i.cgstAmount',
                'i.igstAmount',
                'i.bankChargePercent',
                'i.bankChargeAmount',
                'i.totalAmount',
                'i.status',
                DB::raw('COALESCE(pay.received_amount_total, 0) AS received_amount_total'),
                DB::raw('COALESCE(pay.tds_amount_total, 0) AS tds_amount_total'),
                DB::raw('COALESCE(pay.paid_total, 0) AS paid_total')
            )
            ->orderByDesc('i.id');

        if ($clientId > 0) {
            $query->where('i.ClientID', $clientId);
        }

        return $query;
    }

    private function invoicePaidAmount(int $invoiceId): float
    {
        if (!$this->table('recived_payment')) {
            return 0.0;
        }

        return (float) DB::table('recived_payment')
            ->where('workAmount_invoice_id', $invoiceId)
            ->where('status', 1)
            ->sum(DB::raw('CASE WHEN received_net > 0 THEN received_net ELSE recived_amount END'));
    }

    private function projectDetailQuery()
    {
        $query = DB::table('client_projects as p')
            ->join('clientmaster as c', 'p.client_id', '=', 'c.ClientID')
            ->select('p.*', 'c.ClientName');

        if ($this->table('work_type_master')) {
            $query->leftJoin('work_type_master as wtm', 'p.work_type_id', '=', 'wtm.WorkTypeID')
                ->addSelect(DB::raw('COALESCE(wtm.WorkTypeName, p.work_type) AS work_type_display'));
        } else {
            $query->addSelect(DB::raw('p.work_type AS work_type_display'));
        }

        return $query;
    }

    private function employeeOptionsForMapping()
    {
        if (!$this->table('employees')) {
            return collect();
        }

        $query = DB::table('employees as e')
            ->select(
                'e.employee_id',
                'e.employee_name',
                'e.employee_email',
                'e.employee_mobile',
                'e.employment_type',
                'e.status'
            );

        if ($this->table('designations')) {
            $query->leftJoin('designations as d', 'e.designation_id', '=', 'd.id')
                ->addSelect(DB::raw('d.name as designation_name'));
        } else {
            $query->addSelect(DB::raw("'' as designation_name"));
        }

        if (Schema::hasColumn('employees', 'status')) {
            $query->where('e.status', 'Active');
        }

        return $query->orderBy('e.employee_name')->get();
    }

    private function projectTeamMembers(int $projectId)
    {
        if (!$this->table('project_employee_mappings') || !$this->table('employees')) {
            return collect();
        }

        $query = DB::table('project_employee_mappings as pem')
            ->join('employees as e', 'pem.employee_id', '=', 'e.employee_id')
            ->where('pem.project_id', $projectId)
            ->select(
                'pem.*',
                'e.employee_name',
                'e.employee_email',
                'e.employee_mobile',
                'e.employment_type',
                'e.status as employee_status'
            );

        if ($this->table('designations')) {
            $query->leftJoin('designations as d', 'e.designation_id', '=', 'd.id')
                ->addSelect(DB::raw('d.name as designation_name'));
        } else {
            $query->addSelect(DB::raw("'' as designation_name"));
        }

        return $query->orderBy('e.employee_name')->get();
    }

    private function legacyProjectPayload(Request $request): array
    {
        $clientId = (int) $request->input('client_id', 0);
        $projectName = trim((string) $request->input('project_name', ''));
        $workTypeId = (int) $request->input('work_type_id', 0);
        $workType = $this->workTypeById($workTypeId);
        $engagementType = ($workType->amc_type ?? 'project') === 'amc_only' ? 'amc_only' : 'project';
        $paymentMode = $engagementType === 'amc_only' ? 'percentage' : (($request->input('payment_mode') === 'module') ? 'module' : 'percentage');
        $totalValue = round((float) $request->input('total_value', 0), 2);
        $hasAmc = $request->input('has_amc') === 'yes' ? 'yes' : 'no';
        $projectDurationDays = (int) $request->input('project_duration_days', 0);
        $projectStartDate = $request->input('project_start_date') ?: null;
        $projectEndDate = $request->input('project_end_date') ?: null;
        $fyStartYear = (int) $request->input('fy_start_year', $this->currentFyStartYear());
        $fyLabel = $fyStartYear . '-' . substr((string) ($fyStartYear + 1), -2);
        $amcStart = $hasAmc === 'yes' ? ($request->input('amc_start') ?: null) : null;
        $amcEnd = $hasAmc === 'yes' ? ($request->input('amc_end') ?: null) : null;
        $amcWorkingDays = $hasAmc === 'yes' ? (int) $request->input('amc_working_days', 0) : 0;
        $error = '';

        if ($clientId <= 0) {
            $error = 'Please select a client.';
        } elseif ($projectName === '') {
            $error = $engagementType === 'amc_only' ? 'AMC title / description is required.' : 'Project name is required.';
        } elseif ($workTypeId <= 0 || !$workType) {
            $error = 'Please select work type.';
        } elseif ($totalValue <= 0) {
            $error = $engagementType === 'amc_only' ? 'Enter AMC annual fee amount.' : 'Total project value is required.';
        } elseif ($projectDurationDays <= 0) {
            $error = 'Project duration is required.';
        } elseif (!$projectStartDate || !$projectEndDate) {
            $error = 'Project start and end dates are required.';
        }

        $tax = $this->projectTaxForClient($clientId, $totalValue);
        $totalWithTax = round($totalValue + $tax['tax_amount'], 2);
        $proposalPath = $this->storeProposalFile($request);
        $data = [
            'client_id' => $clientId,
            'project_name' => $projectName,
            'engagement_type' => $engagementType,
            'has_amc' => $hasAmc,
            'payment_mode' => $paymentMode,
            'total_value' => $totalValue,
            'tax_percent' => $tax['tax_percent'],
            'tax_amount' => $tax['tax_amount'],
            'cgst_percent' => $tax['cgst_percent'],
            'cgst_amount' => $tax['cgst_amount'],
            'sgst_percent' => $tax['sgst_percent'],
            'sgst_amount' => $tax['sgst_amount'],
            'igst_percent' => $tax['igst_percent'],
            'igst_amount' => $tax['igst_amount'],
            'tax_type' => $tax['tax_type'],
            'total_value_with_tax' => $totalWithTax,
            'total_with_tax' => $totalWithTax,
            'final_amount' => $totalWithTax,
            'amc_start' => $amcStart,
            'amc_end' => $amcEnd,
            'project_start_date' => $projectStartDate,
            'project_end_date' => $projectEndDate,
            'financial_year' => $fyLabel,
            'amc_working_days' => $amcWorkingDays,
            'project_duration_days' => $projectDurationDays,
            'duration_mode' => 'working',
            'work_type' => (string) ($workType->WorkTypeName ?? ''),
            'work_type_id' => $workTypeId,
            'notes' => trim((string) $request->input('notes', '')),
            'status' => 1,
        ];
        if ($proposalPath !== null) {
            $data['proposal_file'] = $proposalPath;
        }

        $milestones = $this->projectMilestonePayload($request, $engagementType, $paymentMode, $totalValue, $tax);
        if ($error === '' && $engagementType === 'project' && empty($milestones)) {
            $error = 'Add at least one payment milestone.';
        }

        return [$data, $milestones, $error];
    }

    private function projectMilestonePayload(Request $request, string $engagementType, string $paymentMode, float $totalValue, array $tax): array
    {
        if ($engagementType === 'amc_only' && in_array($request->input('amc_split'), ['single', 'half_yearly', 'quarterly'], true)) {
            $dueDate = $request->input('amc_fee_due_date') ?: null;
            $parts = match ($request->input('amc_split')) {
                'half_yearly' => [['AMC 1st Half', 50], ['AMC 2nd Half', 50]],
                'quarterly' => [['AMC Q1', 25], ['AMC Q2', 25], ['AMC Q3', 25], ['AMC Q4', 25]],
                default => [['Annual AMC Fee', 100]],
            };

            return array_map(function ($part) use ($totalValue, $dueDate, $tax) {
                $amount = round($totalValue * $part[1] / 100, 2);
                return $this->milestoneRow($part[0], '', $part[1], $amount, $dueDate, $tax);
            }, $parts);
        }

        $labels = $request->input('milestone_label', []);
        $modules = $request->input('module_name', []);
        $percentages = $request->input('percentage', []);
        $amounts = $request->input('milestone_amount', []);
        $dates = $request->input('due_date', []);
        $rows = [];

        foreach ((array) $labels as $index => $label) {
            $label = trim((string) $label);
            $amount = round((float) ($amounts[$index] ?? 0), 2);
            if ($label === '' && $amount <= 0) {
                continue;
            }
            if ($paymentMode === 'percentage' && $amount <= 0 && $totalValue > 0) {
                $amount = round($totalValue * (float) ($percentages[$index] ?? 0) / 100, 2);
            }
            $rows[] = $this->milestoneRow(
                $label,
                trim((string) ($modules[$index] ?? '')),
                (float) ($percentages[$index] ?? 0),
                $amount,
                $dates[$index] ?? null,
                $tax
            );
        }

        return $rows;
    }

    private function milestoneRow(string $label, string $module, float $percentage, float $amount, ?string $dueDate, array $tax): array
    {
        $taxPercent = (float) $tax['tax_percent'];
        $taxAmount = round($amount * $taxPercent / 100, 2);
        $cgstAmount = round($amount * (float) $tax['cgst_percent'] / 100, 2);
        $sgstAmount = round($amount * (float) $tax['sgst_percent'] / 100, 2);
        $igstAmount = round($amount * (float) $tax['igst_percent'] / 100, 2);

        return [
            'milestone_label' => $label,
            'module_name' => $module !== '' ? $module : null,
            'percentage' => $percentage > 0 ? $percentage : null,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'amount_with_tax' => round($amount + $taxAmount, 2),
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'igst_amount' => $igstAmount,
            'total_with_tax' => round($amount + $taxAmount, 2),
            'due_date' => $dueDate ?: null,
            'is_paid' => 0,
            'paid_amount' => 0,
            'partial_paid' => 0,
            'sort_order' => 0,
        ];
    }

    private function replaceProjectMilestones(int $projectId, array $milestones, array $projectData): void
    {
        if (!$this->table('project_payment_milestones')) {
            return;
        }

        DB::table('project_payment_milestones')->where('project_id', $projectId)->delete();
        foreach ($milestones as $index => $milestone) {
            $milestone['project_id'] = $projectId;
            $milestone['sort_order'] = $index;
            DB::table('project_payment_milestones')->insert($this->tablePayload('project_payment_milestones', $milestone));
        }
    }

    private function projectTaxForClient(int $clientId, float $totalValue): array
    {
        $row = $this->clientLocation($clientId);
        $country = strtolower(trim((string) ($row->CountryName ?? '')));
        $state = strtolower(trim((string) ($row->StateName ?? '')));

        $cgstPercent = 0.0;
        $sgstPercent = 0.0;
        $igstPercent = 0.0;
        $taxType = 'international';
        if (!($country && $country !== 'india')) {
            if ($state === 'uttar pradesh') {
                $cgstPercent = 9.0;
                $sgstPercent = 9.0;
                $taxType = 'intra_state';
            } else {
                $igstPercent = 18.0;
                $taxType = 'inter_state';
            }
        }

        $cgstAmount = round($totalValue * $cgstPercent / 100, 2);
        $sgstAmount = round($totalValue * $sgstPercent / 100, 2);
        $igstAmount = round($totalValue * $igstPercent / 100, 2);

        return [
            'tax_percent' => $cgstPercent + $sgstPercent + $igstPercent,
            'tax_amount' => round($cgstAmount + $sgstAmount + $igstAmount, 2),
            'cgst_percent' => $cgstPercent,
            'cgst_amount' => $cgstAmount,
            'sgst_percent' => $sgstPercent,
            'sgst_amount' => $sgstAmount,
            'igst_percent' => $igstPercent,
            'igst_amount' => $igstAmount,
            'tax_type' => $taxType,
        ];
    }

    private function workTypeById(int $workTypeId)
    {
        if ($workTypeId <= 0 || !$this->table('work_type_master')) {
            return null;
        }

        return DB::table('work_type_master')->where('WorkTypeID', $workTypeId)->first();
    }

    private function workTypeOptions()
    {
        if (!$this->table('work_type_master')) {
            return collect();
        }

        $query = DB::table('work_type_master')->select('WorkTypeID', 'WorkTypeName');
        if (Schema::hasColumn('work_type_master', 'amc_type')) {
            $query->addSelect('amc_type');
        } else {
            $query->addSelect(DB::raw("'project' as amc_type"));
        }
        if (Schema::hasColumn('work_type_master', 'DelStatus')) {
            $query->where('DelStatus', 1);
        }
        if (Schema::hasColumn('work_type_master', 'sort_order')) {
            $query->orderBy('sort_order');
        }

        return $query->orderBy('WorkTypeName')->get();
    }

    private function currentFyStartYear(): int
    {
        return now()->month >= 4 ? now()->year : now()->year - 1;
    }

    private function storeProposalFile(Request $request): ?string
    {
        if (!$request->hasFile('proposal_file') || !$request->file('proposal_file')->isValid()) {
            return null;
        }

        $file = $request->file('proposal_file');
        $allowed = ['pdf', 'doc', 'docx'];
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowed, true)) {
            return null;
        }

        $dir = base_path('neuralinvoice/uploads/proposals');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = uniqid('f_', true) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);

        return 'uploads/proposals/' . $name;
    }

    private function companyPayload(Request $request): array
    {
        foreach (['cin', 'pan', 'gst_number', 'iec_no', 'lut_no', 'tan_no', 'bank1_ifsc', 'bank1_swift', 'bank2_ifsc', 'bank2_swift'] as $key) {
            $request->merge([$key => strtoupper(str_replace(' ', '', trim((string) $request->input($key, ''))))]);
        }

        if ($request->filled('mobile')) {
            $request->merge(['mobile' => preg_replace('/\D+/', '', (string) $request->input('mobile'))]);
        }

        $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'company_code' => ['nullable', 'string', 'max:20'],
            'company_logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'status' => ['required', 'in:Active,Inactive'],
            'email' => ['nullable', 'email', 'max:150'],
            'mobile' => ['nullable', 'regex:/^[0-9]{10,15}$/'],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'string', 'max:150'],
            'cin' => ['nullable', 'regex:/^[A-Z0-9]{10,25}$/'],
            'pan' => ['nullable', 'regex:/^[A-Z0-9]{10,15}$/'],
            'gst_number' => ['nullable', 'regex:/^[A-Z0-9]{10,25}$/'],
            'iec_no' => ['nullable', 'regex:/^[A-Z0-9]{5,30}$/'],
            'lut_no' => ['nullable', 'regex:/^[A-Z0-9]{5,50}$/'],
            'tan_no' => ['nullable', 'regex:/^[A-Z0-9]{5,30}$/'],
            'address' => ['nullable', 'string'],
            'bank1_account_name' => ['nullable', 'string', 'max:150'],
            'bank1_account_no' => ['nullable', 'string', 'max:50'],
            'bank1_bank_name' => ['nullable', 'string', 'max:150'],
            'bank1_ifsc' => ['nullable', 'string', 'max:30'],
            'bank1_micr' => ['nullable', 'string', 'max:30'],
            'bank1_swift' => ['nullable', 'string', 'max:30'],
            'bank1_branch' => ['nullable', 'string', 'max:150'],
            'bank1_branch_code' => ['nullable', 'string', 'max:30'],
            'bank2_account_name' => ['nullable', 'string', 'max:150'],
            'bank2_account_no' => ['nullable', 'string', 'max:50'],
            'bank2_bank_name' => ['nullable', 'string', 'max:150'],
            'bank2_ifsc' => ['nullable', 'string', 'max:30'],
            'bank2_micr' => ['nullable', 'string', 'max:30'],
            'bank2_swift' => ['nullable', 'string', 'max:30'],
            'bank2_branch' => ['nullable', 'string', 'max:150'],
            'bank2_branch_code' => ['nullable', 'string', 'max:30'],
        ]);

        $payload = [
            'CompanyName' => trim((string) $request->input('company_name', '')),
            'CompanyCode' => trim((string) $request->input('company_code', '')),
            'DelStatus' => $request->input('status') === 'Active' ? 1 : 0,
            'EmailAddress' => trim((string) $request->input('email', '')),
            'MobileNo' => trim((string) $request->input('mobile', '')),
            'PhoneNo' => trim((string) $request->input('phone', '')),
            'Website' => trim((string) $request->input('website', '')),
            'Address' => trim((string) $request->input('address', '')),
            'RegistrationNo' => trim((string) $request->input('cin', '')),
            'PanNo' => trim((string) $request->input('pan', '')),
            'GSTNumber' => trim((string) $request->input('gst_number', '')),
            'IECNo' => trim((string) $request->input('iec_no', '')),
            'LUTNo' => trim((string) $request->input('lut_no', '')),
            'TANNo' => trim((string) $request->input('tan_no', '')),
            'Bank1AccountName' => trim((string) $request->input('bank1_account_name', '')),
            'Bank1AccountNo' => trim((string) $request->input('bank1_account_no', '')),
            'Bank1BankName' => trim((string) $request->input('bank1_bank_name', '')),
            'Bank1IFSC' => trim((string) $request->input('bank1_ifsc', '')),
            'Bank1MICR' => trim((string) $request->input('bank1_micr', '')),
            'Bank1SwiftCode' => trim((string) $request->input('bank1_swift', '')),
            'Bank1Branch' => trim((string) $request->input('bank1_branch', '')),
            'Bank1BranchCode' => trim((string) $request->input('bank1_branch_code', '')),
            'Bank2AccountName' => trim((string) $request->input('bank2_account_name', '')),
            'Bank2AccountNo' => trim((string) $request->input('bank2_account_no', '')),
            'Bank2BankName' => trim((string) $request->input('bank2_bank_name', '')),
            'Bank2IFSC' => trim((string) $request->input('bank2_ifsc', '')),
            'Bank2MICR' => trim((string) $request->input('bank2_micr', '')),
            'Bank2SwiftCode' => trim((string) $request->input('bank2_swift', '')),
            'Bank2Branch' => trim((string) $request->input('bank2_branch', '')),
            'Bank2BranchCode' => trim((string) $request->input('bank2_branch_code', '')),
        ];

        $logo = $this->storeCompanyLogo($request);
        if ($logo !== null) {
            $payload['CompanyLogo'] = $logo;
        }

        return $payload;
    }

    private function storeCompanyLogo(Request $request): ?string
    {
        if (!$request->hasFile('company_logo') || !$request->file('company_logo')->isValid()) {
            return null;
        }

        $file = $request->file('company_logo');
        $dir = base_path('neuralinvoice/uploads/company_logos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = uniqid('f_', true) . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);

        return 'uploads/company_logos/' . $name;
    }

    private function tablePayload(string $table, array $payload): array
    {
        return collect($payload)
            ->filter(fn($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }

    private function legacyInvoicePayload(Request $request): array
    {
        $invoiceType = (int) $request->input('invoice_type', 1);
        $clientId = (int) $request->input('client_id', 0);
        $invoiceDate = (string) $request->input('invoiceDate', '');
        $concernPerson = trim((string) $request->input('concern_person', ''));
        $mobile = trim((string) $request->input('mobile', ''));
        $condition = trim((string) $request->input('condition', ''));
        $deliveryDate = (string) $request->input('deliveryDate', '');
        $deuDate = (string) $request->input('deudate', '');
        $descriptions = $request->input('discription', []);
        $durations = $request->input('duration_days', []);
        $amounts = $request->input('Amount', []);
        $discount = max(0, (float) $request->input('discount', 0));
        $error = '';

        if ($clientId <= 0) {
            $error = 'Please select client.';
        } elseif ($invoiceDate === '') {
            $error = 'Invoice Date is required.';
        } elseif ($concernPerson === '') {
            $error = 'Concern Person is required.';
        } elseif ($mobile === '') {
            $error = 'Mobile is required.';
        } elseif ($condition === '') {
            $error = 'Term & Condition is required.';
        } elseif ($deliveryDate === '') {
            $error = 'Delivery Date is required.';
        } elseif ($deuDate === '') {
            $error = 'Due Date is required.';
        } elseif (!is_array($descriptions) || !is_array($durations) || !is_array($amounts)) {
            $error = 'Invalid line items payload. Ensure discription[], duration_days[] and Amount[] are submitted correctly.';
        }

        $lines = [];
        $subtotal = 0.0;
        if ($error === '') {
            foreach ($amounts as $index => $amountValue) {
                $description = trim((string) ($descriptions[$index] ?? ''));
                $duration = trim((string) ($durations[$index] ?? ''));
                $amount = (float) $amountValue;

                if ($description === '' && $duration === '' && abs($amount) < 0.00001) {
                    continue;
                }

                if ($description === '' || $duration === '' || $amount <= 0) {
                    $error = 'Each line needs description, duration and amount.';
                    break;
                }

                $lines[] = [
                    'description' => $description,
                    'duration' => $duration,
                    'amount' => round($amount, 2),
                ];
                $subtotal += $amount;
            }

            if ($error === '' && count($lines) === 0) {
                $error = 'At least one invoice line is required.';
            }
        }

        $subtotal = round($subtotal, 2);
        $finalAmount = round(max(0, $subtotal - $discount), 2);
        $tax = $this->invoiceTaxForClient($clientId, $finalAmount);
        $bankChargeAmount = (float) ($tax['bankChargeAmount'] ?? 0);
        $totalAmount = round($finalAmount + (float) $tax['sgstAmount'] + (float) $tax['cgstAmount'] + (float) $tax['igstAmount'] + $bankChargeAmount, 2);
        $currency = strtoupper(trim((string) $request->input('currency', 'INR'))) === 'USD' ? 'USD' : 'INR';
        $exchangeRate = max(0, (float) $request->input('exchange_rate', 1));
        $totalAmountForeign = $currency === 'USD' && $exchangeRate > 0 ? round($totalAmount / $exchangeRate, 2) : 0;

        return [
            [
                'invoice_type' => $invoiceType,
                'oldInvoiceNumber' => trim((string) $request->input('oldInvoiceNumber', '')),
                'newInvoiceNumber' => trim((string) $request->input('newInvoiceNumber', '')),
                'client_id' => $clientId,
                'milestone_id' => (int) $request->input('milestone_id', 0),
                'invoiceDate' => $invoiceDate,
                'concern_person' => $concernPerson,
                'mobile' => $mobile,
                'condition' => $condition,
                'deliveryDate' => $deliveryDate,
                'deudate' => $deuDate,
                'subtotal' => $subtotal,
                'discount' => round($discount, 2),
                'finalAmount' => $finalAmount,
                'sgstAmount' => $tax['sgstAmount'],
                'cgstAmount' => $tax['cgstAmount'],
                'igstAmount' => $tax['igstAmount'],
                'bankChargePercent' => $tax['bankChargePercent'],
                'bankChargeAmount' => $bankChargeAmount,
                'totalAmount' => $totalAmount,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'total_amount_foreign' => $totalAmountForeign,
            ],
            $lines,
            $error
        ];
    }

    private function invoiceTaxForClient(int $clientId, float $baseAmount): array
    {
        $row = $this->clientLocation($clientId);
        $country = strtolower(trim((string) ($row->CountryName ?? '')));
        $state = strtolower(trim((string) ($row->StateName ?? '')));
        $isInternational = !(($country === '' || $country === 'india') && $state !== 'internation' && $state !== 'international');

        if ($isInternational) {
            return [
                'sgstAmount' => 0.0,
                'cgstAmount' => 0.0,
                'igstAmount' => 0.0,
                'bankChargePercent' => 3.5,
                'bankChargeAmount' => round($baseAmount * 0.035, 2),
            ];
        }

        if ($state === 'uttar pradesh') {
            return [
                'sgstAmount' => round($baseAmount * 0.09, 2),
                'cgstAmount' => round($baseAmount * 0.09, 2),
                'igstAmount' => 0.0,
                'bankChargePercent' => 0.0,
                'bankChargeAmount' => 0.0,
            ];
        }

        return [
            'sgstAmount' => 0.0,
            'cgstAmount' => 0.0,
            'igstAmount' => round($baseAmount * 0.18, 2),
            'bankChargePercent' => 0.0,
            'bankChargeAmount' => 0.0,
        ];
    }

    private function clientLocation(int $clientId)
    {
        if (!$this->table('clientmaster')) {
            return null;
        }

        return DB::table('clientmaster as cm')
            ->leftJoin('countrymaster as c', 'cm.Country', '=', 'c.CountryID')
            ->leftJoin('statemaster as s', 'cm.StateID', '=', 's.StateID')
            ->select('cm.ClientID', 'cm.ClientName', 'cm.ConcernOwner', 'cm.EmailAddress', 'cm.MobileNo', 'cm.Country as CountryID', 'cm.StateID', 'c.CountryName', 's.StateName')
            ->where('cm.ClientID', $clientId)
            ->first();
    }

    private function saveConcernPersonsFromRequest(Request $request, int $projectId, int $clientId, string $projectName): void
    {
        if (!Schema::hasTable('project_concern_persons')) {
            return;
        }

        $names = $request->input('concern_name', []);
        $emails = $request->input('concern_email', []);
        $mobiles = $request->input('concern_mobile', []);
        $designations = $request->input('concern_designation', []);
        $primaryIndex = (int) $request->input('concern_primary_index', 0);
        $canLogins = $request->input('concern_can_login', []);
        $passwords = $request->input('concern_password', []);
        $sendEmails = $request->input('concern_send_email', []);
        $existingPersonIds = $request->input('concern_person_id', []);

        $clientMaster = DB::table('clientmaster')->where('ClientID', $clientId)->first();
        $clientCompanyName = $clientMaster->ClientName ?? null;

        $processedIds = [];

        foreach ((array) $names as $index => $name) {
            $name = trim((string) $name);
            $email = strtolower(trim((string) ($emails[$index] ?? '')));
            $mobile = trim((string) ($mobiles[$index] ?? ''));
            $designation = trim((string) ($designations[$index] ?? ''));

            if ($name === '' && $email === '') {
                continue;
            }

            $personId = (int) ($existingPersonIds[$index] ?? 0);
            $canLogin = !empty($canLogins[$index]);
            $sendEmail = !empty($sendEmails[$index]);
            $rawPassword = trim((string) ($passwords[$index] ?? ''));
            $isPrimary = ($index === $primaryIndex);

            if ($canLogin && empty($rawPassword)) {
                $rawPassword = Str::random(8);
            }

            $loginClientId = null;
            if ($canLogin && $email !== '') {
                $clientUser = $this->createOrUpdateClientLogin(
                    $name,
                    $email,
                    $mobile,
                    $rawPassword ?: null,
                    $clientId,
                    $clientCompanyName
                );
                $loginClientId = $clientUser?->id;
            }

            $credentialsSentAt = null;
            if ($canLogin && $sendEmail && $email !== '' && !empty($rawPassword)) {
                try {
                    Mail::to($email)->send(new ClientCredentialsMail(
                        recipientName: $name,
                        loginEmail: $email,
                        rawPassword: $rawPassword,
                        projectName: $projectName,
                        clientCompanyName: $clientCompanyName,
                        designation: $designation
                    ));
                    $credentialsSentAt = now();
                } catch (\Throwable $e) {
                    Log::warning('Failed sending client credentials email: ' . $e->getMessage());
                }
            }

            $person = null;
            if ($personId > 0) {
                $person = ProjectConcernPerson::where('project_id', $projectId)->where('id', $personId)->first();
            }

            if ($person) {
                $person->update([
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'designation' => $designation,
                    'is_primary' => $isPrimary,
                    'can_login' => $canLogin,
                    'login_client_id' => $loginClientId ?: $person->login_client_id,
                    'credentials_sent_at' => $credentialsSentAt ?: $person->credentials_sent_at,
                ]);
            } else {
                $person = ProjectConcernPerson::create([
                    'project_id' => $projectId,
                    'client_id' => $clientId,
                    'login_client_id' => $loginClientId,
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'designation' => $designation,
                    'is_primary' => $isPrimary,
                    'can_login' => $canLogin,
                    'credentials_sent_at' => $credentialsSentAt,
                ]);
            }

            $processedIds[] = $person->id;
        }

        if (!empty($processedIds)) {
            ProjectConcernPerson::where('project_id', $projectId)
                ->whereNotIn('id', $processedIds)
                ->delete();
        }
    }

    private function createOrUpdateClientLogin(string $name, string $email, ?string $mobile, ?string $password, int $clientId, ?string $companyName): ?Client
    {
        if (empty($email)) {
            return null;
        }

        $clientUser = Client::where('email', $email)->first();
        $nameParts = explode(' ', $name, 2);

        if (!$clientUser) {
            $clientUser = Client::create([
                'first_name' => $nameParts[0],
                'last_name' => $nameParts[1] ?? '',
                'client_name' => $name,
                'company_name' => $companyName ?: 'Client Portal User',
                'username' => $email,
                'email' => $email,
                'phone' => $mobile,
                'mobile' => $mobile,
                'password' => Hash::make($password ?: Str::random(8)),
                'invoice_client_id' => $clientId,
                'status' => 'Active',
            ]);

            try {
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'client']);
                    $clientUser->assignRole($role);
                }
            } catch (\Throwable $e) {
                // Ignore role assignment failure if not configured
            }
        } else {
            $clientUser->first_name = $nameParts[0];
            if (isset($nameParts[1])) {
                $clientUser->last_name = $nameParts[1];
            }
            $clientUser->client_name = $name;
            if ($mobile) {
                $clientUser->phone = $mobile;
                $clientUser->mobile = $mobile;
            }
            if ($companyName) {
                $clientUser->company_name = $companyName;
            }
            if (!$clientUser->invoice_client_id) {
                $clientUser->invoice_client_id = $clientId;
            }
            if (!empty($password)) {
                $clientUser->password = Hash::make($password);
            }
            $clientUser->status = 'Active';
            $clientUser->save();
        }

        return $clientUser;
    }

    private function companyIdForClient(int $clientId): int
    {
        $companyId = $clientId > 0 && $this->table('clientmaster')
            ? (int) DB::table('clientmaster')->where('ClientID', $clientId)->value('CompanyID')
            : 0;

        if ($companyId > 0) {
            return $companyId;
        }

        if (!$this->table('companymaster')) {
            return 11;
        }

        return (int) (DB::table('companymaster')->where('DelStatus', 1)->orderBy('CompanyID')->value('CompanyID')
            ?? DB::table('companymaster')->orderBy('CompanyID')->value('CompanyID')
            ?? 11);
    }

    private function invoicePrefixForClient(int $clientId): string
    {
        $source = '';
        if ($clientId > 0 && $this->table('clientmaster') && $this->table('companymaster')) {
            $source = (string) DB::table('clientmaster as cm')
                ->leftJoin('companymaster as co', 'cm.CompanyID', '=', 'co.CompanyID')
                ->where('cm.ClientID', $clientId)
                ->value('co.CompanyCode');
        }

        if ($source === '' && $this->table('companymaster')) {
            $source = (string) DB::table('companymaster')
                ->where('DelStatus', 1)
                ->whereRaw("COALESCE(CompanyCode, '') <> ''")
                ->orderBy('CompanyID')
                ->value('CompanyCode');
        }

        $prefix = preg_replace('/[^A-Z0-9]+/', '', strtoupper($source ?: 'NSPL'));

        return $prefix !== '' ? substr($prefix, 0, 12) : 'NSPL';
    }

    private function nextInvoiceNumber(int $invoiceId): string
    {
        $now = now();
        $yearStart = $now->month >= 4 ? $now->year : $now->year - 1;
        $yearEnd = ($yearStart + 1) % 100;

        return sprintf('NISPL/%d-%02d/%03d', $yearStart % 100, $yearEnd, $invoiceId);
    }

    private function invoiceAmounts(array $data): array
    {
        $subtotal = (float) $data['subtotal'];
        $discount = (float) ($data['discount'] ?? 0);
        $finalAmount = max(0, $subtotal - $discount);
        $cgst = $data['gst_mode'] === 'intra' ? round($finalAmount * 0.09, 2) : 0;
        $sgst = $data['gst_mode'] === 'intra' ? round($finalAmount * 0.09, 2) : 0;
        $igst = $data['gst_mode'] === 'inter' ? round($finalAmount * 0.18, 2) : 0;

        return [
            'subtotal' => (int) round($subtotal),
            'discount' => (int) round($discount),
            'finalAmount' => (int) round($finalAmount),
            'cgst' => (int) round($cgst),
            'sgst' => (int) round($sgst),
            'igst' => (int) round($igst),
            'total' => (int) round($finalAmount + $cgst + $sgst + $igst),
        ];
    }

    private function syncInvoicePaymentStatus(int $invoiceId): void
    {
        $invoice = DB::table('invoice')->where('id', $invoiceId)->first();
        if (!$invoice) {
            return;
        }

        $paid = DB::table('recived_payment')
            ->where('workAmount_invoice_id', $invoiceId)
            ->where('status', 1)
            ->sum('received_net');

        DB::table('invoice')->where('id', $invoiceId)->update([
            'status' => $paid >= $invoice->totalAmount ? 2 : ($paid > 0 ? 1 : 0),
        ]);
    }

    private function clientPayload(Request $request): array
    {
        $clientName = trim((string) $request->input('client_name', ''));
        $concernOwner = trim((string) $request->input('concern_person', ''));
        $fax = trim((string) $request->input('cin', ''));
        $website = trim((string) $request->input('website', ''));
        $email = trim((string) $request->input('email', ''));
        $mobile = trim((string) $request->input('mobile', ''));
        $country = trim((string) $request->input('country', ''));
        $stateId = trim((string) $request->input('state', ''));
        $address = trim((string) $request->input('address', ''));
        $gstNumber = trim((string) $request->input('gst_number', ''));
        $status = (string) $request->input('status', '1');
        $error = '';

        if ($clientName === '') {
            $error = 'Client Name is required.';
        } elseif ($concernOwner === '') {
            $error = 'Concern Person is required.';
        } elseif ($website === '') {
            $error = 'Website is required.';
        } elseif ($email === '') {
            $error = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif ($mobile === '') {
            $error = 'Mobile is required.';
        } else {
            $mobileDigits = preg_replace('/\D+/', '', $mobile);
            if (strlen($mobileDigits) < 10 || strlen($mobileDigits) > 15) {
                $error = 'Invalid mobile number.';
            } else {
                $mobile = $mobileDigits;
            }
        }

        if ($error === '' && ($country === '' || $country === '0')) {
            $error = 'Country is required.';
        }

        if ($error === '') {
            if (!$this->countryIsIndia((int) $country)) {
                $stateId = (string) $this->internationalStateId((int) $country);
                if ($stateId === '0') {
                    $error = 'International state could not be set.';
                }
            } elseif ($stateId === '' || $stateId === '0') {
                $error = 'State is required.';
            }
        }

        if ($error === '' && $address === '') {
            $error = 'Address is required.';
        }

        if ($error === '' && !in_array($status, ['0', '1'], true)) {
            $error = 'Invalid status.';
        }

        if ($error === '' && $fax !== '') {
            $cinClean = strtoupper(str_replace(' ', '', $fax));
            if (!preg_match('/^[A-Z0-9]{10,21}$/', $cinClean)) {
                $error = 'Invalid CIN. Format: Alphanumeric, 10-21 characters';
            } else {
                $fax = $cinClean;
            }
        }

        if ($error === '' && $gstNumber !== '') {
            $gstClean = strtoupper(str_replace(' ', '', $gstNumber));
            if (!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/', $gstClean)) {
                $error = 'Invalid GST Number. Format: 15 characters (e.g., 22AAAAA0000A1Z5)';
            } else {
                $gstNumber = $gstClean;
            }
        }

        return [
            [
                'ClientName' => $clientName,
                'ConcernOwner' => $concernOwner,
                'Address' => $address,
                'MobileNo' => $mobile,
                'Fax' => $fax,
                'website' => $website,
                'gst_number' => $gstNumber,
                'EmailAddress' => $email,
                'Country' => $country,
                'StateID' => $stateId,
                'DelStatus' => (int) $status,
            ],
            $error
        ];
    }

    private function portalPasswordError(Request $request): string
    {
        $password = trim((string) $request->input('password', ''));
        $confirmation = trim((string) $request->input('password_confirmation', ''));

        if ($password === '' && $confirmation === '') {
            return '';
        }

        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters.';
        }

        if ($password !== $confirmation) {
            return 'Password confirmation does not match.';
        }

        return '';
    }

    private function syncPortalClient(int $invoiceClientId, array $invoiceData, Request $request): ?Client
    {
        if (!Schema::hasTable('clients')) {
            return null;
        }

        $email = (string) $invoiceData['EmailAddress'];
        $client = null;

        if (Schema::hasColumn('clients', 'invoice_client_id')) {
            $client = Client::where('invoice_client_id', $invoiceClientId)->first();
        }

        if (!$client) {
            $client = Client::where('email', $email)->first();
        }

        if ($client && $client->email !== $email && Client::where('email', $email)->where('id', '<>', $client->id)->exists()) {
            throw new \RuntimeException('Another portal client already uses this email address.');
        }

        $password = trim((string) $request->input('password', ''));
        if (!$client && $password === '') {
            return null;
        }

        [$firstName, $lastName] = $this->splitClientName((string) $invoiceData['ConcernOwner']);

        $payload = [
            'invoice_client_id' => $invoiceClientId,
            'role_id' => $this->clientRoleId(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $client?->username ?: $this->uniquePortalUsername($email),
            'email' => $email,
            'phone' => (string) $invoiceData['MobileNo'],
            'company_name' => (string) $invoiceData['ClientName'],
            'status' => ((int) ($invoiceData['DelStatus'] ?? 1) === 1) ? 'Active' : 'Inactive',
            'invoiceDate' => now()->toDateString(),
            'client_name' => (string) $invoiceData['ClientName'],
            'concern_person' => (string) $invoiceData['ConcernOwner'],
            'cin' => (string) $invoiceData['Fax'],
            'website' => (string) $invoiceData['website'],
            'mobile' => (string) $invoiceData['MobileNo'],
            'country_id' => (int) $invoiceData['Country'],
            'state_id' => (int) $invoiceData['StateID'],
            'address' => (string) $invoiceData['Address'],
            'gst_number' => (string) $invoiceData['gst_number'],
        ];

        if ($password !== '') {
            $payload['password'] = $password;
        }

        $payload = $this->supportedClientPayload($payload);

        if ($client) {
            $client->fill($payload)->save();

            return $client->fresh();
        }

        return Client::create($payload);
    }

    private function sendPortalLoginIfRequested(Request $request, ?Client $client, string $plainPassword): string
    {
        if (!$request->boolean('send_login_email')) {
            return '';
        }

        if (!$client) {
            return 'Portal login email was not sent because no portal account exists for this client yet.';
        }

        try {
            Mail::send('emails.client-login', [
                'client' => $client,
                'loginUrl' => route('client.login'),
                'plainPassword' => $plainPassword,
            ], function ($message) use ($client) {
                $message->to($client->email)
                    ->subject('Your Client Portal Login');
            });
        } catch (Throwable $e) {
            return 'Portal login email could not be sent: ' . $e->getMessage();
        }

        return 'Portal login email sent successfully.';
    }

    private function supportedClientPayload(array $payload): array
    {
        $columns = Schema::getColumnListing('clients');

        return array_intersect_key($payload, array_flip($columns));
    }

    private function clientRoleId(): ?int
    {
        if (!Schema::hasTable('roles')) {
            return null;
        }

        return Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'client',
        ])->id;
    }

    private function uniquePortalUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '.');
        $base = $base !== '' ? $base : 'client';
        $username = $base;
        $counter = 1;

        while (Client::where('username', $username)->exists()) {
            $username = $base . '.' . $counter;
            $counter++;
        }

        return $username;
    }

    private function splitClientName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [
            $parts[0] ?? 'Client',
            $parts[1] ?? '',
        ];
    }

    private function countryIsIndia(int $countryId): bool
    {
        if ($countryId <= 0 || !$this->table('countrymaster')) {
            return false;
        }

        $countryName = DB::table('countrymaster')
            ->where('CountryID', $countryId)
            ->value('CountryName');

        return strtolower(trim((string) $countryName)) === 'india';
    }

    private function internationalStateId(int $fallbackCountryId = 0): int
    {
        if (!$this->table('statemaster')) {
            return 0;
        }

        $stateId = DB::table('statemaster')
            ->whereIn(DB::raw('LOWER(TRIM(StateName))'), ['international', 'internation'])
            ->orderBy('StateID')
            ->value('StateID');

        if ($stateId) {
            return (int) $stateId;
        }

        $countryId = $fallbackCountryId;
        if ($countryId <= 0 && $this->table('countrymaster')) {
            $indiaId = DB::table('countrymaster')
                ->whereRaw("LOWER(TRIM(CountryName)) = 'india'")
                ->value('CountryID');

            $countryQuery = DB::table('countrymaster')->orderBy('CountryID');
            if ($indiaId) {
                $countryQuery->where('CountryID', '<>', $indiaId);
            }
            $countryId = (int) $countryQuery->value('CountryID');
        }

        if ($countryId <= 0) {
            return 0;
        }

        return (int) DB::table('statemaster')->insertGetId([
            'StateName' => 'International',
            'CountryID' => $countryId,
        ]);
    }

    private function options(string $table, string $key, string $label, ?string $where = null)
    {
        if (!$this->table($table)) {
            return collect();
        }

        $query = DB::table($table)->select($key, $label);
        if ($where) {
            $query->whereRaw($where);
        }

        return $query->orderBy($label)->get();
    }

    private function countTable(string $table, ?string $where = null): int
    {
        if (!$this->table($table)) {
            return 0;
        }

        $query = DB::table($table);
        if ($where) {
            $query->whereRaw($where);
        }

        return (int) $query->count();
    }

    private function sumTable(string $table, string $column, ?string $where = null): float
    {
        if (!$this->table($table)) {
            return 0;
        }

        $query = DB::table($table);
        if ($where) {
            $query->whereRaw($where);
        }

        return (float) $query->sum($column);
    }

    private function table(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 15, request('page', 1), [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    private function clientAvatarHtml(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        if (count($words) >= 2 && !empty($words[0]) && !empty($words[1])) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } elseif (count($words) === 1 && !empty($words[0])) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, min(2, mb_strlen($words[0]))));
        } else {
            $initials = 'CL';
        }

        $palette = ['#4f46e5', '#0284c7', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2', '#2563eb'];
        $bgColor = $palette[abs(crc32($name)) % count($palette)];

        return '<span class="client-avatar-badge rounded-circle me-2 fw-bold text-white d-inline-flex align-items-center justify-content-center flex-shrink-0" style="background-color: ' . $bgColor . ';" title="' . e($name) . '">' . e($initials) . '</span>';
    }
}
