<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUsers extends Command
{
    protected $signature = 'users:show';

    protected $description = 'Display users and their profile, posts, and roles';

    public function handle(): int
    {
        $users = User::with(['profile', 'posts', 'roles'])
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');

            return self::SUCCESS;
        }

        $rows = $users->map(fn (User $user) => [
            $user->name,
            $user->email,
            $user->profile?->bio ?? 'None',
            $user->posts->pluck('title')->implode(', ') ?: 'None',
            $user->roles->pluck('name')->implode(', ') ?: 'None',
        ]);

        $this->table(['Name', 'Email', 'Profile bio', 'Posts', 'Roles'], $rows);

        return self::SUCCESS;
    }
}
