<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Seed demo users for each role.
     */
    public function run(): void
    {
        $demoUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'super_admin@prayangshu.com',
                'role' => RoleEnum::SuperAdmin,
            ],
            [
                'name' => 'Organizer',
                'email' => 'organizer@prayangshu.com',
                'role' => RoleEnum::Organizer,
            ],
            [
                'name' => 'Participant',
                'email' => 'user@prayangshu.com',
                'role' => RoleEnum::Participant,
            ],
            [
                'name' => 'Judge',
                'email' => 'judge@prayangshu.com',
                'role' => RoleEnum::Judge,
            ],
            [
                'name' => 'Mentor',
                'email' => 'mentor@prayangshu.com',
                'role' => RoleEnum::Mentor,
            ],
        ];

        foreach ($demoUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole($data['role']->value)) {
                $user->assignRole($data['role']->value);
            }
        }
    }
}
