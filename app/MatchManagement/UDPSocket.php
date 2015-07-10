<?php

namespace Bot\MatchManagement;

use Thread;
use Log;
use Config;

class UDPSocket extends Thread {

    private static $_queue; // Fix this
    private $_socket;

    public function __construct()
    {
        Log::info("Constructing new thread");

        $ipPort = explode(':', Config::get('app.ipport'));

        $this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or die("Could not create UDP socket");
        socket_bind($this->_socket, $ipPort[0], $ipPort[1]) or die("Failed to bind socket");
        socket_set_block($this->_socket);

        $this->_queue = new StackableQueueWrapper(new \Stackable());
    }

    public function start($options = 0)
    {
        parent::start($options);

        Log::info("Starting thread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }

    public function run()
    {
        while($this->isRunning())
        {
            if(!socket_recv($this->_socket, $buffer, 4096, 0))
            {
                echo socket_strerror(socket_last_error($this->_socket));
            }

            $this->getMessageQueue()->push($buffer);
        }
    }

    public function kill()
    {
        parent::kill();

        socket_close($this->_socket);

        Log::info("Destroyed thread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }

    public function getMessageQueue()
    {
        return $this->_queue; // TODO: Fix the bug that upon arrival of a packet, pthreads detected an attempt to connect to a Bot\MatchManagement\StackableQueue which has already been destroyed
        //Stack trace traces back to line 55
    }
}