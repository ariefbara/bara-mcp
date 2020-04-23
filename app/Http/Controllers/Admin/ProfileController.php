<?php

namespace App\Http\Controllers\Admin;

use Bara\ {
    Application\Service\AdminChangePassword,
    Application\Service\AdminChangeProfile,
    Domain\Model\Admin,
    Domain\Model\AdminData
};

class ProfileController extends AdminBaseController
{
    public function update()
    {
        $service = $this->buildChangeProfilseService();
        
        $name = $this->stripTagsInputRequest('name');
        $email = $this->stripTagsInputRequest('email');
        
        $adminData = new AdminData($name, $email);
        $admin = $service->execute($this->adminId(), $adminData);
        return $this->singleQueryResponse($this->arrayDataOfAdmin($admin));
    }
    
    public function changePassword()
    {
        $service = $this->buildChangePasswordService();
        $previousPassword = $this->stripTagsInputRequest('previousPassword');
        $newPassword = $this->stripTagsInputRequest('newPassword');
        $service->execute($this->adminId(), $previousPassword, $newPassword);
        return $this->commandOkResponse();
    }
    
    private function arrayDataOfAdmin(Admin $admin)
    {
        return [
            "id" => $admin->getId(),
            "name" => $admin->getName(),
            "email" => $admin->getEmail(),
        ];
    }
    
    protected function buildChangeProfilseService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        return new AdminChangeProfile($adminRepository);
    }
    protected function buildChangePasswordService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        return new AdminChangePassword($adminRepository);
    }
}
