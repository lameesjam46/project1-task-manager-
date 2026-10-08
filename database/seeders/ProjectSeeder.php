<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $techCorp = Company::query()->where('name', 'TechCorp Solutions')->firstOrFail();
        $creativeHub = Company::query()->where('name', 'Creative Hub')->firstOrFail();

        $omar = User::query()->where('email', 'omar.employee@taskmanager.test')->firstOrFail();
        $layla = User::query()->where('email', 'layla.employee@taskmanager.test')->firstOrFail();
        $khaled = User::query()->where('email', 'khaled.employee@taskmanager.test')->firstOrFail();
        $ahmed = User::query()->where('email', 'ahmed.manager@taskmanager.test')->firstOrFail();

        $yousef = User::query()->where('email', 'yousef.employee@taskmanager.test')->firstOrFail();
        $noura = User::query()->where('email', 'noura.employee@taskmanager.test')->firstOrFail();
        $sara = User::query()->where('email', 'sara.manager@taskmanager.test')->firstOrFail();

        $mobileApp = Project::query()->updateOrCreate(
            [
                'title' => 'Mobile App Launch',
                'company_id' => $techCorp->id,
            ],
            [
                'description' => 'Build and launch the company mobile application.',
                'status' => 'Abuilding',
                'project_manager_id' => $omar->id,
            ]
        );

        $apiRefactor = Project::query()->updateOrCreate(
            [
                'title' => 'API Refactor',
                'company_id' => $techCorp->id,
            ],
            [
                'description' => 'Refactor legacy APIs and improve performance.',
                'status' => 'Pending',
                'project_manager_id' => $omar->id,
            ]
        );

        $brandRefresh = Project::query()->updateOrCreate(
            [
                'title' => 'Brand Refresh',
                'company_id' => $creativeHub->id,
            ],
            [
                'description' => 'Redesign brand identity and marketing assets.',
                'status' => 'Abuilding',
                'project_manager_id' => $yousef->id,
            ]
        );

        $websiteRelaunch = Project::query()->updateOrCreate(
            [
                'title' => 'Website Relaunch',
                'company_id' => $creativeHub->id,
            ],
            [
                'description' => 'Relaunch company website with new UI.',
                'status' => 'Finished',
                'project_manager_id' => $yousef->id,
            ]
        );

        // Team members (unique project_user)
        $mobileApp->teamMembers()->syncWithoutDetaching([
            $omar->id,
            $layla->id,
            $khaled->id,
            $ahmed->id,
        ]);

        $apiRefactor->teamMembers()->syncWithoutDetaching([
            $omar->id,
            $khaled->id,
            $ahmed->id,
        ]);

        $brandRefresh->teamMembers()->syncWithoutDetaching([
            $yousef->id,
            $noura->id,
            $sara->id,
        ]);

        $websiteRelaunch->teamMembers()->syncWithoutDetaching([
            $yousef->id,
            $noura->id,
            $sara->id,
        ]);

        // Sample chat messages
        $chats = [
            [$mobileApp, $omar, 'Kickoff meeting done. Let us start sprint 1.'],
            [$mobileApp, $layla, 'UI screens for login are ready for review.'],
            [$mobileApp, $khaled, 'CI pipeline is green on staging.'],
            [$brandRefresh, $yousef, 'Please share the approved color palette.'],
            [$brandRefresh, $noura, 'First moodboard uploaded.'],
        ];

        foreach ($chats as [$project, $sender, $message]) {
            ChatMessage::query()->firstOrCreate(
                [
                    'project_id' => $project->id,
                    'sender_id' => $sender->id,
                    'message' => $message,
                ],
                [
                    'sent_at' => now()->subHours(rand(1, 48)),
                ]
            );
        }
    }
}
