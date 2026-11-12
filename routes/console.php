<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('superadmin:change-password {email=superadmin@example.com} {--password=}', function ($email) {
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        $this->error("User with email {$email} not found.");
        return 1;
    }

    $password = $this->option('password') ?: \Illuminate\Support\Str::password(16, letters: true, numbers: true, symbols: true, spaces: false);
    $user->password = \Illuminate\Support\Facades\Hash::make($password);
    $user->save();

    $this->info("Password for Super Admin [{$user->name} <{$user->email}>] updated successfully.");
    $this->line("New Password: {$password}");

    return 0;
})->purpose('Change or reset the password for a superadmin account');

Artisan::command('employee:reset-passwords {--id=} {--password=Neural@2026!} {--pin=1234}', function () {
    $id = $this->option('id');
    $password = $this->option('password');
    $pin = $this->option('pin');

    $query = \App\Models\Employee::query();
    if ($id) {
        $query->where('employee_id', $id);
    }

    $count = 0;
    foreach ($query->cursor() as $emp) {
        $emp->employee_password = \Illuminate\Support\Facades\Hash::make($password);
        if ($pin) {
            $emp->login_pin = \Illuminate\Support\Facades\Hash::make($pin);
            $emp->is_pin_enabled = 1;
            $emp->pin_set_at = now();
        }
        $emp->status = 'Active';
        $emp->save();
        $count++;
    }

    $this->info("Successfully reset credentials for {$count} employee(s).");
    $this->line("New Password : {$password}");
    if ($pin) {
        $this->line("New Mobile PIN: {$pin}");
    }

    return 0;
})->purpose('Batch reset or update password and PIN for employees');

Artisan::command('client:reset-password {email=mahendra.s@neuralinfo.org} {--password=Client@2026!}', function ($email) {
    $password = $this->option('password') ?: 'Client@2026!';
    $client = \App\Models\Client::where('email', $email)->first();
    if (!$client) {
        $this->error("Client with email {$email} not found.");
        return 1;
    }

    $client->password = \Illuminate\Support\Facades\Hash::make($password);
    $client->status = 'Active';
    $client->save();

    $this->info("Password for Client [{$client->client_name} <{$client->email}>] updated successfully.");
    $this->line("New Password: {$password}");

    return 0;
})->purpose('Reset password for a client portal account');



