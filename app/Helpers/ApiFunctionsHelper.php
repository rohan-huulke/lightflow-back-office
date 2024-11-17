<?php

namespace App\Helpers;

use Exception;
//use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class ApiFunctionsHelper
{

    public static function json($data)
    {
        header('Content-Type: application/json');
        echo $data;
    }

    public static function getRequestResult($requestMethod = 'get', $requestUri = 'ping', $data = [], $formParams = [], $jsonData = '')
    {

        if (auth()->check() && auth()->user()->id != 1 && !array_key_exists("id", $data)) {
            $data['id'] = auth()->user()->s_id;
        }
        
        $data['originId'] = auth()->user()->card_id ?? null;
        try {
            $client = new Client();
            $accessToken = request()->session()->get('auth.token');

            //Add token
            if ($accessToken) {
                $headers = [
                    'Authorization' => "Bearer {$accessToken}",
                    'Content-Type' => 'application/json',
                ];
            } else {
                $headers = [
                    'Content-Type' => 'application/json',
                ];
            }

            $response = $client->request(
                $requestMethod,
                env('KIMBERLITEBASEURL', 'http://167.172.180.175/') . $requestUri,
                // env('KIMBERLITEBASEURL', 'https://api.lightflowmetaverse.com/') . $requestUri,
                [
                    'headers' => $headers,
                    'query' => $data,
                    'form_params' => $formParams,
                    'http_errors' => false,
                    'json' => $jsonData,
                    //'body' => json_encode(['Action'=>'Return']),
                ],
            );
            // dump([
            //     'requestMethod' => $requestMethod,
            //     'requestUri' => $requestUri,
            //     'data' => $data,
            //     'jsonData' => $jsonData,
            //     'response' => $response->getBody()->getContents()
            // ]);
            return ($response->getBody()->getContents());
            //dump($response->getBody()->getContents());
            // if (!empty($response->getStatusCode()) && $response->getStatusCode() == 200) { // OK
            //     return $response->getBody()->getContents();
            // }

            // // 400 vs errors..
            // if (json_decode($response->getBody()->getContents())->Code == 1001) {

            //     $genbaAccessToken = GenbaFunctionsHelper::getAccessToken();
            //     $token = new GenbaToken;
            //     $token->token = $genbaAccessToken;
            //     $token->save();
            //     GenbaFunctionsHelper::getRequestResult($requestMethod, $requestUri, $data, $formParams);
            // }
            // //dd($response->getBody()->getContents());
            // $data = ['error' => 511, 'message' => 'Error getting result from genba', 'genbaOutput' => json_decode($response->getBody()->getContents())];
            // GenbaFunctionsHelper::json(json_encode($data));
        } catch (Exception $exception) {
            //dd($exception->getMessage());
            $data = ['error' => 512, 'message' => 'Exception getting result from API: ' . $exception->getMessage()];
            ApiFunctionsHelper::json(json_encode($data));
        }
    }
}
