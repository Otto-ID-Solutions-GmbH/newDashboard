<?php

namespace Cintas\Console\Commands;

use Cintas\Exceptions\NoteworthyException;
use Illuminate\Console\Command;

class SendErrorMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-error 
        {message? : the test message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send and log a test error notification';

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
        $message = $this->argument('message') ?? 'This is a test error message, no actions are required!';
        report(new NoteworthyException($message, 0, null, 'error'));
    }
}
