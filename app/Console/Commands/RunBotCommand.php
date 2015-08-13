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

namespace Bot\Console\Commands;

use Bot\Console\Bot;
use Config;
use Illuminate\Console\Command;
use Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class RunBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'bot:run';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Run the bot.';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();

        $this->setupLogHandler();
    }

    public function setupLogHandler()
    {
        $logLevel = Logger::toMonologLevel(Config::get('APP_LOGLEVEL'));

        Log::getMonolog()->pushHandler(new StreamHandler(STDOUT, $logLevel, true, null, false));

        if(is_string($logLevel))
        {
            echo "ERROR: Unknown log level {$logLevel}! Exiting."; // Why use Log::error if there's no output to the console? :(
            exit;
        }

        unset($logLevel);
    }

    /**
     * Execute the console command.
     *
     * @param Bot $bot
     * @return mixed
     */

    public function handle(Bot $bot)
    {
        $bot->run();
    }
}