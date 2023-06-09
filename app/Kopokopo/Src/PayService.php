<?php

namespace App\Kopokopo\Src;

// require 'vendor/autoload.php';

use App\Kopokopo\Src\Requests\PayRecipientMobileRequest;
use App\Kopokopo\Src\Requests\PayRecipientAccountRequest;
use App\Kopokopo\Src\Requests\PayRecipientTillRequest;
use App\Kopokopo\Src\Requests\PayRecipientPaybillRequest;
use App\Kopokopo\Src\Requests\PayRequest;
use App\Kopokopo\Src\Data\FailedResponseData;
use Exception;

use GuzzleHttp\Client;


class PayService extends Service
{
    public function addPayRecipient($options)
    {
        try {
            if (!isset($options['type'])) {
                throw new \InvalidArgumentException('You have to provide the type');
            } elseif ($options['type'] === 'bank_account') {
                $payRecipientrequest = new PayRecipientAccountRequest($options);
            } elseif ($options['type'] === 'till') {
                $payRecipientrequest = new PayRecipientTillRequest($options);
            } elseif ($options['type'] === 'paybill') {
                $payRecipientrequest = new PayRecipientPaybillRequest($options);
            } elseif ($options['type'] === 'mobile_wallet') {
                $payRecipientrequest = new PayRecipientMobileRequest($options);
            } else{
                throw new \InvalidArgumentException('Invalid recipient type');
            }

            $response = $this->client->post('pay_recipients', ['body' => json_encode($payRecipientrequest->getPayRecipientBody()), 'headers' => $payRecipientrequest->getHeaders()]);

            return $this->postSuccess($response);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $dataHandler = new FailedResponseData();
            return $this->error($dataHandler->setErrorData($e));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sendPay($options)
    {
        $payRequest = new PayRequest($options);
        try {
            $response = $this->client->post('payments', ['body' => json_encode($payRequest->getPayBody()), 'headers' => $payRequest->getHeaders()]);

            return $this->postSuccess($response);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $dataHandler = new FailedResponseData();
            return $this->error($dataHandler->setErrorData($e));
        } catch(\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
