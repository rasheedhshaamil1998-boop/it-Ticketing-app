<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('helpdesk:create-admin {--name=} {--email=} {--department=IT} {--role=superadmin}')]
#[Description('Create or promote a helpdesk administrator or super administrator.')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = (string) ($this->option('name') ?: $this->ask('Full name'));
        $email = (string) ($this->option('email') ?: $this->ask('Email address'));
        $department = (string) $this->option('department');
        $role = (string) $this->option('role');
        $password = (string) $this->secret('Password');

        if (! in_array($role, ['admin', 'superadmin'], true)) {
            $this->error('Role must be admin or superadmin.');

            return self::FAILURE;
        }

        if ($password === '') {
            $this->error('Password is required.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'department' => $department,
                'password' => $password,
                'role' => $role,
                'is_active' => true,
            ],
        );

        $this->info("{$user->roleLabel()} {$user->email} is ready.");

        return self::SUCCESS;
    }
}
