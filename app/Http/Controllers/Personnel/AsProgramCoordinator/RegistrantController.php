<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Firm\ {
    Application\Service\Firm\Program\AcceptRegistrant,
    Application\Service\Firm\Program\RejectRegistrant,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Registrant as Registrant2
};
use Query\ {
    Application\Service\Firm\Program\ViewRegistrant,
    Domain\Model\Firm\Client\ClientRegistrant,
    Domain\Model\Firm\Program\Registrant,
    Domain\Model\Firm\Team\TeamProgramRegistration,
    Domain\Model\User\UserRegistrant
};
use Resources\Application\Event\Dispatcher;

class RegistrantController extends AsProgramCoordinatorBaseController
{
    public function accept($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildAcceptService();
        $service->execute($this->firmId(), $programId, $registrantId);
        
        return $this->show($programId, $registrantId);
    }
    public function reject($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildRejectService();
        $service->execute($this->firmId(), $programId, $registrantId);
        
        return $this->commandOkResponse();
    }
    
    public function show($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $registrant = $service->showById($this->firmId(), $programId, $registrantId);
        return $this->singleQueryResponse($this->arrayDataOfRegistrant($registrant));
    }
    
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $registrants = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($registrants);
        
        foreach ($registrants as $registrant) {
            $result['list'][] = $this->arrayDataOfRegistrant($registrant);
        }
        return $this->listQueryResponse($result);
    }
    
    public function arrayDataOfRegistrant(Registrant $registrant): array
    {
        return [
            "id" => $registrant->getId(),
            "registeredTime" => $registrant->getRegisteredTimeString(),
            "note" => $registrant->getNote(),
            "concluded" => $registrant->isConcluded(),
            "user" => $this->arrayDataOfUser($registrant->getUserRegistrant()),
            "client" => $this->arrayDataOfClient($registrant->getClientRegistrant()),
            "team" => $this->arrayDataOfTeam($registrant->getTeamRegistrant()),
        ];
    }
    
    protected function arrayDataOfUser(?UserRegistrant $userRegistrant): ?array
    {
        return empty($userRegistrant)? null: [
            "id" => $userRegistrant->getUser()->getId(),
            "name" => $userRegistrant->getUser()->getFullName(),
        ];
    }
    
    protected function arrayDataOfClient(?ClientRegistrant $clientRegistrant): ?array
    {
        return empty($clientRegistrant)? null: [
            "id" => $clientRegistrant->getClient()->getId(),
            "name" => $clientRegistrant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramRegistration $teamRegistrant): ?array
    {
        return empty($teamRegistrant)? null: [
            "id" => $teamRegistrant->getTeam()->getId(),
            "name" => $teamRegistrant->getTeam()->getName(),
        ];
    }
    
    public function buildViewService()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        return new ViewRegistrant($registrantRepository);
    }
    protected function buildRejectService()
    {
        $registrantRepository = $this->em->getRepository(Registrant2::class);
        return  new RejectRegistrant($registrantRepository);
    }
    protected function buildAcceptService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();
        return new AcceptRegistrant($programRepository, $dispatcher);
    }
}
