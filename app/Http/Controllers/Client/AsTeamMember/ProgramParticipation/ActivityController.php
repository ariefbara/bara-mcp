<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use ActivityCreator\ {
    Application\Service\TeamMember\InitiateActivity,
    Application\Service\TeamMember\UpdateActivity,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\DependencyModel\Firm\Team\ProgramParticipation,
    Domain\Model\ParticipantActivity as ParticipantActivity2,
    Domain\service\ActivityDataProvider
};
use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\ {
    Application\Service\Firm\Team\ProgramParticipation\ViewParticipantActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity
};
use Resources\Application\Event\Dispatcher;

class ActivityController extends AsTeamMemberBaseController
{

    public function initiate($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("activityTypeId");

        $activityId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $activityTypeId, $this->buildActivityDataProvider());

        $viewService = $this->buildViewService();
        $participantActivity = $viewService->showById($this->firmId(), $teamId, $activityId);
        return $this->commandCreatedResponse($this->arrayDataOfParticipantActivity($participantActivity));
    }

    public function update($teamId, $teamProgramParticipationId, $activityId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $activityId, $this->buildActivityDataProvider());
        return $this->show($teamId, $teamProgramParticipationId, $activityId);
    }

    protected function buildActivityDataProvider()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        $dataProvider = new ActivityDataProvider(
                $managerRepository, $coordinatorRepository, $consultantRepository, $participantRepository, $name,
                $description, $startTime, $endTime, $location, $note);

        foreach ($this->request->input("invitedManagerList") as $managerId) {
            $dataProvider->addManagerInvitation($managerId);
        }
        foreach ($this->request->input("invitedCoordinatorList") as $consultantId) {
            $dataProvider->addCoordinatorInvitation($consultantId);
        }
        foreach ($this->request->input("invitedConsultantList") as $consultantId) {
            $dataProvider->addConsultantInvitation($consultantId);
        }
        foreach ($this->request->input("invitedParticipantList") as $participantId) {
            $dataProvider->addParticipantInvitation($participantId);
        }

        return $dataProvider;
    }

    public function show($teamId, $teamProgramParticipationId, $activityId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $participantActivity = $service->showById($this->firmId(), $teamId, $activityId);
        return $this->singleQueryResponse($this->arrayDataOfParticipantActivity($participantActivity));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $participantActivities = $service->showAll(
                $this->firmId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($participantActivities);
        foreach ($participantActivities as $participantActivity) {
            $result["list"][] = $this->arrayDataOfParticipantActivity($participantActivity);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfParticipantActivity(ParticipantActivity $participantActivity): array
    {
        return [
            "id" => $participantActivity->getId(),
            "activityType" => [
                "id" => $participantActivity->getActivityType()->getId(),
                "name" => $participantActivity->getActivityType()->getName(),
            ],
            "name" => $participantActivity->getName(),
            "description" => $participantActivity->getDescription(),
            "startTime" => $participantActivity->getStartTimeString(),
            "endTime" => $participantActivity->getEndTimeString(),
            "location" => $participantActivity->getLocation(),
            "note" => $participantActivity->getNote(),
            "cancelled" => $participantActivity->isCancelled(),
        ];
    }

    protected function buildViewService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity::class);
        return new ViewParticipantActivity($participantActivityRepository);
    }

    protected function buildInitiateService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(ProgramParticipation::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();
        
        return new InitiateActivity($participantActivityRepository, $teamMemberRepository, $teamParticipantRepository, $activityTypeRepository, $dispatcher);
    }

    protected function buildUpdateService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $dispatcher = new Dispatcher();
        
        return new UpdateActivity($participantActivityRepository, $teamMemberRepository, $dispatcher);
    }

}
