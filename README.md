# FlareDNS-client

Synchronize your App dynamic IP with your CloudFlare DNS A records.

Use this package if:

* You use [CloudFlare](https://cloudflare.com "CloudFlare").
* You run your Laravel App on a server with a Dynamic IP or your IP changes often.



## Installation

```
composer require thinkingcircles/flaredns-client

php artisan vendor:publish --provider="ThinkingCircles\FlareDNSClient\FlareDNSClientServiceProvider"

```

## Get CloudFlare api keys


## Configuration

```php
// .env

cloudflare_zone_id=
cloudflare_global_api_key=
cloudflare_api_email=

```
:or

```php
// config/flaredns-client.php

<?php return [
    
    'cloudflare_api_account' => [
        'cloudflare_zone_id' => 'ZONE-ID',
        'cloudflare_global_api_key' => 'API-KEY',
        'cloudflare_api_email' => 'API-EMAIL',
        'dns_records' => [
        	//will match id or name
            ['id'=>null, 'name'=>'domain.net'],
            ['id'=>'CloudFlare-API-Record-ID', 'name'=>null]
        ]
    ]

];

```


```php
// config/app.php

'providers' => [
    // Other service providers...    
    ThinkingCircles\FlareDNSClient\FlareDNSClientServiceProvider::class,
]

```

## Usage

```php
// App/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    $schedule->command('flarednsclient:ipsync')->everyFiveMinutes();
}
```



## Todo

- [x] Debug
- [ ] Improve logic 
- [ ] Documentation
- [ ] Make UI [FlareDNS Management UI](https://www.github.com/ThinkingCircles/FlareDNS "FlareDNS Management UI")