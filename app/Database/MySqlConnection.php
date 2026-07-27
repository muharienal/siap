<?php

namespace App\Database;

use App\Database\Schema\CustomMySqlSchemaState;
use Illuminate\Database\MySqlConnection as BaseMySqlConnection;
use Illuminate\Filesystem\Filesystem;

class MySqlConnection extends BaseMySqlConnection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @param  \Illuminate\Filesystem\Filesystem|null  $files
     * @param  callable|null  $processFactory
     * @return \App\Database\Schema\CustomMySqlSchemaState
     */
    public function getSchemaState(?Filesystem $files = null, ?callable $processFactory = null)
    {
        return new CustomMySqlSchemaState($this, $files, $processFactory);
    }
}