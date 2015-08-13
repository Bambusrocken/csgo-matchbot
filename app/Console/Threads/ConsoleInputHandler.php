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

namespace Bot\Console\Threads;

use Thread;

class ConsoleInputHandler extends Thread {

    private $_stackable;

    public function __construct($stackable)
    {
        $this->_stackable = $stackable;
    }

    public function run()
    {
        while(true)
        {
            // TODO: Make this detect if any artisan command is registered by the issued name and execute it if it exists

            $stdin = fopen('php://stdin', 'r');

            $line = strtolower(trim(fgets($stdin)));

            if($line == "help" || $line == "?")
            {
                $this->logMessage('Available commands: "help", "quit"');
                continue;
            }

            if($line == "quit" || $line == "exit")
            {
                $this->_stackable[] = [
                    'type' => 'shutdown'
                ];

                break;
            }

            // Only process at the end
            if($line != null || $line != "")
            {
                /*$this->_stackable[] = [ // Remember: This is data
                    'type' => 'botlog', // Data type
                    'loglevel' => 'info', // Data information: Log level (string)
                    'message' => "Unknown command. Type \"help\" for help.", // Data information: Log message
                    'args' => [ 'command' => $line ] // Data information: Log arguments
                ]; // */

                $this->logMessage('Unknown command. Type "help" for help.', [ 'command' => $line ]);
            }
        }

        $this->kill();
    }

    private function logMessage($message, $args = [])
    {
        $this->_stackable[] = [
            'type' => 'botlog',
            'loglevel' => 'info',
            'message' => $message,
            'args' => $args
        ];
    }
}