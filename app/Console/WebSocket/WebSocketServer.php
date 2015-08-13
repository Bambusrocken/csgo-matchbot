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

use Bot\Console\WebSocket\Base\WebSocketServerBase;

class WebSocketServer extends WebSocketServerBase {

    private $_stackable;

    public function __construct($addr, $port, $stackable, &$shutdown)
    {
        parent::__construct($addr, $port, 2048);

        $this->_stackable = $stackable;

        $this->shutdown = &$shutdown;
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