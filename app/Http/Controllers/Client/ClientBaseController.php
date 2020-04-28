<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ClientBaseController extends Controller
{
    protected function clientId()
    {
        return $this->request->clientId;
    }
}
