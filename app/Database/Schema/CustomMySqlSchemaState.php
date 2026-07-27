<?php

namespace App\Database\Schema;

use Illuminate\Database\Schema\MySqlSchemaState;

class CustomMySqlSchemaState extends MySqlSchemaState
{
    /**
     * Load the given schema file into the database.
     *
     * @param  string  $path
     * @return void
     */
    public function load($path)
    {
        $command = 'mysql '.$this->connectionString().' --skip-ssl --database="${:LARAVEL_LOAD_DATABASE}" < "${:LARAVEL_LOAD_PATH}"';

        $process = $this->makeProcess($command)->setTimeout(null);

        $process->mustRun(null, array_merge($this->baseVariables($this->connection->getConfig()), [
            'LARAVEL_LOAD_PATH' => $path,
        ]));
    }

    /**
     * Get the base dump command arguments for MySQL as a string.
     *
     * @return string
     */
    protected function baseDumpCommand()
    {
        $command = 'mysqldump '.$this->connectionString().' --skip-ssl --no-tablespaces --skip-add-locks --skip-comments --skip-set-charset --tz-utc --column-statistics=0';

        if (! $this->connection->isMaria()) {
            $command .= ' --set-gtid-purged=OFF';
        }

        return $command.' "${:LARAVEL_LOAD_DATABASE}"';
    }
}