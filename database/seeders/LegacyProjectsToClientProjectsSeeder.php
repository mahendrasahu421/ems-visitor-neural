<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LegacyProjectsToClientProjectsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['client_projects', 'clientmaster', 'work_type_master'] as $table) {
            if (!Schema::hasTable($table)) {
                $this->command?->error("{$table} table does not exist.");

                return;
            }
        }

        $legacyProjectsTable = $this->legacyProjectsTable();
        if (!$legacyProjectsTable) {
            $this->command?->error('Legacy projects source table does not exist.');

            return;
        }

        $indiaId = (string) (DB::table('countrymaster')
            ->whereRaw("LOWER(TRIM(CountryName)) = 'india'")
            ->value('CountryID') ?? '0');
        $stateId = $indiaId !== '0'
            ? (string) (DB::table('statemaster')->where('CountryID', $indiaId)->orderBy('StateID')->value('StateID') ?? '')
            : '';

        $inserted = 0;
        $updated = 0;
        $clientsCreated = 0;
        $workTypesCreated = 0;

        DB::transaction(function () use ($legacyProjectsTable, $indiaId, $stateId, &$inserted, &$updated, &$clientsCreated, &$workTypesCreated) {
            $legacyProjects = DB::table($legacyProjectsTable)->orderBy('id')->get();

            foreach ($legacyProjects as $legacyProject) {
                $clientId = $this->clientIdForName((string) $legacyProject->client_name, $indiaId, $stateId, $clientsCreated);
                $workTypeId = $this->workTypeIdForName((string) $legacyProject->project_category, $workTypesCreated);
                $legacyMarker = 'Migrated from legacy projects ID: ' . $legacyProject->id;
                $existingId = DB::table('client_projects')
                    ->where('project_id', $legacyProject->id)
                    ->value('project_id');

                $markedId = null;
                if (!$existingId && Schema::hasColumn('client_projects', 'notes')) {
                    $markedId = DB::table('client_projects')
                        ->where('notes', 'like', $legacyMarker . '%')
                        ->value('project_id');
                }

                $status = ($legacyProject->status === 'active' && $legacyProject->deleted_at === null) ? 1 : 0;
                $createdAt = $legacyProject->created_at ?: now();

                $payload = $this->clientProjectPayload([
                    'project_id' => (int) $legacyProject->id,
                    'client_id' => $clientId,
                    'project_name' => (string) $legacyProject->project_name,
                    'engagement_type' => 'project',
                    'has_amc' => 'no',
                    'payment_mode' => 'percentage',
                    'total_value' => 0,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'cgst_percent' => 0,
                    'cgst_amount' => 0,
                    'sgst_percent' => 0,
                    'sgst_amount' => 0,
                    'igst_percent' => 0,
                    'igst_amount' => 0,
                    'tax_type' => null,
                    'total_value_with_tax' => 0,
                    'total_with_tax' => 0,
                    'final_amount' => 0,
                    'amc_start' => null,
                    'amc_end' => null,
                    'project_start_date' => date('Y-m-d', strtotime((string) $createdAt)),
                    'project_end_date' => date('Y-m-d', strtotime((string) $createdAt)),
                    'financial_year' => $this->financialYearForDate((string) $createdAt),
                    'amc_working_days' => 0,
                    'project_duration_days' => 1,
                    'duration_mode' => 'working',
                    'work_type' => (string) $legacyProject->project_category,
                    'work_type_id' => $workTypeId,
                    'proposal_file' => null,
                    'notes' => $legacyMarker . '. Category: ' . $legacyProject->project_category . '.',
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $legacyProject->updated_at ?: $createdAt,
                    'DelStatus' => $status,
                ]);

                if ($existingId) {
                    unset($payload['project_id']);
                    DB::table('client_projects')->where('project_id', $existingId)->update($payload);
                    $updated++;
                } elseif ($markedId) {
                    DB::table('client_projects')->where('project_id', $markedId)->update($payload);
                    $updated++;
                } else {
                    DB::table('client_projects')->insert($payload);
                    $inserted++;
                }
            }
        });

        $this->command?->info(
            "Legacy projects migrated. Inserted: {$inserted}. Updated: {$updated}. Clients created: {$clientsCreated}. Work types created: {$workTypesCreated}."
        );
    }

    private function clientIdForName(string $clientName, string $indiaId, string $stateId, int &$clientsCreated): int
    {
        $clientId = DB::table('clientmaster')->where('ClientName', $clientName)->value('ClientID');
        if ($clientId) {
            return (int) $clientId;
        }

        $clientId = DB::table('clientmaster')->insertGetId([
            'CompanyID' => 11,
            'ProductCategoryType' => null,
            'CategoryType' => null,
            'ClientCode' => '',
            'Priority' => '',
            'Source' => null,
            'ClientName' => $clientName,
            'ConcernOwner' => 'NA',
            'Address' => 'NA',
            'MobileNo' => '0000000000',
            'Fax' => '',
            'website' => '',
            'client_logo' => '',
            'gst_number' => '',
            'pinNo' => '',
            'EmailAddress' => '',
            'Country' => $indiaId,
            'StateID' => $stateId,
            'PhoneNo' => '',
            'DelStatus' => 1,
            'created_at' => now(),
        ], 'ClientID');

        $clientsCreated++;

        return (int) $clientId;
    }

    private function legacyProjectsTable(): ?string
    {
        if (Schema::hasTable('projects')) {
            return 'projects';
        }

        $backup = DB::table('information_schema.tables')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'like', 'projects_backup_%')
            ->orderByDesc('table_name')
            ->value('table_name');

        return $backup ? (string) $backup : null;
    }

    private function workTypeIdForName(string $workTypeName, int &$workTypesCreated): int
    {
        $workTypeId = DB::table('work_type_master')
            ->whereRaw('LOWER(TRIM(WorkTypeName)) = ?', [strtolower(trim($workTypeName))])
            ->value('WorkTypeID');

        if ($workTypeId) {
            return (int) $workTypeId;
        }

        $workTypeId = ((int) DB::table('work_type_master')->max('WorkTypeID')) + 1;

        DB::table('work_type_master')->insert([
            'WorkTypeID' => $workTypeId,
            'WorkTypeName' => $workTypeName,
            'amc_type' => 'project',
            'DelStatus' => 1,
            'sort_order' => $workTypeId,
            'created_at' => now(),
        ]);

        $workTypesCreated++;

        return $workTypeId;
    }

    private function clientProjectPayload(array $payload): array
    {
        return array_filter(
            $payload,
            fn(string $column): bool => Schema::hasColumn('client_projects', $column),
            ARRAY_FILTER_USE_KEY
        );
    }

    private function financialYearForDate(string $date): string
    {
        $timestamp = strtotime($date) ?: time();
        $year = (int) date('Y', $timestamp);
        $month = (int) date('n', $timestamp);
        $startYear = $month >= 4 ? $year : $year - 1;

        return $startYear . '-' . substr((string) ($startYear + 1), -2);
    }
}
