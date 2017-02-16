<?php

require 'vendor/autoload.php';
require 'extensions/SerialEmitter.php';

use React\EventLoop\Factory;

$config = require 'config/config.php';

$loop = Factory::create();
// $serialEmitter = new SerialEmitter($loop, '/dev/ttyUSB0', 2400);
// $serialEmitter->on('data', function ($data) {
//     echo $data;
// });
// 

$fd = dio_open('/dev/ttyUSB0', O_RDWR | O_NOCTTY | O_NONBLOCK);

dio_fcntl($fd, F_SETFL, O_SYNC);

dio_tcsetattr($fd, array(
  'baud' => 2400,
  'bits' => 8,
  'stop'  => 1,
  'parity' => 0
)); 

while (1) {

  $data = dio_read($fd, 256);

  if ($data) {
      echo ord($data);
  }
} 

$loop->run();
