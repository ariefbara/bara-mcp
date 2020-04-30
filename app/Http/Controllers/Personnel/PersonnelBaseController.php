<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;

class PersonnelBaseController extends Controller
{
    protected function firmId()
    {
        return $this->request->firmId;
    }
    protected function personnelId()
    {
        return $this->request->personnelId;
    }
}
