<?php
/*
 * D4rKDeagle's Bot: A CS:GO Match Management Bot
 * Copyright (c) 2015 D4rKDeagle
 *
 * This file is part of D4rKDeagle's Bot.
 * D4rKDeagle's Bot is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * D4rKDeagle's Bot is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with D4rKDeagle's Bot.  If not, see <http://www.gnu.org/licenses/>.
 *
 * The Laravel Framework is used in this project.
 * Laravel Framework Version 5.1
 * Copyright (c) <Taylor Otwell>
 *
 * This file is NOT a part of PHP-WebSockets.
 */

namespace Bot\Console\Threads;

use Bot\Console\WebSocket\WebSocketServer;
use Log;
use Thread;

class WebSocketServerThread extends Thread {

    private $_stackable;

    private $_addr;
    private $_port;

    private $_websocketServer;

    public function __construct($stackable, $addr, $port)
    {
        $this->_stackable = $stackable;
        $this->_addr = $addr;
        $this->_port = $port;

        Log::info("Constructing new WebSocketServerThread");
    }

    public function start($options = PTHREADS_INHERIT_NONE)
    {
        parent::start($options);

        Log::info("Started WebSocketServerThread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }

    public function run()
    {
        // Manual Requires are required because of PThreads
        require_once __DIR__.'/../../../vendor/autoload.php';

        $this->_websocketServer = new WebSocketServer($this->_addr, $this->_port, $this->_stackable);

        $this->_websocketServer->run();
    }

    public function term()
    {
        $this->_websocketServer->shutdown();
    }

    public function kill()
    {
        parent::kill();

        Log::info("Destroyed WebSocketServerThread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }
}