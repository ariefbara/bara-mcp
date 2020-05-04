<?php

namespace App\Http\Controllers\Manager;

use Firm\ {
    Application\Service\Firm\PersonnelAdd,
    Domain\Model\Firm,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\PersonnelData
};
use Query\ {
    Application\Service\Firm\PersonnelView,
    Domain\Model\Firm\Personnel as Personnel2
};

class PersonnelController extends ManagerBaseController
{

    public function add()
    {
        $this->authorizedUserIsFirmManager();

        $service = $this->buildAddService();
        $personnelId = $service->execute($this->firmId(), $this->getPersonnelData());
        
        $viewservice = $this->buildViewService();
        $personnel = $viewservice->showById($this->firmId(), $personnelId);
        return $this->commandCreatedResponse($this->arrayDataOfPersonnel($personnel));
    }

    public function show($personnelId)
    {
        $this->authorizedUserIsFirmManager();

        $service = $this->buildViewService();
        $personnel = $service->showById($this->firmId(), $personnelId);

        return $this->singleQueryResponse($this->arrayDataOfPersonnel($personnel));
    }

    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $personnels = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($personnels);
    }

    protected function getPersonnelData()
    {
        $name = $this->stripTagsInputRequest('name');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $phone = $this->stripTagsInputRequest('phone');

        return new PersonnelData($name, $email, $password, $phone);
    }

    protected function arrayDataOfPersonnel(Personnel2 $personnel)
    {
        return [
            "id" => $personnel->getId(),
            "name" => $personnel->getName(),
            "email" => $personnel->getEmail(),
            "phone" => $personnel->getPhone(),
            "joinTime" => $personnel->getJoinTimeString(),
        ];
    }

    protected function buildAddService()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        return new PersonnelAdd($personnelRepository, $firmRepository);
    }

    protected function buildViewService()
    {
        $personnelRepository = $this->em->getRepository(Personnel2::class);
        return new PersonnelView($personnelRepository);
    }

}
