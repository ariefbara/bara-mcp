<?php

namespace App\Http\Controllers\Client\TeamMembership;

use App\Http\Controllers\Client\ClientBaseController;
use Participant\{
    Application\Service\Firm\Client\TeamMembership\CancelTeamProgramRegistration,
    Application\Service\Firm\Client\TeamMembership\RegisterTeamToProgram,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program,
    Domain\Model\TeamProgramRegistration
};
use Query\{
    Application\Service\Firm\Client\TeamMembership\ViewTeamProgramRegistration,
    Domain\Model\Firm\Team\TeamProgramRegistration as TeamProgramRegistration2
};

class ProgramRegistrationController extends TeamMembershipBaseController
{

    public function register($teamMembershipId)
    {
        $service = $this->buildRegisterService();
        $programId = $this->stripTagsInputRequest("programId");
        $teamProgramRegsistrationId = $service->execute($this->firmId(), $this->clientId(), $teamMembershipId,
                $programId);

        $viewService = $this->buildViewService();
        $teamProgramRegistration = $viewService->showById($this->firmId(), $this->clientId(), $teamMembershipId,
                $teamProgramRegsistrationId);
        return $this->commandCreatedResponse($this->arrayDataOfTeamProgramRegistration($teamProgramRegistration));
    }

    public function cancel($teamMembershipId, $teamProgramRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramRegistrationId);
        return $this->commandOkResponse();
    }

    public function show($teamMembershipId, $teamProgramRegistrationId)
    {
        $service = $this->buildViewService();
        $teamProgramRegistration = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramRegistration($teamProgramRegistration));
    }

    public function showAll($teamMembershipId)
    {
        $service = $this->buildViewService();
        $teamProgramRegistrations = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $this->getPage(), $this->getPageSize());

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
        return new ViewTeamProgramRegistration(
                $teamProgramRegistrationRepository, $this->buildActiveTeamMembershipAuthorization());
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
