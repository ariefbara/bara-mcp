<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class UserBaseController extends Controller
{
    protected function userId()
    {
        return $this->request->userId;
    }
}
