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

namespace Bot\Console\WebSocket;

use Bot\Console\Bot;
use Bot\Console\WebSocket\Base\WebSocketServerBase;

/**
 * Class WebSocketServer
 * Class meant to extend the functionality of the basic WebSocketServerBase class.
 * To fully work, some changes are necessary to the WebSocketServerBase class. These changes are listed here, in case updates to the WebSocketServerBase class are ever necessary:
 *  Added D4rKDeagle's Bot header
 *  Added notice saying this part is also file of PHP-WebSockets on the bot's header
 *  Line 30, added comment saying any modified lines are commented
 *  Lines 32 and 33, Commented require_once lines
 *  Line 35, added namespace declaration
 *  Line 38 (line after class declaration), added protected $shutdown = false;
 *  Line 52 (first constructor line), commented lines after $this->maxBufferSize = $bufferLength;
 *  Line 109 (line after the while(true) { line), added if($this->shutdown) break;
 *  Line 117 (socket_select line), changed timeout on socket_select to 0
 *
 * @package Bot\Console\WebSocket
 */
class WebSocketServer extends WebSocketServerBase
{

    private $_stackable;

    private $_addr;
    private $_port;

    private $_tickinterval = Bot::TICK_INTERVAL;

    public function __construct($addr, $port, $stackable)
    {
        parent::__construct($addr, $port, 2048);

        $this->_stackable = $stackable;

        $this->_addr = $addr;
        $this->_port = $port;
    }

    protected function process($user, $message)
    {
        $this->stdout("IDK what the hell is happening");
    }

    protected function connected($user)
    {
        $this->stdout("process this, noob!");
    }

    protected function closed($user)
    {

    }

    protected function connecting($user)
    {
        $this->stdout("Hehe how about this, idiot?");
        echo "WHAT ARE YOU FUCKING DOING";
    }

    public function run()
    {
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Failed: socket_create()");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die("Failed: socket_option()");
        socket_bind($this->master, $this->_addr, $this->_port) or die("Failed: socket_bind()");
        socket_listen($this->master, 20) or die("Failed: socket_listen()");
        $this->sockets['m'] = $this->master; // Bot Changed
        $this->stdout("Server started\nListening on: $this->_addr:$this->_port\nMaster socket: " . $this->master); // */

        parent::run();

        foreach ($this->sockets as $socket) {
            $this->disconnect($socket);
        }
    }

    protected function tick()
    {
        usleep($this->_tickinterval);
    }

    public function stdout($message)
    {
        if (!$this->interactive) return;

        $this->stackable[] = [
            'type' => "botlog",
            'loglevel' => "INFO",
            'message' => $message,
            'args' => []
        ];
    }

    public function stderr($message)
    {
        if (!$this->interactive) return;

        $this->stackable[] = [
            'type' => "botlog",
            'loglevel' => "ERROR",
            'message' => $message,
            'args' => []
        ];
    }

    public function shutdown()
    {
        $this->shutdown = true;
    }
}