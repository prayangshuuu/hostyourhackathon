<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
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
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ───────────────────────────────────────────
        // Define all permissions
        // ───────────────────────────────────────────
        $permissions = [
            // Organizer permissions
            'manage-hackathon',
            'manage-teams',
            'manage-submissions',
            'manage-announcements',
            'manage-judges',
            'view-scores',

            // Participant permissions
            'register-team',
            'submit-idea',
            'view-hackathon',

            // Judge permissions
            'view-submissions',
            'score-submissions',

            // Mentor permissions
            'view-teams',
            // 'view-submissions' is shared with judge
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ───────────────────────────────────────────
        // Create roles and assign permissions
        // ───────────────────────────────────────────

        // Super Admin — gets all permissions via Spatie's Gate::before wildcard
        Role::firstOrCreate(['name' => RoleEnum::SuperAdmin->value]);

        // Organizer
        Role::firstOrCreate(['name' => RoleEnum::Organizer->value])
            ->syncPermissions([
                'manage-hackathon',
                'manage-teams',
                'manage-submissions',
                'manage-announcements',
                'manage-judges',
                'view-scores',
            ]);

        // Participant
        Role::firstOrCreate(['name' => RoleEnum::Participant->value])
            ->syncPermissions([
                'register-team',
                'submit-idea',
                'view-hackathon',
            ]);

        // Judge
        Role::firstOrCreate(['name' => RoleEnum::Judge->value])
            ->syncPermissions([
                'view-submissions',
                'score-submissions',
            ]);

        // Mentor
        Role::firstOrCreate(['name' => RoleEnum::Mentor->value])
            ->syncPermissions([
                'view-teams',
                'view-submissions',
            ]);
    }
}
