<?php

namespace app\Actions;

use \Dotenv\Dotenv;

class BuildDbConfigurationAction
{
    public function execute()
    {
        $env = Dotenv::createArrayBacked(base_path())->load();

        $config = [];

        foreach (array_keys($env) as $variableName) {
            if (str_starts_with($variableName, 'SERVER_DB_NAME_')) {
                $connectionFragments = explode('_', $variableName);
                $connectionNumber = end($connectionFragments);

                $config[$this->getVariable($env, 'SERVER_DB_NAME', $connectionNumber)] = [
                    'driver' => 'mysql',
                    'url' => $this->getVariable($env, 'SERVER_DATABASE_URL', $connectionNumber),
                    'host' => $this->getVariable($env, 'SERVER_DB_HOST', $connectionNumber),
                    'port' => $this->getVariable($env, 'SERVER_DB_PORT', $connectionNumber),
                    'database' => $this->getVariable($env, 'SERVER_DB_DATABASE', $connectionNumber),
                    'username' => $this->getVariable($env, 'SERVER_DB_USERNAME', $connectionNumber),
                    'password' => $this->getVariable($env, 'SERVER_DB_PASSWORD', $connectionNumber),
                    'unix_socket' => $this->getVariable($env, 'SERVER_DB_SOCKET', $connectionNumber),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                    'options' => extension_loaded('pdo_mysql') ? array_filter([
                        \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    ]) : [],
                ];
            }
        }

        return $config;
    }

    private function getVariable(array $env, string $variableName, string $connectionNumber): ?string 
    {
        $fullVariableName = $variableName . '_' . $connectionNumber;

        if (array_key_exists($fullVariableName, $env)){
            return $env[$fullVariableName];
        }

        return null;
    }
}
