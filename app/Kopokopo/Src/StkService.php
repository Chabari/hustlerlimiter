<?php

namespace App\Kopokopo\Src;

// require 'vendor/autoload.php';

use App\Kopokopo\Src\Requests\StkIncomingPaymentRequest;
use App\Kopokopo\Src\Data\FailedResponseData;
use Exception;

class StkService extends Service
{
    public function initiateIncomingPayment($options)
    {
        $stkPaymentRequest = new StkIncomingPaymentRequest($options);
        try {
            $response = $this->client->post('incoming_payments', ['body' => json_encode($stkPaymentRequest->getPaymentRequestBody()), 'headers' => $stkPaymentRequest->getHeaders()]);

            return $this->postSuccess($response);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $dataHandler = new FailedResponseData();
            return $this->error($dataHandler->setErrorData($e));
        } catch(\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
