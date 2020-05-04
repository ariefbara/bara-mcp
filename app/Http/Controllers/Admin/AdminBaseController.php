<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Query\ {
    Application\Auth\AdminAuthorization,
    Domain\Model\Admin
};

class AdminBaseController extends Controller
{
    protected function authorizeUserIsAdmin()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        $authZ = new AdminAuthorization($adminRepository);
        $authZ->execute($this->adminId());
    }
    
    protected function adminId()
    {
        return $this->request->adminId;
    }
}
