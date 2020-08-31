<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ClientBaseController extends Controller
{
    protected function clientId()
    {
        return $this->request->clientId;
    }
    protected function firmId()
    {
        return $this->request->firmId;
    }
}
