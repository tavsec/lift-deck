<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateCoach extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-coach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates new coach';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Coach name');
        $email = $this->ask('Coach email');
        $password = $this->secret('Coach password');

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'coach'
        ]);

        $this->info('Coach created successfully');
    }
}
