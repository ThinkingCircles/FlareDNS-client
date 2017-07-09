<?php return [
    
    'cloudflare_api_account' => [

        'cloudflare_zone_id' => env('cloudflare_zone_id', null), //ZONE ID

        'cloudflare_global_api_key' => env('cloudflare_global_api_key', null), // API KEY

        'cloudflare_api_email' => env('cloudflare_api_email', null), // API EMAIL

        'dns_records' => [
        	//Will match CloudFlare Record ID or domain name 
            ['id'=>null, 'name'=>'wwww.domain.net'],
        ]
    ]

];

