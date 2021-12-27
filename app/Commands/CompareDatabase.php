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

        $connections = config()->get('database.connections');
        $connectionsKeys = array_keys($connections);

        $firstConnection = $this->choice("Selecione a primeira conexão com o banco de dados", $connectionsKeys);

        $databasesFromFirstConnection = DB::connection($firstConnection)->select("SHOW DATABASES");

        $availableDatabasesFromFirstConnection = [];

        foreach ($databasesFromFirstConnection as $database) {
            $availableDatabasesFromFirstConnection[] = $database->Database;
        }

        $firstDatabase = $this->choice("Os seguintes bancos da conexão '$firstConnection' estão disponíveis: ", $availableDatabasesFromFirstConnection);

        $secondConnection = $this->choice("Selecione a segunda conexão com o banco de dados", $connectionsKeys);

        $databasesFromSecondConnection = DB::connection($secondConnection)->select("SHOW DATABASES");

        $availableDatabasesFromSecondConnection = [];

        foreach ($databasesFromSecondConnection as $database) {
            $availableDatabasesFromSecondConnection[] = $database->Database;
        }

        $secondDatabase = $this->choice("Os seguintes bancos da conexão '$secondConnection' estão disponíveis: ", $availableDatabasesFromSecondConnection);

        $connectionConfig = $connections[$firstConnection];
        $firstServer = "{$connectionConfig['username']}:{$connectionConfig['password']}@{$connectionConfig['host']}:{$connectionConfig['port']}";

        $connectionConfig = $connections[$secondConnection];
        $secondServer = "{$connectionConfig['username']}:{$connectionConfig['password']}@{$connectionConfig['host']}:{$connectionConfig['port']}";

        $command = "php ." . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ["vendor", "dbdiff", "dbdiff", "dbdiff"]) . " server1.$firstDatabase:server2.$secondDatabase --server1=$firstServer --server2=$secondServer --output=$firstDatabase-$secondDatabase.sql";
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
