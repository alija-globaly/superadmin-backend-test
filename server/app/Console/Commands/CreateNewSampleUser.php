<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Str;

class CreateNewSampleUser extends Command
{
    protected $signature = 'create:sample-user';
    protected $description = 'Create a new sample user with custom input';

    public function handle()
    {
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the user\'s password?');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'Admin',
            'status' => 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $this->info("User created successfully!");
        $this->info("Name: {$user->name}");
        $this->info("Email: {$user->email}");
    }
}
