<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Creates exactly one Admin + demo Managers/Employees with profiles.
     * Companies and company_id are attached later in CompanySeeder.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // ===== One Admin only (web dashboard) =====
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@taskmanager.test'],
            [
                'name' => 'System Admin',
                'password' => $password,
                'role' => 'Admin',
                'active' => true,
                'company_id' => null,
                'email_verified_at' => now(),
            ]
        );
        $this->seedProfile($admin, [
            'phone' => '0500000001',
            'address' => 'Admin Office',
            'bio' => 'Platform administrator',
        ]);

        // ===== Managers (company owners) =====
        $managers = [
            [
                'name' => 'Ahmed Manager',
                'email' => 'ahmed.manager@taskmanager.test',
                'phone' => '0501111001',
                'address' => 'Riyadh',
                'bio' => 'Company owner and manager',
            ],
            [
                'name' => 'Sara Manager',
                'email' => 'sara.manager@taskmanager.test',
                'phone' => '0501111002',
                'address' => 'Jeddah',
                'bio' => 'Company owner and manager',
            ],
        ];

        foreach ($managers as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'role' => 'Manager',
                    'active' => true,
                    'company_id' => null,
                    'email_verified_at' => now(),
                ]
            );
            $this->seedProfile($user, $data);
        }

        // ===== Employees =====
        $employees = [
            [
                'name' => 'Omar Employee',
                'email' => 'omar.employee@taskmanager.test',
                'phone' => '0502222001',
                'address' => 'Riyadh',
                'bio' => 'Backend developer',
            ],
            [
                'name' => 'Layla Employee',
                'email' => 'layla.employee@taskmanager.test',
                'phone' => '0502222002',
                'address' => 'Riyadh',
                'bio' => 'Frontend developer',
            ],
            [
                'name' => 'Yousef Employee',
                'email' => 'yousef.employee@taskmanager.test',
                'phone' => '0502222003',
                'address' => 'Jeddah',
                'bio' => 'QA engineer',
            ],
            [
                'name' => 'Noura Employee',
                'email' => 'noura.employee@taskmanager.test',
                'phone' => '0502222004',
                'address' => 'Jeddah',
                'bio' => 'UI/UX designer',
            ],
            [
                'name' => 'Khaled Employee',
                'email' => 'khaled.employee@taskmanager.test',
                'phone' => '0502222005',
                'address' => 'Dammam',
                'bio' => 'DevOps engineer',
            ],
            [
                'name' => 'Maha Pending',
                'email' => 'maha.pending@taskmanager.test',
                'phone' => '0502222006',
                'address' => 'Khobar',
                'bio' => 'Freelancer waiting to join a company',
            ],
        ];

        foreach ($employees as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'role' => 'Employee',
                    'active' => true,
                    'company_id' => null,
                    'email_verified_at' => now(),
                ]
            );
            $this->seedProfile($user, $data);
        }
    }

    private function seedProfile(User $user, array $data): void
    {
        Profile::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? '1995-01-15',
                'Image' => 'profiles/default.png',
                'bio' => $data['bio'] ?? null,
            ]
        );
    }
}
