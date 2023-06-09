<?php

namespace App\Kopokopo\Src;

// require 'vendor/autoload.php';

use App\Kopokopo\Src\Requests\PollingRequest;
use App\Kopokopo\Src\Data\FailedResponseData;
use Exception;

class PollingService extends Service
{
    public function pollTransactions($options)
    {
        $pollingRequest = new PollingRequest($options);
        try {
            $response = $this->client->post('polling', ['body' => json_encode($pollingRequest->getPollingRequestBody()), 'headers' => $pollingRequest->getHeaders()]);

            return $this->postSuccess($response);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $dataHandler = new FailedResponseData();
            return $this->error($dataHandler->setErrorData($e));
        } catch(\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
