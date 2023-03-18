<?php

namespace App\Helpers;

class Utils{
    private $base_url;
    /**
     * The api key
     * @var string $api
     */
    public $api_key;
    /**
     * The partner id
     * @var string $partner
     * _secret
     */
    public $partner_id;
    /**
     * The short code
     * @var string $shortcode
     */
    public $short_code;



    public function __construct()
    {

        $this->base_url = 'https://sms.emreignltd.com/api/services/';

        $this->api_key = "03f491cb58086d365898aa7959e61456";
        $this->partner_id = "4265";
        $this->short_code = "Riel";
    }

    public function getCandles()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1h');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials, 'Content-Type: application/json'));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($info["http_code"] == 200) {
            return $response;
        } else {
            return false;
        }
    }

    // Make request
    private function submit_request($url, $data)
    { // Returns cURL response

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    // submit request
    public function send_message($message, $phone)
    {
        if (!is_numeric($phone)) {
            throw new Exception("Invalid amount and/or phone number. phone number should be in the format 254xxxxxxxx");
            return false;
        }

        $data = array(
            'apikey' => $this->api_key,
            'partnerID' => $this->partner_id,
            'message' => $message,
            'shortcode' => $this->short_code,
            'mobile' => $phone
        );
        $data = json_encode($data);
        $url = $this->base_url.'sendsms/';
        $response = $this->submit_request($url, $data);

        if (isset($response)) {
            return $response;
        } else {
            return false;
        }

    }

    public function getcode($length){
        $characters = '0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function aircode($length){
        $characters = '123456789';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function getreferalcode($length){
        $characters = 'AbcD01efGh23JkmN45pQ67rst8uvWX9yZ';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function getreference($length){
        $characters = 'ABCD01EFGH23JKMN45PQ67RST8UVWX9YZ';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function gettoken($length){
        $characters = 'AbcDJ01:efJGh23Jk}mN4H53p$Q67rPst8\uvWDX9yeZ';
        return substr(str_shuffle($characters), 0, $length);
    }
    public function sanitizePhone($phone)
    {
        if(strlen($phone) >= 9){
            if(strlen($phone) == 12){
                return $phone;
            }else{
                if(str_starts_with($phone, '0')){
                    return '254'.substr($phone, 1);
                }else if(str_starts_with($phone, '+')){
                    return substr($phone, 1);
                }else{
                    return '254'.$phone;
                }
            }
        }
        return "error";
    }



}
