<?php

namespace App\Services\Progress;

use App\Services\Progress\Query\Grammer\ProgressGrammer;
use Illuminate\Database\Connection;
use PDO;

class ProgressConnection extends Connection
{
    /**
     * @param \PDO   $pdo
     * @param string $database
     * @param string $tablePrefix
     * @param array  $config
     */
    public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);
    }

    /**
     * @return \Illuminate\Database\Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        $defaultGrammar = new ProgressGrammer(
            $this->config
        );

        // set date format if any specified
        if (array_key_exists('date_format', $this->config)) {
            $defaultGrammar->setDateFormat($this->config['date_format']);
        }

        return $this->withTablePrefix($defaultGrammar);
    }
}
