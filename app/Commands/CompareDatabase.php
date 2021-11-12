<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class CompareDatabase extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'db:compare';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->line("Buscando bancos de dados disponíveis...");
        $databases = DB::select("SHOW DATABASES");


        $availableDatabases = [];

        foreach ($databases as $database) {
            $availableDatabases[] = $database->Database;
        }

        $firstDatabase = $this->choice("Os seguintes bancos estão disponíveis: ", $availableDatabases);
        $secondDatabase = $this->choice("Selecione por indíce o segundo banco para comparação com o primeiro", $availableDatabases);

        $databaseConfig = config()->get('database');
        $connection = $databaseConfig['default'];
        $connectionConfig = $databaseConfig['connections'][$connection];
        $server = "{$connectionConfig['username']}:{$connectionConfig['password']}@{$connectionConfig['host']}:{$connectionConfig['port']}";

        $command = "vendor/dbdiff/dbdiff/dbdiff server1.$firstDatabase:server2.$secondDatabase --server1=$server --server2=$server --output=$firstDatabase-$secondDatabase.sql";
        $output = shell_exec($command);
        $this->line("Resposta após rodar o comando: ");
        $this->newLine();
        echo $output;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        //
    }
}
