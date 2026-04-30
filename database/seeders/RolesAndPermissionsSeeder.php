<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tables = config('permission.table_names');

        DB::table($tables['role_has_permissions'])->delete();
        DB::table($tables['model_has_permissions'])->delete();
        Permission::query()->delete();

        $permissions = [
            'hackathons.viewAny',
            'hackathons.view',
            'hackathons.create',
            'hackathons.update',
            'hackathons.delete',
            'hackathons.forceDelete',
            'hackathons.restore',
            'hackathons.changeStatus',
            'hackathons.viewAll',
            'teams.viewAny',
            'teams.view',
            'teams.create',
            'teams.update',
            'teams.delete',
            'teams.ban',
            'teams.unban',
            'teams.viewAll',
            'submissions.viewAny',
            'submissions.view',
            'submissions.create',
            'submissions.update',
            'submissions.reopen',
            'submissions.disqualify',
            'submissions.viewAll',
            'users.viewAny',
            'users.view',
            'users.update',
            'users.delete',
            'users.changeRole',
            'users.ban',
            'users.unban',
            'users.impersonate',
            'announcements.viewAny',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',
            'judges.assign',
            'judges.remove',
            'judges.banTeam',
            'scores.create',
            'scores.update',
            'settings.view',
            'settings.update',
            'cache.clear',
        ];

        foreach ($permissions as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => RoleEnum::SuperAdmin->value, 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $announcementPermissions = [
            'announcements.viewAny',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',
        ];

        Role::firstOrCreate(['name' => RoleEnum::Organizer->value, 'guard_name' => 'web'])
            ->syncPermissions([
                'hackathons.viewAny',
                'hackathons.view',
                'hackathons.create',
                'hackathons.update',
                'hackathons.delete',
                'hackathons.changeStatus',
                'teams.viewAny',
                'teams.view',
                'teams.update',
                'teams.ban',
                'teams.unban',
                'submissions.viewAny',
                'submissions.view',
                'submissions.reopen',
                'submissions.disqualify',
                ...$announcementPermissions,
                'judges.assign',
                'judges.remove',
            ]);

        Role::firstOrCreate(['name' => RoleEnum::Participant->value, 'guard_name' => 'web'])
            ->syncPermissions([
                'teams.create',
                'teams.view',
                'submissions.create',
                'submissions.update',
                'hackathons.view',
            ]);

        Role::firstOrCreate(['name' => RoleEnum::Judge->value, 'guard_name' => 'web'])
            ->syncPermissions([
                'submissions.viewAny',
                'submissions.view',
                'scores.create',
                'scores.update',
                'judges.banTeam',
            ]);

        Role::firstOrCreate(['name' => RoleEnum::Mentor->value, 'guard_name' => 'web'])
            ->syncPermissions([
                'teams.view',
                'teams.viewAny',
                'submissions.view',
                'submissions.viewAny',
            ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
