<?php

namespace Bot\Console\Commands;

use Config;
use Log;

use Bot\MatchManagement\UDPSocket;
use Illuminate\Console\Command;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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

        unset($logLevel); // Memory cleanup, even though just a tiny bit used, in the end if you add it all up it's a lot and because this process is going, most likely, to be open for a long time, memory is a concern
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        stream_set_blocking(STDIN, false);

        $socket = new UDPSocket();

        $socket->start();

        while(true)
        {
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
            Log::info("Test");
            $data = $socket->getMessageQueue()->pop();

            if($data)
            {
                Log::info($data);
            }

            $line = strtolower(trim(fgets(STDIN)));

            if($line == "quit" || $line == "exit")
            {
                // Shutting down should set a special variable $shutdown to true
                Log::info("Shutting down...");
                break;
            }

            // Only process at the end
            if($line != null || $line != "")
            {
                Log::info("Unknown command.", [ 'command' => $line ]);
            }
        }

        $socket->kill();
    }
}