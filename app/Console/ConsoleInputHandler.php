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

namespace Bot\Console;

use Thread;

class ConsoleInputHandler extends Thread{

    private $_stackable;

    public function __construct($stackable)
    {
        $this->_stackable = $stackable;
    }

    public function run()
    {
        while(true)
        {
            $stdin = fopen('php://stdin', 'r');

            $line = strtolower(trim(fgets($stdin)));
            if($line == "quit" || $line == "exit")
            {
                // Shutting down should set a special variable $shutdown to true
                $this->_stackable[] = [
                    Bot::INSTRUCTION_TYPE => Bot::INSTRUCTION_SHUTDOWN
                ];

                break;
            }

            // Only process at the end
            if($line != null || $line != "")
            {
                $this->_stackable[] = [ // Remember: This is data
                    Bot::INSTRUCTION_TYPE => Bot::INSTRUCTION_BOTLOG, // Data type
                    Bot::BOTLOG_LEVEL => 'info', // Data information: Log level (string)
                    Bot::BOTLOG_MESSAGE => "Unknown command.", // Data information: Log message
                    Bot::BOTLOG_ARGS => [ 'command' => $line ] // Data information: Log arguments
                ];
            }
        }

        $this->kill();
    }
}