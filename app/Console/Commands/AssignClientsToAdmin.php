<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class AssignClientsToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-clients-to-admin {admin_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attribue tous les clients sans user_id à l\'administrateur spécifié';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminId = $this->argument('admin_id');

        $clientsWithoutUser = Client::whereNull('user_id')->get();
        $count = $clientsWithoutUser->count();

        if ($count === 0) {
            $this->info('Aucun client sans user_id trouvé.');
            return 0;
        }

        $this->info("Attribution de {$count} clients à l'utilisateur ID {$adminId}...");

        foreach ($clientsWithoutUser as $client) {
            $client->user_id = $adminId;
            $client->save();
        }

        $this->info('Terminé avec succès !');
        return 0;
    }
}
