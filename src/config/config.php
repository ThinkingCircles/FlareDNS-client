<?php return [
    
    'cloudflare_api_account' => [
        'cloudflare_zone_id' => env('cloudflare_zone_id', null),
        'cloudflare_global_api_key' => env('cloudflare_global_api_key', null),
        'cloudflare_api_email' => env('cloudflare_api_email', null),
        'dns_records' => [
            ['id'=>null, 'name'=>'flaredns.thinkingcircles.com']
        ]
    ]

];
