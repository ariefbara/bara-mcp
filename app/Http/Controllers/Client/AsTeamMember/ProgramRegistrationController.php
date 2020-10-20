<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\CancelTeamProgramRegistration,
    Application\Service\Firm\Client\TeamMembership\RegisterTeamToProgram,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program,
    Domain\Model\TeamProgramRegistration
};
use Query\ {
    Application\Service\Firm\Team\ViewTeamProgramRegistration,
    Domain\Model\Firm\Team\TeamProgramRegistration as TeamProgramRegistration2
};

class ProgramRegistrationController extends AsTeamMemberBaseController
{

    public function register($teamId)
    {
        $service = $this->buildRegisterService();
        $programId = $this->stripTagsInputRequest("programId");
        $teamProgramRegistrationId = $service->execute($this->firmId(), $this->clientId(), $teamId, $programId);

        $viewService = $this->buildViewService();
        $teamProgramRegistration = $viewService->showById($this->firmId(), $teamId, $teamProgramRegistrationId);
        return $this->commandCreatedResponse($this->arrayDataOfTeamProgramRegistration($teamProgramRegistration));
    }

    public function cancel($teamId, $teamProgramRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramRegistrationId);
        return $this->commandOkResponse();
    }

    public function show($teamId, $teamProgramRegistrationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $teamProgramRegistration = $service->showById($this->firmId(), $teamId, $teamProgramRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramRegistration($teamProgramRegistration));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $concludedStatus = $this->filterBooleanOfQueryRequest("concludedStatus");
        $teamProgramRegistrations = $service->showAll(
                $this->firmId(), $teamId, $this->getPage(), $this->getPageSize(), $concludedStatus);

        $result = [];
        $result["total"] = count($teamProgramRegistrations);
        foreach ($teamProgramRegistrations as $teamProgramRegistration) {
            $result["list"][] = $this->arrayDataOfTeamProgramRegistration($teamProgramRegistration);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfTeamProgramRegistration(TeamProgramRegistration2 $teamProgramRegistration): array
    {
        return [
            "id" => $teamProgramRegistration->getId(),
            "registeredTime" => $teamProgramRegistration->getRegisteredTimeString(),
            "note" => $teamProgramRegistration->getNote(),
            "concluded" => $teamProgramRegistration->isConcluded(),
            "program" => [
                "id" => $teamProgramRegistration->getProgram()->getId(),
                "name" => $teamProgramRegistration->getProgram()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration2::class);
        return new ViewTeamProgramRegistration($teamProgramRegistrationRepository);
    }

    protected function buildRegisterService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $programRepository = $this->em->getRepository(Program::class);

        return new RegisterTeamToProgram($teamProgramRegistrationRepository, $teamMembershipRepository,
                $programRepository);
    }

    protected function buildCancelService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new CancelTeamProgramRegistration($teamProgramRegistrationRepository, $teamMembershipRepository);
    }

}
