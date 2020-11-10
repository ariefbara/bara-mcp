<?php

namespace App\Http\Controllers\User\AsFirmParticipant;

use Query\{
    Application\Service\Firm\ManagerView,
    Domain\Model\Firm\Manager
};

class ManagerController extends AsFirmParticipantBaseController
{

    public function show($firmId, $managerId)
    {
        $this->authorizeUserIsActiveParticipantInFirm($firmId);

        $service = $this->buildViewService();
        $manager = $service->showById($firmId, $managerId);
        return $this->singleQueryResponse($this->arrayDataOfManager($manager));
    }

    public function showAll($firmId)
    {
        $this->authorizeUserIsActiveParticipantInFirm($firmId);

        $service = $this->buildViewService();
        $managers = $service->showAll($firmId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($managers);
        foreach ($managers as $manager) {
            $result["list"][] = $this->arrayDataOfManager($manager);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfManager(Manager $manager): array
    {
        return [
            "id" => $manager->getId(),
            "name" => $manager->getName(),
            "removed" => $manager->isRemoved(),
        ];
    }

    protected function buildViewService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        return new ManagerView($managerRepository);
    }

}
