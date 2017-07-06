<?php
namespace ThinkingCircles\FlareDNSClient\Console\Commands;

use Illuminate\Console\Command;
use ThinkingCircles\FlareDNSClient\FlareDNSClient;

class FlareDNSClientSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flarednsclient:ipsync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise server\'s public IP address with Cloudflare DNS record';

    /**
     * @var FlareDNSClient
     */
    private $flarednsclient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FlareDNSClient $flarednsclient)
    {
        //config('cloudflare_api_account')

        parent::__construct();
        $this->flarednsclient = $flarednsclient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $response = $this->flarednsclient->cloudflareSync();
        if ($response) {
            if(isset($response['success'])){
                if(is_array($response['success']) || is_object($response['success']) ){
                    $response['success'] = json_encode($response['success']);
                }
                $this->info($response['success']);
                return;
            }else{
                if(is_array($response['error']) || is_object($response['error']) ){
                    $response['error'] = json_encode($response['error']);
                }
                $this->error("Errors.=>".$response['error']);
                return;
            }
            
        }
        $this->error("Update unsuccessful. Exception was thrown.");

    }

}
