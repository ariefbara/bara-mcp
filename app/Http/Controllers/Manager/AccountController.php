<?php

namespace App\Http\Controllers\Manager;

use Query\ {
    Application\Service\Firm\ManagerView,
    Domain\Model\Firm\Manager
};
use User\ {
    Application\Service\Manager\ChangePassword,
    Domain\Model\Manager as Manager2
};

class AccountController extends ManagerBaseController
{
    public function changePassword()
    {
        $service = $this->buildChangePasswordService();
        $oldPassword = $this->stripTagsInputRequest("previousPassword");
        $newPassword = $this->stripTagsInputRequest("newPassword");
        
        $service->execute($this->firmId(), $this->managerId(), $oldPassword, $newPassword);
        return $this->commandOkResponse();
    }
    
    public function show()
    {
        $service = $this->buildViewService();
        $manager = $service->showById($this->firmId(), $this->managerId());
        return $this->singleQueryResponse($this->arrayDataOfManager($manager));
    }
    
    protected function arrayDataOfManager(Manager $manager): array
    {
        return [
            "id" => $manager->getId(),
            "name" => $manager->getName(),
            "email" => $manager->getEmail(),
            "phone" => $manager->getPhone(),
        ];
    }
    
    protected function buildViewService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        return new ManagerView($managerRepository);
    }
    
    protected function buildChangePasswordService()
    {
        $managerRepository = $this->em->getRepository(Manager2::class);
        return new ChangePassword($managerRepository);
    }
}
