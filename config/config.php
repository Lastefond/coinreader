<?php
return [
'coinreader' => [
        'device' => '/dev/serial0',
        'config' => 'cs8 9600 ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts',
    ],
];
