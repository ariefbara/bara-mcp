<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant\Mission;

use App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant\AsProgramParticipantBaseController;
use Config\EventList;
use Participant\ {
    Application\Listener\LearningMaterialAccessedByTeamMemberListener,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\LogViewLearningMaterialActivity,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant,
    Domain\Model\Participant\ViewLearningMaterialActivityLog
};
use Query\ {
    Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant\ViewLearningMaterialDetail,
    Application\Service\Firm\Program\Mission\ViewLearningMaterial,
    Domain\Model\Firm\Program\Mission\LearningMaterial,
    Domain\Model\Firm\Team\Member,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Service\LearningMaterialFinder,
    Domain\Service\TeamProgramParticipationFinder
};
use Resources\Application\Event\Dispatcher;

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function showAll($teamId, $programId, $missionId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveProgramParticipant($teamId, $programId);

        $service = $this->buildViewAllService();
        $learningMaterials = $service->showAll($this->firmId(), $programId, $missionId, $this->getPage(),
                $this->getPageSize());
        return $this->commonIdNameListQueryResponse($learningMaterials);
    }

    public function show($teamId, $programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildViewDetailService();
        $learningMaterial = $service->execute($this->clientId(), $teamId, $programId, $learningMaterialId);
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
            "removed" => $learningMaterial->isRemoved(),
        ];
    }

    protected function buildViewAllService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }

    protected function buildViewDetailService()
    {
        $teamMemberRepositoryRepository = $this->em->getRepository(Member::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $teamProgramParticipationFinder = new TeamProgramParticipationFinder($teamProgramParticipationRepository);
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $learningMaterialFinder = new LearningMaterialFinder($learningMaterialRepository);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::LEARNING_MATERIAL_VIEWED_BY_PARTICIPANT, $this->buildLogViewLearningMaterialActivity());
        return new ViewLearningMaterialDetail(
                $teamMemberRepositoryRepository, $teamProgramParticipationFinder, $learningMaterialFinder, $dispatcher);
    }

    protected function buildLogViewLearningMaterialActivity()
    {
        $viewLearningMaterialActivityLogRepository = $this->em->getRepository(ViewLearningMaterialActivityLog::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $logViewLearningMaterialActivity = new LogViewLearningMaterialActivity(
                $viewLearningMaterialActivityLogRepository, $teamMembershipRepository, $participantRepository);
        return new LearningMaterialAccessedByTeamMemberListener($logViewLearningMaterialActivity);
    }

}
