<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

class ProcessAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'act:processapi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actions which will be executed daily';

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
        // $withdrawCon = app('App\Http\Controllers\Users\WithdrawalController');
        
        // Log::info('process withdrawal start...');
		// $withdrawCon->runAutoWithdrawal();
		// Log::info('process withdrawal end...');
	}
}
