<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyJoinRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $ahmed = User::query()->where('email', 'ahmed.manager@taskmanager.test')->firstOrFail();
        $sara = User::query()->where('email', 'sara.manager@taskmanager.test')->firstOrFail();

        $techCorp = Company::query()->updateOrCreate(
            ['name' => 'TechCorp Solutions'],
            ['user_id' => $ahmed->id]
        );

        $creativeHub = Company::query()->updateOrCreate(
            ['name' => 'Creative Hub'],
            ['user_id' => $sara->id]
        );

        // Link managers to their companies (users.company_id + employee_company)
        $this->linkUserToCompany($ahmed, $techCorp, isProjectManager: false);
        $this->linkUserToCompany($sara, $creativeHub, isProjectManager: false);

        // TechCorp employees
        $omar = User::query()->where('email', 'omar.employee@taskmanager.test')->firstOrFail();
        $layla = User::query()->where('email', 'layla.employee@taskmanager.test')->firstOrFail();
        $khaled = User::query()->where('email', 'khaled.employee@taskmanager.test')->firstOrFail();

        $this->linkUserToCompany($omar, $techCorp, isProjectManager: true);
        $this->linkUserToCompany($layla, $techCorp, isProjectManager: false);
        $this->linkUserToCompany($khaled, $techCorp, isProjectManager: false);

        // Creative Hub employees
        $yousef = User::query()->where('email', 'yousef.employee@taskmanager.test')->firstOrFail();
        $noura = User::query()->where('email', 'noura.employee@taskmanager.test')->firstOrFail();

        $this->linkUserToCompany($yousef, $creativeHub, isProjectManager: true);
        $this->linkUserToCompany($noura, $creativeHub, isProjectManager: false);

        // Pending join request (employee without company yet)
        $maha = User::query()->where('email', 'maha.pending@taskmanager.test')->firstOrFail();

        CompanyJoinRequest::query()->updateOrCreate(
            [
                'user_id' => $maha->id,
                'company_id' => $techCorp->id,
            ],
            [
                'status' => 'pending',
                'message' => 'I would like to join TechCorp as a developer.',
            ]
        );
    }

    private function linkUserToCompany(User $user, Company $company, bool $isProjectManager): void
    {
        $user->update(['company_id' => $company->id]);

        $exists = DB::table('employee_company')
            ->where('user_id', $user->id)
            ->where('company_id', $company->id)
            ->exists();

        if ($exists) {
            DB::table('employee_company')
                ->where('user_id', $user->id)
                ->where('company_id', $company->id)
                ->update([
                    'is_project_manager' => $isProjectManager,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('employee_company')->insert([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'is_project_manager' => $isProjectManager,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
