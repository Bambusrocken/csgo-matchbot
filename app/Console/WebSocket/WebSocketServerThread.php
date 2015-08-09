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

namespace Bot\Console\WebSocket;

use Log;

class WebSocketServerThread extends WebSocketServer {

    private $_stackable;

    public function __construct($addr, $port, $stackable)
    {
        parent::__construct($addr, $port, 2048);
        //$this->sockets = new \Stackable;
        /*$this->users = new \Stackable;
        $this->heldMessages = new \Stackable; // */
        $this->_stackable = $stackable;

        Log::info("Constructing new WebSocketServerThread", ['tickinterval' => $this->tickinterval, 'interactive' => $this->interactive]);
    }

    public function start($options = null)
    {
        parent::start($options);

        Log::info("Started WebSocketServerThread thread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
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

    public function kill()
    {
        parent::kill();

        $this->shutdown = true;

        Log::info("Destroyed WebSocketServerThread", [ 'id' => $this->getThreadId() ]); // Acceptable because it's being called from the main thread
    }

    public function stdout($message, $args = [])
    {
        echo "$message\n";
        if(!$this->interactive) return;

        $this->stackable[] = [
            'type' => "botlog",
            'loglevel' => "INFO",
            'message' => $message,
            'args' => $args
        ];
    }

    public function stderr($message)
    {
        if(!$this->interactive) return;

        $this->stackable[] = [
            'type' => "botlog",
            'loglevel' => "ERROR",
            'message' => $message,
            'args' => []
        ];
    }
}