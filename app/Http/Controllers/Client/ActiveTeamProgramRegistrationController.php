<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Firm\Client\ViewTeamMembership;
use Query\Application\Service\Firm\Team\ViewTeamProgramRegistration;
use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

class ActiveTeamProgramRegistrationController extends ClientBaseController
{
    public function showAll()
    {
        $teamMembershipRepository = $this->em->getRepository(Member::class);
        $teamMemberships = (new ViewTeamMembership($teamMembershipRepository))
                ->showAll($this->firmId(), $this->clientId(), 1, 100, $activeStatus = true);
        $result = [];
        foreach ($teamMemberships as $teamMembership) {
            $result['list'][] = $this->arrayDataOfTeamMembership($teamMembership);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfTeamMembership(Member $teamMembership): array
    {
        return [
            'id' => $teamMembership->getId(),
            'team' => [
                'id' => $teamMembership->getTeam()->getId(),
                'name' => $teamMembership->getTeam()->getName(),
                'programRegistrations' => $this->allDataOfTeamProgramRegistrations($teamMembership->getTeam()->getId()),
            ],
        ];
    }
    protected function allDataOfTeamProgramRegistrations(string $teamId): array
    {
        $result = [];
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamProgramRegistrations = (new ViewTeamProgramRegistration($teamProgramRegistrationRepository))
                ->showAll($this->firmId(), $teamId, 1, 100, $concludedStatus = false);
        foreach ($teamProgramRegistrations as $teamProgramRegistration) {
            $result[] = [
                'id' => $teamProgramRegistration->getId(),
                'registeredTime' => $teamProgramRegistration->getRegisteredTimeString(),
                'program' => [
                    'id' => $teamProgramRegistration->getProgram()->getId(),
                    'name' => $teamProgramRegistration->getProgram()->getName(),
                    "illustration" => $this->arrayDataOfIllustration($teamProgramRegistration->getProgram()->getIllustration()),
                ],
            ];
        }
        return $result;
    }
    
    protected function arrayDataOfIllustration(?FirmFileInfo $illustration): ?array
    {
        return empty($illustration)? null: [
            "id" => $illustration->getId(),
            "url" => $illustration->getFullyQualifiedFileName(),
        ];
    }
    
}
