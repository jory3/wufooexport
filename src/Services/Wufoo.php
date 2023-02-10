<?php

declare(strict_types=1);

namespace Services;

use Adamlc\Wufoo\WufooApiWrapper;

use Exception;
use Log;

final class Wufoo
{
    private $apiKey;
    private $subDomain;
    private $domain;
    private $password;

    public function __construct($apiKey, $password, $subDomain, $domain = 'wufoo.com')
    {
        $this->apiKey = $apiKey;
        $this->subDomain = $subDomain;
        $this->domain = $domain;
        $this->password = $password;
    }

    public function handle()
    {
    }

    public function getForms()
    {
        try {
            $curl = curl_init("https://$this->subDomain.wufoo.com/api/v3/forms.json");

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey . ":" . $this->password);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Wufoo Sample Code');

            $response = curl_exec($curl);
            $resultStatus = curl_getinfo($curl);

            curl_close($curl);

            if ($resultStatus['http_code'] != 200) {
                echo 'Call Failed ' . print_r($resultStatus);
            }

            $jsonResponse = json_decode($response, true);

            return $jsonResponse;
        } catch (Exception $error) {
            Log::critical("There was an error fetching all forms." . $error->getMessage());
        }
    }

    public function getEntries($hash)
    {
        try {
            $curl = curl_init("https://$this->subDomain.wufoo.com/api/v3/forms/$hash/entries.json");

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, $this->apiKey . ":" . $this->password);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Wufoo Sample Code');

            $response = curl_exec($curl);
            $resultStatus = curl_getinfo($curl);

            curl_close($curl);

            if ($resultStatus['http_code'] != 200) {
                echo 'Call Failed ' . print_r($resultStatus);
            }

            $jsonResponse = json_decode($response, true);
            return $jsonResponse;
        } catch (Exception $error) {
            Log::critical("There was an error pulling the entries for this form");
        }
    }
}
