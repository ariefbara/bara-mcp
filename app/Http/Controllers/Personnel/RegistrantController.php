<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Personnel\ViewRegistrant;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;
use Query\Domain\Model\User\UserRegistrant;

class RegistrantController extends PersonnelBaseController
{
    public function showAll()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        $service = new ViewRegistrant($this->personnelQueryRepository(), $registrantRepository);
        
        $concludedStatus = $this->filterBooleanOfQueryRequest('concludedStatus');
        $registrants = $service->showAll(
                $this->firmId(), $this->personnelId(), $this->getPage(), $this->getPageSize(), $concludedStatus);
        
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
            'program' => [
                "id" => $registrant->getProgram()->getId(),
                "name" => $registrant->getProgram()->getName(),
            ],
        ];
    }
    protected function arrayDataOfUser(?UserRegistrant $userRegistrant): ?array
    {
        return empty($userRegistrant) ? null : [
            "id" => $userRegistrant->getUser()->getId(),
            "name" => $userRegistrant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?ClientRegistrant $clientRegistrant): ?array
    {
        return empty($clientRegistrant) ? null : [
            "id" => $clientRegistrant->getClient()->getId(),
            "name" => $clientRegistrant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramRegistration $teamRegistrant): ?array
    {
        return empty($teamRegistrant) ? null : [
            "id" => $teamRegistrant->getTeam()->getId(),
            "name" => $teamRegistrant->getTeam()->getName(),
        ];
    }
}
