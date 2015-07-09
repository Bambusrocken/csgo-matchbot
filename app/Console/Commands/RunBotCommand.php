<?php

namespace Bot\Console\Commands;

use Log;
use Storage;
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
    protected $signature = 'bot:run {--loglevel= : Monolog Logging level name}';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $logLevel = Logger::toMonologLevel($this->option("loglevel"));

        if(is_string($logLevel))
        {
            echo "ERROR: Unknown log level {$logLevel}! Exiting."; // Why use Log::error if there's no output to the console? :(
            exit;
        }

        Log::getMonolog()->pushHandler(new StreamHandler(STDOUT, $logLevel, true, null, false));

        unset($logLevel); // Memory cleanup, even though just a tiny bit used, in the end if you add it all up it's a lot and because this process is going, most likely, to be open for a long time, memory is a concern

        // Starting the bot should be as simple as this:
        // 1º: Start a queue daemon process
        // 2º: Start a UDP Listening process
        // 3º: Start a WebSockets server
        // 4º: Get this process managing everything else while also accepting user input

        //TODO: Add a command line option to get the user to tell me where php.exe or whatever is stored at and also any extra launch options they might wanna pass to php
        Process::newManagedProcess("php artisan queue:work --daemon", "queue");
        #Process::newManagedProcess("php artisan bot:udplisten", null);
        #Process::newManagedProcess("php artisan bot:websocketserver", "websocket_server");

        stream_set_blocking(STDIN, false);

        while(true)
        {

            //Main bot loop
            //Handle Websockets packets and process them, putting anything that the bot should handle in the appropriate instruction queue
            //Run the tick() or run() method (not sure what to call it) on every match manager
            //Run any other necessary logic
            //Check for $shutdown
            //Handle console input (Might remove this feature in the future, just because it seems I'm not gonna have any commands to put here)

            //Main loop for each match manager
            //Handle udp packets and put them in the instruction queue
            //Check if $shutdown is true: if it is, add to the top of the queue a special shutdown instruction that indicates to the match that we're shutting down
            //Run bot logic: process instruction queue

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

        Process::closeManagedProcesses();
    }
}

class Process
{
    private static $_processes = [];

    private $_id;
    private $_process;
    private $_pipes;

    /**
     * Creates a new child process of this script.
     *
     * @param int $id
     * @param String $command the command to send to the OS's shell
     * @param array $logsFolder the folder in which to store the logs for this child process
     * @return Process
     */
    function __construct($id, $command, $logsFolder)
    {
        $this->_id = $id;

        $dirName = storage_path() . "app/logs/{$logsFolder}";

        //echo "Working dir: $dirName" . PHP_EOL;
        Log::debug("Constructing Process.", ['id' => $id, 'command' => $command, 'dirname' => $dirName ]);

        if(!file_exists($dirName))
        {
            //echo "Directory doesn't exist: creating it" . PHP_EOL;
            Log::debug("Logs folder doesn't exist, creating recursively.", [ 'id' => $id, 'dirName' => $dirName ]);
            mkdir($dirName, 077, true);
        }

        $descriptorSpec = [
            [ 'pipe', "r" ],
            [ 'pipe', "w" ],
            [ 'file', $dirName . "/stderror.txt", "a" ]
        ];

        $options = [
            'bypass_shell' => true
        ];

        //echo "Starting process: $command" . PHP_EOL;
        Log::info("Starting process", [ 'command' => $command ]);

        $process = proc_open($command, $descriptorSpec, $pipes, null, null, $options);

        $this->_process = $process;
        $this->_pipes = $pipes;
    }

    /**
     * Returns this script's write pipe to the child process.
     * <p>
     * Use this pipe to write data to the child process.
     *
     * @return resource
     */
    function getWritePipe()
    {
        return $this->_pipes[0];
    }

    /**
     * Returns this script's read pipe from the child process.
     * <p>
     * Use this pipe to read output from the child process.
     *
     * @return resource
     */
    function getReadPipe()
    {
        return $this->_pipes[1];
    }

    /**
     * Terminates the child process.
     *
     * @return bool
     */
    function close()
    {
        return $this->terminateProcess();
    }

    /**
     * Terminates the child process.
     *
     * @return bool
     */
    function terminateProcess()
    {
        Log::info("Terminating process.", [ 'id' => $this->_id ]);

        fwrite($this->getWritePipe(), "shutdown", sizeof("shutdown"));

        sleep(100/1000); // 100ms

        foreach($this->_pipes as $pipe)
        {
            fclose($pipe);
        }

        return proc_terminate($this->_process);
    }

    /**
     * Open a new process to be managed by this class.
     *
     * @param $command
     * @param $logsFolder
     * @return Process
     */
    static function newManagedProcess($command, $logsFolder)
    {
        $processId = count(self::$_processes);
        return self::$_processes[$processId] = new Process($processId, $command, $logsFolder);
    }

    /**
     * Close an opened managed process.
     *
     * @param Process $process
     */
    static function closeManagedProcess($process)
    {
        $process->close();

        unset(self::$_processes[$process->_id]);
        unset($process);
    }

    /**
     * Close all opened processes managed by this class.
     */
    static function closeManagedProcesses()
    {
        foreach(self::$_processes as $process)
        {
            self::closeManagedProcess($process);
        }
    }
}