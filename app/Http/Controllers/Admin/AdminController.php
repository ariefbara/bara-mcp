<?php

namespace App\Http\Controllers\Admin;

use Bara\ {
    Application\Service\AdminAdd,
    Application\Service\AdminRemove,
    Domain\Model\Admin,
    Domain\Model\AdminData
};
use Query\ {
    Application\Service\AdminView,
    Domain\Model\Admin as QueryAdmin
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
        $adminId = $service->execute($adminData, $password);
        
        $viewService = $this->buildViewService();
        $admin = $viewService->showById($adminId);
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
    
    private function arrayDataOfAdmin(QueryAdmin $admin)
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
        $adminRepository = $this->em->getRepository(QueryAdmin::class);
        return new AdminView($adminRepository);
    }

}
