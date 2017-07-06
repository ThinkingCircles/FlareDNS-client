<?php
namespace ThinkingCircles\FlareDNSClient\Console\Commands;

use Illuminate\Console\Command;
use ThinkingCircles\FlareDNSClient\Client;

class FlareDNSClientSyncCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'flaredns:ipsync';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flarednsclient:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise server\'s public IP address with Cloudflare DNS record';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        
    }

}
