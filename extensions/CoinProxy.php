<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class CoinProxy implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if ($msg === 'PING') {
            $from->send('PONG');
            echo "PING from {$from->resourceId}";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function broadcast($message)
    {
        echo 'broadcasting '.$message;
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }
}
