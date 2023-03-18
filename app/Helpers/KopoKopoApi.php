<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;
use App\Kopokopo\Src\K2;


class KopoKopoApi{
    /**
     * The url
     * @var string $production url
     */
    private $prod_url;
    /**
     * The url
     * @var string $sandbox url
     */
    private $sandbox_url;
    /**
     * The api key
     * @var string $api
     */
    public $api_key;
    /**
     * The client id
     * @var string $client_id
     * _secret
     */
    public $client_id;
    /**
     * The secret
     * @var string $secret
     */
    public $client_secret;
    /**
     * The kopokopo SDK
     * @var string $SDK
     */
    public $kConnect;
    /**
     * The webhook
     * @var string $webhook callback
     */
    public $webhook_callback;
    /**
     * The stk
     * @var string $stk callback
     */
    public $stk_callback;
    /**
     * The till
     * @var string $kopokopo till
     */
    public $till_number;
    /**
     * The api key
     * @var string $api
     */
    public $stk_till_number;


    public function __construct()
    {

        $this->base_url = 'https://api.kopokopo.com';
        $this->sandbox_url = 'https://sandbox.kopokopo.com';
        $this->webhook_callback = "https://uat.geetabtechnologies.com/api/payment-callback";
        $this->stk_callback = "https://uat.geetabtechnologies.com/api/payment-callback-stk";
        $this->client_id = "lIujo2l0YNyRjWMd9rgdTGPjReHKjvQtr7hRxOJOwlw";
        $this->client_secret = "lgiL_QMESWJr6JuSye2jog5ZCJeIZp9qW4u_hC2MSqo";
        $this->api_key = "532748bd4dadf337ba92e4d199ad4c075e4b85f7";
        $this->till_number = '9760811';
        $this->stk_till_number = "K465138";
        $options = [
            'clientId' => $this->client_id,
            'clientSecret' => $this->client_secret,
            'apiKey' => $this->api_key,
            'baseUrl' => $this->base_url
        ];
        $this->kConnect = new K2($options);

    }

    public function generateToken()
    {
        $tokens = $this->kConnect->TokenService();
        $result = $tokens->getToken();
        if($result['status'] == 'success'){
            $data = $result['data'];
            return $data['accessToken'];
        }
        return false;

    }

    public function subscribeWebhook(){

        $webhooks = $this->kConnect->Webhooks();
        $response = $webhooks->subscribe([
            'eventType' => 'buygoods_transaction_received',
            'url' => $this->webhook_callback,
            'scope' => 'till',
            'scopeReference' => $this->till_number,
            'accessToken' => $this->generateToken()
        ]);

        if($response['status'] == 'success')
        {
            return json_encode($response['location']);
        }
        return false;

    }
    public function initiate_stk_push($data)
    {
        $stk = $this->kConnect->StkService();
        $response = $stk->initiateIncomingPayment($data);
        return $response;
        // if($response['status'] == 'success')
        // {
        //     return json_encode($response['location']);
        // }
        // return false;
    }


}
