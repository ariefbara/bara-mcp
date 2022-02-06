<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant\Mission;

use App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant\AsProgramParticipantBaseController;
use Config\EventList;
use Participant\Application\Listener\LearningMaterialAccessedByTeamMemberListener;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\LogViewLearningMaterialActivity;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ViewLearningMaterialActivityLog;
use Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant\ViewLearningMaterialDetail;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\TeamProgramParticipationFinder;
use Resources\Application\Event\Dispatcher;

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function showAll($teamId, $programId, $missionId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveParticipantOfProgram($teamId, $programId);

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
        $learningAttachments = [];
        foreach ($learningMaterial->iterateAllActiveLearningAttachments() as $learningAttachment) {
            $learningAttachments[] = [
                'id' => $learningAttachment->getId(),
                'firmFileInfo' => [
                    'id' => $learningAttachment->getFirmFileInfo()->getId(),
                    'path' => $learningAttachment->getFirmFileInfo()->getFullyQualifiedFileName(),
                ],
            ];
        }
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
            'learningAttachments' => $learningAttachments,
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
