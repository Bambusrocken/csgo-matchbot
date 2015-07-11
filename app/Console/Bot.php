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

use Bot\MatchManagement\Contracts\MatchManager;
use Bot\MatchManagement\GameServerListenThread;
use Bot\Util\StackableQueueWrapper;
use Log;
use Monolog\Logger;
use Stackable;

/**
 * Main bot class. Handles all of the BOT logic.
 *
 * @package Bot
 */
class Bot {
    const INSTRUCTION_TYPE = 0;

    const INSTRUCTION_SHUTDOWN = 0;

    const INSTRUCTION_BOTLOG = 1;
    const BOTLOG_LEVEL = 1;
    const BOTLOG_MESSAGE = 2;
    const BOTLOG_ARGS = 3;

    const INSTRUCTION_SERVERLOG = 2;
    const SERVERLOG_IP = 1;
    const SERVERLOG_PORT = 2;
    const SERVERLOG_MESSAGE = 3;

    // Don't forget to change tick interval in GameServerListenThread
    const TICK_INTERVAL = 7.8125; // Tick interval in milliseconds, for 128 ticks type 7.8125, for 64 ticks type 15.625, for another tickrate just calculate (1/<tickrate>)*1000

    private $_matchManager;

    private $_stackable;

    private $_consoleThread;
    private $_gameserverListenThread;

    public function __construct(MatchManager $matchManager)
    {
        $this->_matchManager = $matchManager;

        $this->_stackable = new Stackable;

        $this->_consoleThread = new ConsoleInputHandler($this->_stackable);
        $this->_gameserverListenThread = new GameServerListenThread($this->_stackable, self::TICK_INTERVAL);
    }

    public function run() {
        $this->_consoleThread->start();
        $this->_gameserverListenThread->start();

        while(true) {
            //Main bot loop
            //Handle udp packets and put them in the appropriate instruction queue
            //Handle Websockets packets and process them, putting anything that the bot should handle in the appropriate instruction queue
            //Run the tick() or run() method (not sure what to call it) on every match manager
            //Run any other necessary logic
            //Check for $shutdown
            //Handle console input (Might remove this feature in the future, just because it seems I'm not gonna have any commands to put here)

            //Main loop for each match manager
            //Check if $shutdown is true: if it is, add to the top of the queue a special shutdown instruction that indicates to the match that we're shutting down
            //Run bot logic: process instruction queue

            $instruction = StackableQueueWrapper::pop($this->_stackable);

            if($instruction != false) {
                // The instruction array always contains an item, $instruction[0], which has a string specifying the type of instruction we should process
                // Instruction 1 means BOTLOG, which is an internal thing of the bot to tell the main loop it should log something to the user console (and files)
                // Instruction 2 means SERVERLOG, which means that the array contains data received from a game server

                if($instruction[self::INSTRUCTION_TYPE] == self::INSTRUCTION_SHUTDOWN)
                {
                    break;
                }

                if($instruction[self::INSTRUCTION_TYPE] == self::INSTRUCTION_BOTLOG)
                {
                    //Data array structure should be:
                    // $data[0] == 'botlog' (string)
                    // $data[1] == Log level (string)
                    // $data[2] == Log message (string)
                    // $data[3] == Log arguments (array)
                    Log::getMonolog()->addRecord(
                        constant("\\Monolog\\Logger::" . strtoupper($instruction[self::BOTLOG_LEVEL])),
                        $instruction[self::BOTLOG_MESSAGE],
                        $instruction[self::BOTLOG_ARGS]
                    );
                }

                if($instruction[self::INSTRUCTION_TYPE] == self::INSTRUCTION_SERVERLOG)
                {
                    //Data array structure should be:
                    // $data[0] == 'serverlog' (string)
                    // $data[1] == IP (string)
                    // $data[2] == Port (string)
                    // $data[3] == Log message (string)
                    Log::debug("Received ServerLog", [
                        'ip' => $instruction[self::SERVERLOG_IP],
                        'port' => $instruction[self::SERVERLOG_PORT],
                        'message' => $instruction[self::SERVERLOG_MESSAGE]
                    ]);
                }
            }

            usleep(self::TICK_INTERVAL);
        }

        $this->_gameserverListenThread->kill();
        $this->_consoleThread->kill();

        Log::info("Exiting");

        exit;
    }
}