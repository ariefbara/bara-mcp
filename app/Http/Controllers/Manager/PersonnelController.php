<?php

namespace App\Http\Controllers\Manager;

use Firm\Application\Service\Firm\PersonnelAdd;
use Firm\Application\Service\Manager\DisablePersonnel;
use Firm\Application\Service\Manager\EnablePersonnel;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Personnel;
use Firm\Domain\Model\Firm\PersonnelData;
use Query\Application\Service\Firm\PersonnelView;
use Query\Domain\Model\Firm\Personnel as Personnel2;

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
    
    public function disable($personnelId)
    {
        $this->buildDisableService()
                ->execute($this->firmId(), $this->managerId(), $personnelId);
        return $this->commandOkResponse();
    }
    public function enable($personnelId)
    {
        $this->buildEnableService()
                ->execute($this->firmId(), $this->managerId(), $personnelId);
        return $this->show($personnelId);
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
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $personnels = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $activeStatus);
        return $this->commonIdNameListQueryResponse($personnels);
    }

    protected function getPersonnelData()
    {
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        $email = $this->stripTagsInputRequest('email');
        $password = $this->stripTagsInputRequest('password');
        $phone = $this->stripTagsInputRequest('phone');
        $bio = $this->stripTagsInputRequest('bio');
        
        return new PersonnelData($firstName, $lastName, $email, $password, $phone, $bio);
    }

    protected function arrayDataOfPersonnel(Personnel2 $personnel)
    {
        return [
            "id" => $personnel->getId(),
            "name" => $personnel->getName(),
            "email" => $personnel->getEmail(),
            "phone" => $personnel->getPhone(),
            "bio" => $personnel->getBio(),
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
    
    protected function buildDisableService()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new DisablePersonnel($personnelRepository, $managerRepository);
    }
    protected function buildEnableService()
    {
        $personnelRepository = $this->em->getRepository(Personnel::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new EnablePersonnel($personnelRepository, $managerRepository);
    }

}
