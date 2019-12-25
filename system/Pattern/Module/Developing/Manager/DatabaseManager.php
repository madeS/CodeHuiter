<?php

namespace CodeHuiter\Pattern\Module\Developing\Manager;

use CodeHuiter\Config\RelationalDatabaseConfig;

class DatabaseManager
{
    public function saveDumpDB(RelationalDatabaseConfig $config, string $directory, ?string $name = null): void
    {
        $host = $this->getDsnAttribute('host', $config->dsn);
        $database = $this->getDsnAttribute('dbname', $config->dsn);
        $charset = $this->getDsnAttribute('charset', $config->dsn) ?? $config->charset;
        $user = $config->username;
        $password = $config->password;
        $filename = $directory . ($name ? $name : $database) . '.dump.sql';

        exec(
            "export MYSQL_PWD=$password ;mysqldump -h $host -u $user $database --add-drop-table --skip-add-locks --default-character-set=$charset --single-transaction > $filename",
            $output,
            $returnVar
        );
    }

    public function loadDumpDB(RelationalDatabaseConfig $config, string $directory, ?string $name = null): void
    {
        $host = $this->getDsnAttribute('host', $config->dsn);
        $database = $this->getDsnAttribute('dbname', $config->dsn);
        $user = $config->username;
        $password = $config->password;
        $filename = $directory . ($name ? $name : $database) . '.dump.sql';

        exec(
//            "mysql -h $host -u $user -p$password -e \"
//                    DROP DATABASE IF EXISTS $database;
//                    CREATE database $database;
//                    USE $database;
//                    SOURCE $filename;
//            \"",
            "export MYSQL_PWD=$password ;mysql -h $host -u $user -e \"
                    USE $database; 
                    SOURCE $filename;
            \"",
            $output,
            $returnVar
        );
    }

    /**
     * @param string $name
     * @param string $dsn
     * @return string|null
     */
    private function getDsnAttribute(string $name, string $dsn): ?string
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        }
        return null;
    }
}