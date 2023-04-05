<?php

namespace App\Services\Progress;

use Illuminate\Database\Connectors\Connector;
use Illuminate\Database\Connectors\ConnectorInterface;

class ProgressConnector extends Connector implements ConnectorInterface
{
    /**
     * @param array $config
     *
     * @return \PDO
     */
    public function connect(array $config)
    {
        // First we'll create the basic DSN and connection instance connecting to the
        // using the configuration option specified by the developer.
        $connection = $this->createConnection(
            $this->getDsn($config),
            $config,
            $this->getOptions($config)
        );

        return $connection;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    protected function getDsn(array $config)
    {
        // First we will create the basic DSN setup as well as the port if it is in
        // in the configuration options. This will give us the basic DSN we will
        // need to establish the PDO connections and return them back for use.
        extract($config, EXTR_SKIP);

        $dsn = 'odbc:'.
               'Driver='.($systemdriver ?? 'Progress').
               ';DatabaseName='.($database ?? '').
               ';Hostname='.($host ?? '');

        // If a port was specified, we will add it to this Progress DSN connections
        // format.
        if (isset($port)) {
            $dsn .= ";PortNumber={$port}";
        }

        // If a codepage as specified, we will add it to this Progress DSN connections
        // format. Once we have done that we are ready to return this connection
        // string back out for usage, as this has been fully constructed here.
        if (isset($codepage)) {
            $dsn .= ";IANAAppCodePage={$codepage}";
        }

        return $dsn;
    }

    /**
     * Format the schema for the DSN.
     *
     * @param array|string $schema
     *
     * @return string
     */
    protected function formatSchema($schema)
    {
        if (is_array($schema)) {
            return '"'.implode('", "', $schema).'"';
        }

        return '"'.$schema.'"';
    }
}
