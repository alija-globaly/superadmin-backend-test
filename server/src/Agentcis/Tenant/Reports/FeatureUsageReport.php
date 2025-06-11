<?php

namespace Agentcis\Tenant\Reports;

use Illuminate\Database\DatabaseManager;

class FeatureUsageReport
{
    private $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    /**
     *
     *  TimeZone :: UTC
     *  || Tenant  || Feature || Date || Total Rows ||
     *
     */
    public function __invoke()
    {
        $tenants = $this->database->connection('agentcis')
            ->table('tenants')
            ->join('subscriptions', 'subscriptions.user_id', '=', 'tenants.id')
            ->select(
                'tenants.id',
                'tenants.business_name',
                'tenants.email',
                'tenants.db_name',
                'tenants.subdomain',
                'tenants.created_at',
                'tenants.zoho_id as zoho_customer_id',
                'subscriptions.zoho_id as zoho_subscription_id'
            )->get();

        $tenants->each(function($tenant) {
            $table = $tenant->db_name. '.users';
            $tenant->users = $this->database->connection('agentcis')
                ->table($table)
                ->join($tenant->db_name. '.branches', 'branches.id', '=', 'users.branch_id')
                ->join($tenant->db_name. '.model_has_roles', function($query) use ($tenant){
                    $query->on($tenant->db_name. '.users.id', '=', $tenant->db_name. '.model_has_roles.model_id')
                        ->where('.model_type', 'user');
                })
                ->join($tenant->db_name. '.roles', 'roles.id', '=', $tenant->db_name. '.model_has_roles.role_id')
                ->select(
                    'users.id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.photo',
                    'users.phone_number',
                    'users.status',
                    'users.is_owner',
                    'users.job_title',
                    'users.created_at',
                    'branches.name',
                    'roles.display_name as role_name'
                )->get();

            return $tenant;
        });
    }
}
