<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Socket;

require dirname( __FILE__ ) . '/vendor/autoload.php';
require_once('db-config.php');

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Socket($conn)
        )
    ),
    8080
);

$server->run();
?>