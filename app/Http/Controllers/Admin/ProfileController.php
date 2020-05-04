<?php

namespace App\Http\Controllers\Admin;

use Bara\ {
    Application\Service\AdminChangePassword,
    Application\Service\AdminChangeProfile,
    Domain\Model\Admin,
    Domain\Model\AdminData
};
use Query\ {
    Application\Service\AdminView,
    Domain\Model\Admin as Admin2
};

class ProfileController extends AdminBaseController
{
    public function update()
    {
        $service = $this->buildChangeProfilseService();
        
        $name = $this->stripTagsInputRequest('name');
        $email = $this->stripTagsInputRequest('email');
        
        $adminData = new AdminData($name, $email);
        $service->execute($this->adminId(), $adminData);
        
        $adminRepository = $this->em->getRepository(Admin2::class);
        $viewService = new AdminView($adminRepository);
        $admin = $viewService->showById($this->adminId());
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
    
    private function arrayDataOfAdmin(Admin2 $admin)
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
