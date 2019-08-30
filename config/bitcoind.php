<?php

return [

    'default' => [
        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC Scheme
        |--------------------------------------------------------------------------
        | URI scheme of Bitcoin Core's JSON-RPC server.
        |
        | Use 'https' scheme for SSL connection.
        | Note that you'll need to setup secure tunnel or reverse proxy
        | in order to access Bitcoin Core via SSL.
        | See: https://bitcoin.org/en/release/v0.12.0#rpc-ssl-support-dropped
        |
        */

        'scheme' => env('BITCOIND_SCHEME', ''),

        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC Host
        |--------------------------------------------------------------------------
        | Tells service provider which hostname or IP address
        | Bitcoin Core is running at.
        |
        | If Bitcoin Core is running on the same PC as
        | laravel project use localhost or 127.0.0.1.
        |
        | If you're running Bitcoin Core on the different PC,
        | you may also need to add rpcallowip=<server-ip-here> to your bitcoin.conf
        | file to allow connections from your laravel client.
        |
        */

        #'host' => env('BITCOIND_HOST', '206.189.80.162/init.php'),
        'host' => env('BITCOIND_HOST', ''),
        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC Port
        |--------------------------------------------------------------------------
        | The port at which Bitcoin Core is listening for JSON-RPC connections.
        | Default is 8332 for mainnet and 18332 for testnet.
        | You can also directly specify port by adding rpcport=<port>
        | to bitcoin.conf file.
        |
        */

        #'port' => env('BITCOIND_PORT', '18332'),
        'port' => env('BITCOIND_PORT', ''),

        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC User
        |--------------------------------------------------------------------------
        | Username needs to be set exactly as in bitcoin.conf file
        | rpcuser=<username>.
        |
        */

        //'user' => env('BITCOIND_USER', 'bapp2blockusr1'),
        'user' => env('BITCOIND_USER', ''),

        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC Password
        |--------------------------------------------------------------------------
        | Password needs to be set exactly as in bitcoin.conf file
        | rpcpassword=<password>.
        |
        */

        //'password' => env('BITCOIND_PASSWORD', 'd4mny07moth3rf4th312'),
        'password' => env('BITCOIND_PASSWORD', ''),

        /*
        |--------------------------------------------------------------------------
        | Bitcoind JSON-RPC Server CA
        |--------------------------------------------------------------------------
        | If you're using SSL (https) to connect to your Bitcoin Core
        | you can specify custom ca package to verify against.
        | Note that you'll need to setup secure tunnel or reverse proxy
        | in order to access Bitcoin Core via SSL.
        | See: https://bitcoin.org/en/release/v0.12.0#rpc-ssl-support-dropped
        |
        */

        'ca' => null,

        /*
        |--------------------------------------------------------------------------
        | Preserve method name case.
        |--------------------------------------------------------------------------
        | Keeps method name case as defined in code when making a request,
        | instead of lowercasing them.
        | When this option is set to true, bitcoind()->getBlock()
        | request will be sent to server as 'getBlock', when set to false
        | method name will be lowercased to 'getblock'.
        | For Bitcoin Core leave as default(false), for ethereum
        | JSON-RPC API this must be set to true.
        |
        */
        'preserve_case' => false,

        /*
        |--------------------------------------------------------------------------
        | Bitcoind ZeroMQ options
        |--------------------------------------------------------------------------
        | Used to subscribe to zeromq topics pushed by daemon.
        | In order to use this you mush install "denpa\laravel-zeromq" package,
        | have Bitcoin Core with zeromq support included and have zmqpubhashtx,
        | zmqpubhashblock, zmqpubrawblock and zmqpubrawtx options defined
        | in bitcoind.conf.
        | For more information
        | visit https://laravel-bitcoinrpc.denpa.pro/docs/zeromq/
        |
        */

        'zeromq' => [
            'host' => 'localhost',
            'port' => 28332,
        ],
    ],

    'bitcoin' => [
        'scheme'        => env('BITCOIND_SCHEME', ''),
        'host'          => env('BITCOIND_HOST', ''),
        'port'          => env('BITCOIND_PORT', ''),
        'user'          => env('BITCOIND_USER', ''),
        'password'      => env('BITCOIND_PASSWORD', ''),
        'ca'            => null,
        'preserve_case' => false,
        'zeromq'        => null,
    ],

    'bitabc' => [
        'scheme'        => env('BITABCD_SCHEME', ''),
        'host'          => env('BITABCD_HOST', ''),
        'port'          => env('BITABCD_PORT', ''),
        'user'          => env('BITABCD_USER', ''),
        'password'      => env('BITABCD_PASSWORD', ''),
        'ca'            => null,
        'preserve_case' => false,
        'zeromq'        => null,
    ],

    'dashcoin' => [
        'scheme'        => env('DASHD_SCHEME', ''),
        'host'          => env('DASHD_HOST', ''),
        'port'          => env('DASHD_PORT', ''),
        'user'          => env('DASHD_USER', ''),
        'password'      => env('DASHD_PASSWORD', ''),
        'ca'            => null,
        'preserve_case' => false,
        'zeromq'        => null,
    ],

    'dogecoin' => [
        'scheme'        => env('DOGED_SCHEME', ''),
        'host'          => env('DOGED_HOST', ''),
        'port'          => env('DOGED_PORT', ''),
        'user'          => env('DOGED_USER', ''),
        'password'      => env('DOGED_PASSWORD', ''),
        'ca'            => null,
        'preserve_case' => false,
        'zeromq'        => null,
    ],


    'litecoin' => [
        'scheme'        => env('LITECOIND_SCHEME', ''),
        'host'          => env('LITECOIND_HOST', ''),
        'port'          => env('LITECOIND_PORT', ''),
        'user'          => env('LITECOIND_USER', ''),
        'password'      => env('LITECOIND_PASSWORD', ''),
        'ca'            => null,
        'preserve_case' => false,
        'zeromq'        => null,
    ],

];
