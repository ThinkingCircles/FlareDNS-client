<?php
namespace ThinkingCircles\FlareDNSClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

class FlareDNSClient
{
    protected $cf_base_uri = 'https://api.cloudflare.com/client/v4/zones';

    /**
     * CloudFlare API Account and records
     *
     * @var string $apiAccount
     */
    private $apiAccount;
    /**
     * Client constructor.
     */
    public function __construct()
    {
        $apiAccountConfig = config('flaredns-client.cloudflare_api_account', null);
        //print_r($apiAccountConfig);
        if ($apiAccountConfig) {
            $this->apiAccount = (object) $apiAccountConfig;
        }

    }
    /**
     * update records
     *
     * @return mixed
     */
    public function cloudflareSync()
    {
        $response = [];
        if ($this->apiAccount) {
            $listAllDNSRecordsRespond = $this->CloudFlare_DNS_Request('LIST', null, $this->apiAccount);
            if (isset($listAllDNSRecordsRespond['success'])) {
                
                if (is_array($this->apiAccount->dns_records)) {
                    $ip = $this->dyn_IP_Get();
                    if (!$ip || isset($ip['error'])) {
                        $response = $ip;

                        return $response;
                    }
                    $ip = $ip;
                    if (isset($this->apiAccount->dns_records['id']) || isset($this->apiAccount->dns_records['name'])) {
                        
                        if ((isset($this->apiAccount->dns_records['id']) && $dnsRecord->id == $this->apiAccount->dns_records['id']) ||
                            (isset($this->apiAccount->dns_records['name']) && $dnsRecord->name == $this->apiAccount->dns_records['name'])) {

                            foreach ($listAllDNSRecordsRespond['success'] as $dnsRecord) {
                                if ($dnsRecord->type == 'A') {
                                    if ($dnsRecord->content != $ip) {
                                        $dnsRecord->content = $ip;
                                        $response           = $this->CloudFlare_DNS_Request('UPDATE', $dnsRecord, $this->apiAccount);
                                        if ($response) {
                                            if (isset($response['success'])) {
                                                $response['success'][$dnsRecord->id] = $response['success'];
                                            } else if (isset($response['error'])) {
                                                $response['error'][$dnsRecord->id] = $response['error'];
                                            } else {
                                                $response['error'][$dnsRecord->id] = 'Error !response[error || success]->cf_dns_request() Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                            }
                                        } else {
                                            $response['error'][$dnsRecord->id] = 'Error !response->cf_dns_request() Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                        }
                                    } else {
                                        $response['success'] = 'Record is up to date. Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                    }
                                }
                            }

                        }

                    } else {

                        foreach ($this->apiAccount->dns_records as $key => $sync_dnsRecord) {
                            

                            if (is_array($sync_dnsRecord)) {$sync_dnsRecord = (object) $sync_dnsRecord;}


                            foreach ($listAllDNSRecordsRespond['success'] as $dnsRecord) {
                                

                                if ($dnsRecord->type == 'A') {
                                    

                                    if ((isset($sync_dnsRecord->id) && $dnsRecord->id == $sync_dnsRecord->id) || (isset($sync_dnsRecord->name) && $dnsRecord->name == $sync_dnsRecord->name)) {
                                        
                                        if ($dnsRecord->content != $ip) {
                                           
                                            $dnsRecord->content = $ip;
                                            $resp          = $this->CloudFlare_DNS_Request('UPDATE', $dnsRecord, $this->apiAccount);
                                            if ($resp) {

                                                

                                                if (isset($resp['success'])) {
                                                    $response['success'][$dnsRecord->id] = $resp['success'];
                                                } else if (isset($resp['error'])) {
                                                    $response['error'][$dnsRecord->id] = $resp['error'];
                                                } else {
                                                    $response['error'][$dnsRecord->id] = 'Error !response[error || success]->cf_dns_request() Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                                }
                                            } else {
                                                $response['error'][$dnsRecord->id] = 'Error !response->cf_dns_request() Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                            }

                                        } else {
                                            $response['success'][$dnsRecord->id] = 'Record is up to date. Name=>' . $dnsRecord->name . ' Content=>' . $dnsRecord->content;
                                        }

                                    }else{
                                        $response['error'] = 'Unable to match an existing record on cloudflare';
                                    }
                                }
                            }
                        }
                    }

                } else {

                }

            } else {
                $response = $listAllDNSRecordsRespond;
            }

        } else {
            $response['error'] = 'FlareDNS Client Config file (app/config/flaredns-client.php) not set.';
        }
        

        return $response;
    }

    public function CloudFlare_DNS_Request($action, $DNS, $cfAPI, $page = 1)
    {

        $response = [];

        $action        = strtoupper($action);
        $actionOptions = ['CREATE', 'UPDATE', 'DELETE', 'DETAILS', 'LIST'];

        if (in_array($action, $actionOptions)) {
            if ($cfAPI) {
                if (!isset($cfAPI->cloudflare_zone_id) || !isset($cfAPI->cloudflare_global_api_key) || !isset($cfAPI->cloudflare_api_email)) {
                    $response['error'] = 'One or more API keys or id are not set';
                    return $response;
                }
            }

            $requestClient = new Client();

            $uri = $this->cf_base_uri . '/' . $cfAPI->cloudflare_zone_id . '/dns_records';

            $sendJSON = true;
            $useURLid = true;
            $paginate = false;

            $sendDATA = [];

            switch ($action) {

                case 'CREATE':
                    $method   = 'POST';
                    $useURLid = false;
                    break;

                case 'UPDATE':
                    $method = 'PUT';
                    break;

                case 'DELETE':
                    $method   = 'DELETE';
                    $sendJSON = false;
                    break;

                case 'DETAILS':
                    $method   = 'GET';
                    $sendJSON = false;
                    break;

                case 'LIST':
                default:
                    $method               = 'GET';
                    $sendJSON             = false;
                    $useURLid             = false;
                    $sendDATA['page']     = $page;
                    $sendDATA['per_page'] = 10;
                    $uri .= '?' . http_build_query($sendDATA);
                    $paginate = true;
                    break;
            }

            $sendDATA['headers'] = ['X-Auth-Email' => $cfAPI->cloudflare_api_email, 'X-Auth-Key' => $cfAPI->cloudflare_global_api_key, 'Content-Type' => 'application/json'];

            if ($sendJSON) {
                $sendDATA['json'] = [
                    "type"    => $DNS->type,
                    "name"    => $DNS->name,
                    "content" => $DNS->content,
                    "ttl"     => ($DNS->ttl) ? $DNS->ttl : 1,
                    "proxied" => (($DNS->proxied == 'true' || $DNS->proxied == '1' || $DNS->proxied == true || $DNS->proxied == 1) ? true : false),
                ];
            }

            if ($useURLid) {
                if (isset($DNS->cloudflare_record_id)) {

                    $uri .= '/' . $DNS->cloudflare_record_id;
                } else {
                    $uri .= '/' . $DNS->id;
                }

            }

            try {
                $resp = $requestClient->request($method, $uri, $sendDATA);
            } catch (ClientException $e) {
                $response = [];

                $response['request'] = $e->getRequest();

                if ($e->hasResponse()) {
                    $result = $e->getResponse()->getBody(true)->getContents();
                    //print_r($result);
                    $response['result'] = $result;
                    $result             = json_decode($result);

                    if (isset($result->errors)) {
                        $response['error'] = $result->errors;
                    }
                }

                //print_r($response);

                return $response;
            }
            $body = $resp->getBody();

            $bodyArray = json_decode($body);
            if (isset($bodyArray->result)) {
                if ($paginate) {
                    if (isset($bodyArray->result_info) && isset($bodyArray->result_info->page) && isset($bodyArray->result_info->total_count) && isset($bodyArray->result_info->per_page)) {
                        $pg_count       = (int) $bodyArray->result_info->total_count;
                        $per_pg         = (int) $bodyArray->result_info->per_page;
                        $next_pg_number = (int) $page;
                        $next_pg_number = $next_pg_number + 1;
                        if (ceil($pg_count / $per_pg) >= $next_pg_number) {
                            $pg_result = $this->CloudFlare_DNS_Request($action, $DNS, $cfAPI, $next_pg_number);

                            if (isset($pg_result['success'])) {

                                $tempBodyArray     = (array) $bodyArray->result;
                                $tempPgResult      = (array) $pg_result['success'];
                                $bodyArray->result = array_merge($tempBodyArray, $tempPgResult);
                            }
                        }

                    }
                }
                $response['success'] = $bodyArray->result;
            } else {
                $response['error'] = 'Action options is incorrect.';
            }

        } else {
            $response['error']           = 'Action options is incorrect.';
            $response['posible_options'] = $actionOptions;
        }

        return $response;
    }

    public function dyn_IP_Get($method = 'GET', $uri = 'http://checkip.dyndns.com/')
    {
        try {
            $requestClient = new Client();

            $method = 'GET';
            $ipR    = $requestClient->request($method, $uri);
            $ipBody = trim($ipR->getBody());

            preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $ipBody, $m);
            $ip = '';
            if (isset($m[1])) {
                $ip = trim($m[1]);
            }
            return $ip;
        } catch (RequestException $e) {
            $response          = [];
            $response['error'] = Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                $response['response'] = Psr7\str($e->getResponse());
            }
            //print_r($response);

            return $response;
        }
        //NOTE: no need for catch just false return
        return false;
    }
}
