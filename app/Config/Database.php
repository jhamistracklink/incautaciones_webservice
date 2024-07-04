<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     */
    // public array $default = [
    //     'DSN'          => '',
    //     'hostname'     => 'localhost',
    //     'username'     => '',
    //     'password'     => '',
    //     'database'     => '',
    //     'DBDriver'     => 'MySQLi',
    //     'DBPrefix'     => '',
    //     'pConnect'     => false,
    //     'DBDebug'      => true,
    //     'charset'      => 'utf8',
    //     'DBCollat'     => 'utf8_general_ci',
    //     'swapPre'      => '',
    //     'encrypt'      => false,
    //     'compress'     => false,
    //     'strictOn'     => false,
    //     'failover'     => [],
    //     'port'         => 3306,
    //     'numberNative' => false,
    // ];

    //BSOFT prod: 
    //'hostname'     => '192.168.2.8',
    // 'username'     => 'bsoft-user',
    //'password'     => '$Gt&bft3SLhx-5PrKs=',

    //BSOFT PRUEBAS: 
    //'hostname'     => '192.168.2.172',
    // 'username'     => 'sa',
    //'password'     => 'TrackAdmin1',
    public array $default = [
        'DSN'          => '',
        'hostname'     => '192.168.2.8',
        'username'     => 'bsoft-user',
        'password'     => '$Gt&bft3SLhx-5PrKs=',
        'database'     => 'BSOFT',
        'DBDriver'     => 'SQLSRV', 
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        // 'port'         => 3306,
        'numberNative' => false,
    ];

    public array $onyx = [
        'DSN'          => '',
        'hostname'     => '192.168.2.7',
        'username'     => 'sidi',
        'password'     => 'sidi',
        'database'     => 'sidi_peru',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    public array $trackdb = [
        'DSN'          => '',
        'hostname'     => '192.168.2.20',
        'username'     => 'pvasquez',
        'password'     => 'pvasquez!',
        'database'     => 'trackdb',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
