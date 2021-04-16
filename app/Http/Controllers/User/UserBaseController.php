<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Query\Domain\Model\User;

class UserBaseController extends Controller
{
    protected function userId()
    {
        return $this->request->userId;
    }
    
    protected function userQueryRepository()
    {
        return $this->em->getRepository(User::class);
    }
}
