<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],
        'mysql' => [
            'driver' => 'mysql',
			'read'=>[
				'host' => env('DB_HOST_READ', ''),
			],
			'write'=>[
				'host' => env('DB_HOST_WRITE', ''),
			],
            //'host' => env('DB_HOST', ''),
            'port' => env('DB_PORT', ''),
            'database' => env('DB_DATABASE', ''),
            'username' =>  env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
		
		'suvidhapc' => [
            'driver' => 'mysql',
			'read'=>[
				'host' => '10.247.137.76',
			],
			'write'=>[
				'host' => '10.247.137.75',
			],
            'port' => env('DB_PORT', '3306'),
            'database' => 'suvidha_pc_2024_05_e24',
            'username' =>  env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
       
        'suivhdalivetest' => [
            'driver' => 'mysql',
            'host' => env('DB_LIVE_TEST_HOST', ''),
            'port' => env('DB_LIVE_TEST_PORT', ''),
            'database' => env('DB_LIVE_TEST_DATABASE', ''),
            'username' =>  env('DB_LIVE_TEST_USERNAME', ''),
            'password' => env('DB_LIVE_TEST_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'suivhdaaclivetest' => [
            'driver' => 'mysql',
			'read'=>[
				'host' => '10.247.137.43',
			],
			'write'=>[
				'host' => '10.247.137.43',
			],
			'port' => '3306',
            'database' => env('DB_LIVE_TEST_DATABASE', ''),
            'username' => 'suvidhaapp',
            'password' => 'P7$b&n#367BYaRt91',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
			'sticky' => true,
        ],

        'boothapptest' => [
            'driver' => 'mysql',
            'host' => '10.247.137.49',
            'port' => '3306',
            'database' => 'suvidha_ac_2022_03_e13',
            'username' => 'suvidhaapp',
            'password' => 'P7$b&n#367BYaRt91',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_for_pc' => [
            'driver' => 'mysql',
            'host' => env('DB_PC_HOST', ''),
            'port' => env('DB_PC_PORT', ''),
            'database' => env('DB_PC_DATABASE', ''),
            'username' =>  env('DB_PC_USERNAME', ''),
            'password' => env('DB_PC_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'booth_revamp' => [
            'driver' => 'mysql',
            'host' => env('DB_BOOTHAPP_REVAMP_HOST', ''),
            'port' => env('DB_BOOTHAPP_REVAMP_PORT', ''),
            'database' => env('DB_BOOTHAPP_REVAMP_DATABASE', ''),
            'username' =>  env('DB_BOOTHAPP_REVAMP_USERNAME', ''),
            'password' => env('DB_BOOTHAPP_REVAMP_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'booth_revamp_write' => [
            'driver' => 'mysql',
            'host' => env('DB_BOOTHAPP_REVAMP_WRITE_HOST', ''),
            'port' => env('DB_BOOTHAPP_REVAMP_WRITE_PORT', ''),
            'database' => env('DB_BOOTHAPP_REVAMP_WRITE_DATABASE', ''),
            'username' =>  env('DB_BOOTHAPP_REVAMP_WRITE_USERNAME', ''),
            'password' => env('DB_BOOTHAPP_REVAMP_WRITE_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'booth_revamp_test' => [
            'driver' => 'mysql',
            'host' => env('DB_BOOTHAPP_REVAMP_TEST_HOST', ''),
            'port' => env('DB_BOOTHAPP_REVAMP_TEST_PORT', ''),
            'database' => env('DB_BOOTHAPP_REVAMP_TEST_DATABASE', ''),
            'username' =>  env('DB_BOOTHAPP_REVAMP_TEST_USERNAME', ''),
            'password' => env('DB_BOOTHAPP_REVAMP_TEST_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'booth_revamp_test_write' => [
            'driver' => 'mysql',
            'host' => env('DB_BOOTHAPP_REVAMP_TEST_WRITE_HOST', ''),
            'port' => env('DB_BOOTHAPP_REVAMP_TEST_WRITE_PORT', ''),
            'database' => env('DB_BOOTHAPP_REVAMP_TEST_WRITE_DATABASE', ''),
            'username' =>  env('DB_BOOTHAPP_REVAMP_TEST_WRITE_USERNAME', ''),
            'password' => env('DB_BOOTHAPP_REVAMP_TEST_WRITE_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'spm' => [
            'driver' => 'mysql',
            'host' => '10.246.24.6',
            'port' => '3306',
            'database' => 'spms_booth',
            'username' => 'spm_read',
            'password' => 'S9m@slct472',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'mysql_database_history' => [
            'driver' => 'mysql',
            'read'=>[
				'host' => env('DB_HOST_READ', ''),
			],
			'write'=>[
				'host' => env('DB_HOST_WRITE', ''),
			],
            'port' => env('DB_PORT', ''),
            'database' => env('DB_DATABASE', ''),
            'username' =>  env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],
        'central' => [
            'driver' => 'mysql',
            'host' => env('DB_CENTRAL_HOST', ''),
            'port' => env('DB_AC_PORT', ''),
            'database' => env('DB_AC_DATABASE', ''),
            'username' =>  env('DB_AC_USERNAME', ''),
            'password' => env('DB_AC_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        'vtpt' => [
            'driver' => 'mysql',
            'host' => env('DB_VTPT_HOST', '127.0.0.1'),
            'port' => env('DB_VTPT_PORT', '5432'),
            'database' => env('DB_VTPT_DATABASE', 'forge'),
            'username' => env('DB_VTPT_USERNAME', 'forge'),
            'password' => env('DB_VTPT_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'mysql_suvidhaac_database' => [
            'driver' => 'mysql',
            'read'=>[
                'DB_HOST_SUVIDHA_READ' => '10.247.137.75',
            ],
            'write'=>[
                'DB_HOST_SUVIDHA_WRITE' => '10.247.137.75',
            ],
            'host' => env('DB_HOST_SUVIDHA', ''),
            'port' => env('DB_PORT_SUVIDHA', ''),
            'database' => env('DB_DATABASE_SUVIDHA', ''),
            'username' =>  env('DB_DATABASE_SUVIDHA_USERNAME', ''),
            'password' => env('DB_DATABASE_SUVIDHA_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
