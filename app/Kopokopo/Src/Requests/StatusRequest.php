<?php

namespace App\Kopokopo\Src\Requests;

class StatusRequest extends BaseRequest
{
    public function getLocation()
    {
        return $this->getRequestData('location');
    }
}
