<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class HashAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:hash-password {password? : Wird abgefragt, falls nicht angegeben}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erzeugt einen Passwort-Hash zum Eintragen als ADMIN_PASSWORD_HASH in der .env';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $password = $this->argument('password') ?? $this->secret('Neues Admin-Passwort');

        $this->line(Hash::make($password));
        $this->info('Diesen Wert als ADMIN_PASSWORD_HASH in die .env eintragen.');

        return self::SUCCESS;
    }
}
