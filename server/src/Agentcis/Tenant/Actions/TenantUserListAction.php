<?php

namespace Agentcis\Tenant\Actions;

use Agentcis\Tenant\Model\Tenant;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TenantUserListAction
{
    public function __invoke($tenantId)
    {
        $tenant = Tenant::query()->where('tenants.id', $tenantId)
            ->select(
                'tenants.id',
                'tenants.business_name',
                'tenants.first_name',
                'tenants.last_name',
                'tenants.email',
                'tenants.db_name')->first();

        $table = $tenant->db_name . '.%s';

        $users = DB::table(sprintf($table, 'users'))
            ->join(sprintf($table, 'branches'), 'branches.id', 'users.branch_id')
            ->join(sprintf($table, 'model_has_roles'), function (JoinClause $query) {
                $query->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', 'user');
            })
            ->join(sprintf($table, 'roles'), 'roles.id', 'model_has_roles.role_id')
            ->select([
                'users.id',
                'branches.name as branch_name',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.job_title',
                'users.status',
                'users.is_owner',
                'roles.display_name as role_name',
            ])
            ->addSelect(['secondary_branches' => DB::table(sprintf($table, 'branch_user'))
                ->join(sprintf($table, 'branches'), 'branches.id', '=', 'branch_user.branch_id')
                ->whereNull('branches.deleted_at')
                ->whereColumn('branch_user.user_id', 'users.id')
                ->selectRaw('GROUP_CONCAT(DISTINCT branches.name separator \', \')')
            ])
            ->cursor();

//        /**
//         * select `branches`.`name`, `branches`.`id`, `branch_user`.`user_id` as `pivot_user_id`,
//         * `branch_user`.`branch_id` as `pivot_branch_id` from `branches`
//         * inner join `branch_user` on `branches`.`id` = `branch_user`.`branch_id`
//         * where `branch_user`.`user_id` in (389, 390, 391, 392, 393, 394, 395, 396, 397, 398) and `branches`.`deleted_at` is null
//         */
//        $branches = DB::table(sprintf($table, 'branches'))
//            ->join(sprintf($table, 'branch_user'), 'branches.id', '=', 'branch_user.branch_id')
//            ->select([
//                'branches.name',
//                'branch_user.user_id'
//            ])->cursor();

        return [
            'data' => $users,
        ];
    }
}
