<?php

namespace App\Http\Controllers\Admin;

use Bara\ {
    Application\Service\AdminAdd,
    Application\Service\AdminRemove,
    Application\Service\AdminView,
    Domain\Model\Admin,
    Domain\Model\AdminData
};

class AdminController extends AdminBaseController
{

    public function add()
    {
        $this->authorizeUserIsAdmin();
        
        $service = $this->buildAddService();
        
        $name = $this->stripTagsInputRequest('name');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        
        $adminData = new AdminData($name, $email);
        $admin = $service->execute($adminData, $password);
        return $this->commandCreatedResponse($this->arrayDataOfAdmin($admin));
    }

    public function remove($adminId)
    {
        $this->authorizeUserIsAdmin();
        
        $service = $this->buildRemoveService();
        $service->execute($adminId);
        return $this->commandOkResponse();
    }

    public function show($adminId)
    {
        $this->authorizeUserIsAdmin();
        
        $service = $this->buildViewService();
        $admin = $service->showById($adminId);
        return $this->singleQueryResponse($this->arrayDataOfAdmin($admin));
    }

    public function showAll()
    {
        $this->authorizeUserIsAdmin();
        
        $service = $this->buildViewService();
        $admins = $service->showAll($this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($admins);
    }
    
    private function arrayDataOfAdmin(Admin $admin)
    {
        return [
            "id" => $admin->getId(),
            "name" => $admin->getName(),
            "email" => $admin->getEmail(),
        ];
    }

    private function buildAddService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        return new AdminAdd($adminRepository);
    }
    protected function buildRemoveService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        return new AdminRemove($adminRepository);
    }
    private function buildViewService()
    {
        $adminRepository = $this->em->getRepository(Admin::class);
        return new AdminView($adminRepository);
    }

}
