<?php

use Evenement\EventEmitter;

use React\Stream\Stream as FileStreamer;
use React\EventLoop\LoopInterface;

/**
*
*/
class SerialEmitter extends EventEmitter
{
    protected $loop;
    
    protected $fileStreamer;

    public function __construct(LoopInterface $loop, $device, $baudrate)
    {
        $this->configureDevice($device, $baudrate);

        $stream = fopen($device, 'r+');

        while ($line = fread($stream, 1)) {
            echo $line;
        }

        $this->fileStreamer = new FileStreamer($stream, $loop);

        $that = $this;

        $this->fileStreamer->on('error', function ($error) use ($that) {
            echo 'data';
            $that->emit('error', [$error, $that]);
        });
        $this->fileStreamer->on('drain', function () use ($that) {
            echo 'data';
            $that->emit('drain', [$that]);
        });

        $this->fileStreamer->on('data', function ($data) use ($that) {
            echo 'data';
            $that->handleData($data);
        });
    }

    protected function configureDevice($device, $baudrate)
    {
        $this->exec('stty -F ' . $device . ' ' . $baudrate);
        $this->exec('stty -F ' . $device . ' parenb -parodd');
    }

    public function handleData($data)
    {
        $this->emit('data', $data);
    }

    protected function exec($cmd, &$out = null)
    {
        $desc = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $proc = proc_open($cmd, $desc, $pipes);
        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $retVal = proc_close($proc);
        if (func_num_args() == 2) {
            $out = [$ret, $err];
        }
        return $retVal;
    }
}
