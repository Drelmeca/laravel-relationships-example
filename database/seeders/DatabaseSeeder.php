<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ManyToManyRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'Genses@example.com'],
            ['name' => 'Genses Robinson'],
        );

        $user->profile()->firstOrCreate([], [
            'bio' => 'I enjoy writing about technology and design.',
            'website' => 'https://example.com',
        ]);

        $user->posts()->firstOrCreate(
            ['title' => 'Getting started with Laravel relationships'],
            ['body' => 'This post belongs to Alex and demonstrates a one-to-many relationship.'],
        );

        $role = ManyToManyRole::firstOrCreate(['name' => 'Editor']);
        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
