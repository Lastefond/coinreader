<?php

require 'vendor/autoload.php';
require 'extensions/CoinProxy.php';

use React\EventLoop\Factory;
use mikk150\serial\SerialEmitter;
use Ratchet\App;

$config = require 'config/config.php';

$loop = Factory::create();

$coinProxy = new CoinProxy;

$serialEmitter = new SerialEmitter($loop, $config['coinreader']['device'], $config['coinreader']['baudrate']);
$serialEmitter->on('data', function ($data) use ($coinProxy) {
    $coin = ord($data);
    if ($coin !== 255) {
        $coinProxy->broadcast($coin);
    }
});

$app = new Ratchet\App('localhost', 8080, '127.0.0.1', $loop);
$app->route('/coins', $coinProxy);

$app->run();
