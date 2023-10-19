<?php

use Laravel\Octane\Contracts\OperationTerminated;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use Laravel\Octane\Listeners\EnsureUploadedFilesAreValid;
use Laravel\Octane\Listeners\EnsureUploadedFilesCanBeMoved;
use Laravel\Octane\Listeners\FlushTemporaryContainerInstances;
use Laravel\Octane\Listeners\ReportException;
use Laravel\Octane\Listeners\StopWorkerIfNecessary;
use Laravel\Octane\Octane;

return [

    /*
    |--------------------------------------------------------------------------
    | Octane Server
    |--------------------------------------------------------------------------
    |
    | This value determines the default "server" that will be used by Octane
    | when starting, restarting, or stopping your server via the CLI. You
    | are free to change this to the supported server of your choosing.
    |
    | Supported: "roadrunner", "swoole"
    |
    */

    'server' => env('OCTANE_SERVER', 'swoole'),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | When this configuration value is set to "true", Octane will inform the
    | framework that all absolute links must be generated using the HTTPS
    | protocol. Otherwise your links may be generated using plain HTTP.
    |
    */

    'https' => env('OCTANE_HTTPS', false),

    /*
    |--------------------------------------------------------------------------
    | Octane Listeners
    |-----------------------------------------------()->-------------------------
    |
    | All of the event listeners for Octane's events are defined below. These
    | listeners are responsible for resetting your application's state for
    | the next request. You may even add your own listeners to the list.
    |
    */

    'listeners' => [
        WorkerStarting::class => [
            EnsureUploadedFilesAreValid::class,
            EnsureUploadedFilesCanBeMoved::class,
        ],

        RequestReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            ...Octane::prepareApplicationForNextRequest(),
            //
        ],

        RequestHandled::class => [
            //
        ],

        RequestTerminated::class => [
            //
        ],

        TaskReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            //
        ],

        TaskTerminated::class => [
            //
        ],

        TickReceived::class => [
            ...Octane::prepareApplicationForNextOperation(),
            //
        ],

        TickTerminated::class => [
            //
        ],

        OperationTerminated::class => [
            FlushTemporaryContainerInstances::class,
            // DisconnectFromDatabases::class,
            // CollectGarbage::class,
        ],

        WorkerErrorOccurred::class => [
            ReportException::class,
            StopWorkerIfNecessary::class,
        ],

        WorkerStopping::class => [
            //
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Warm / Flush Bindings
    |--------------------------------------------------------------------------
    |
    | The bindings listed below will either be pre-warmed when a worker boots
    | or they will be flushed before every new request. Flushing a binding
    | will force the container to resolve that binding again when asked.
    |
    */

    'warm' => [
        ...Octane::defaultServicesToWarm(),
    ],

    'flush' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Cache Table
    |--------------------------------------------------------------------------
    |
    | While using Swoole, you may leverage the Octane cache, which is powered
    | by a Swoole table. You may set the maximum number of rows as well as
    | the number of bytes per row using the configuration options below.
    |
    */

    'cache' => [
        'rows' => env('OCTANE_CACHE_ROWS', 1000),
        'bytes' => env('OCTANE_CACHE_ROW_BYTE', 102400),
    ],

    /*
    |--------------------------------------------------------------------------
    | Octane Swoole Tables
    |--------------------------------------------------------------------------
    |
    | While using Swoole, you may define additional tables as required by the
    | application. These tables can be used to store data that needs to be
    | quickly accessed by other workers on the particular Swoole server.
    |
    */

    'tables' => [
        'example:1000' => [
            'name' => 'string:1000',
            'votes' => 'int',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Watching
    |--------------------------------------------------------------------------
    |
    | The following list of files and directories will be watched when using
    | the --watch option offered by Octane. If any of the directories and
    | files are changed, Octane will automatically reload your workers.
    |
    */

    'watch' => [
        'app',
        'bootstrap',
        'config',
        'database',
        'public/**/*.php',
        'public/*.json',
        'resources/**/*.php',
        'routes',
        'composer.lock',
        '.env',
    ],

    /*
    |--------------------------------------------------------------------------
    | Garbage Collection Threshold
    |--------------------------------------------------------------------------
    |
    | When executing long-lived PHP scripts such as Octane, memory can build
    | up before being cleared by PHP. You can force Octane to run garbage
    | collection if your application consumes this amount of megabytes.
    |
    */

    'garbage' => 50,

    /*
    |--------------------------------------------------------------------------
    | Maximum Execution Time
    |--------------------------------------------------------------------------
    |
    | The following setting configures the maximum execution time for requests
    | being handled by Octane. You may set this value to 0 to indicate that
    | there isn't a specific time limit on Octane request execution time.
    |
    */

    'max_execution_time' => env('OCTANE_MAX_EXECUTION_TIME', 30),

    'defaultServerOptions' => [
        'enable_coroutine' => true,
    ],
    'swoole'               => [
        'options' => [
//            'task_enable_coroutine' => true,
            'log_file'                       => storage_path('logs/swoole.http.log'),
            'package_max_length'             => 10 * 1024 * 1024,
            // Enable TCP Keep-Alive check
            'open_tcp_keepalive'             => true,
            // Check if there is no data for 4s (the time a connection needs to remain idle before TCP starts sending keep alive probes)
            'tcp_keepidle'                   => 4,
            //In seconds, the time between individual keep alive probes/checks
            'tcp_keepinterval'               => 1,   // Check if there is data every 1s
            //The maximum number of keep alive probes/checks to send before dropping the connection, classing it as dead or broken
            'tcp_keepcount'                  => 5,      // Close the connection if there is no data for 5 cycles.
            //Set this configuration to true to use the Nagle merge algorithm.
            //This option attempts to improve the efficiency of TCP/IP by reducing the number of packets that need to be sent.
            'open_tcp_nodelay'               => true,

            // Kernel
            'backlog'                        => 10000,
            'kernel_socket_send_buffer_size' => 65535,
            'kernel_socket_recv_buffer_size' => 65535,

            /**
             * This configuration is the interval of when to poll every TCP connection to see if it is idle.
             * Default value is false, set in seconds.
             * If the connection hasn't sent any data to the server in the last interval of heartbeat_check_interval,
             * the connection will be closed,
             * this option works with heartbeat_idle_time which decides if a connection is idle or not.
             * The Swoole server does not send the heartbeat packet to the client,
             * it waits for the heartbeat packet from the client.
             * The heartbeat check thatis done by the Swoole server only checks the last time data has been
             * received from the client. If the time exceeds heartbeat_idle_time,
             * the connection between the server and the client will be closed.
             * When a connection is closed due to breaching a heartbeat check, it will trigger the call to onClose.
             * This is only for a TCP Swoole server.
             */
            'heartbeat_idle_time'            => 40,
            'heartbeat_check_interval'       => 60,
            'daemonize'                      => false,

            // Coroutine
            'enable_coroutine'               => true,
        ],
    ]

];
