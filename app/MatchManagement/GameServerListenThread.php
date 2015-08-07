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
 */

namespace Bot\MatchManagement;

use Config;
use Log;
use Thread;

class GameServerListenThread extends Thread {

    private $_stackable;

    private $_shutdown = false;

    private $_socket;
    private $_ipPort;

    private $_tickinterval;

    public function __construct($stackable, $tickinterval)
    {
        Log::info("Constructing new GameServerListenThread");

        $this->_stackable = $stackable;

        $this->_ipPort = explode(':', Config::get('app.ipport'));

        $this->_tickinterval = $tickinterval;
    }

    public function start($options = 0)
    {
        parent::start($options);

        Log::info("Starting GameServerListenThread thread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }

    public function run()
    {
        $this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or die("Could not create UDP socket"); // TODO: Add IPv6 support
        socket_bind($this->_socket, $this->_ipPort[0], $this->_ipPort[1]) or die("Failed to bind socket");
        socket_set_nonblock($this->_socket);

        while(true)
        {
            if($this->_shutdown) break;

            $readSockets = [ $this->_socket ];
            $writeSockets = [];
            $exceptSockets = [];
            if(socket_select($readSockets, $writeSockets, $exceptSockets, 0) === 0)
            {
                usleep($this->_tickinterval);
                continue;
            }

            if(!@socket_recvfrom($this->_socket, $buffer, 4096, 0, $remoteIp, $remotePort))
            {
                $lastError = socket_last_error($this->_socket);
                if($lastError == 0) break; // Thread shutting down error to unblock the blocking call, no need to log

                echo "Socket error num: " . $lastError . ", Socket error string: " . socket_strerror($lastError) . PHP_EOL;
                continue;
            }

            // TODO: Figure out why pthreads doesn't let me access the class "Bot" from here
            $this->_stackable[] = [
                'type' => 'serverlog',
                'ip' => $remoteIp,
                'port' => $remotePort,
                'message' => $buffer
            ];
        }

        socket_close($this->_socket);
    }

    public function kill()
    {
        parent::kill();

        $this->_shutdown = true;

        Log::info("Destroyed GameServerListenThread thread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }
}