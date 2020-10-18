<?php

namespace App\Http\Controllers\User\AsProgramParticipant\Mission;

use App\Http\Controllers\User\AsProgramParticipant\AsProgramParticipantBaseController;
use Config\EventList;
use Participant\ {
    Application\Listener\LearningMaterialAccessedByParticipantListener,
    Application\Service\Participant\LogViewLearningMaterialActivity,
    Domain\Model\Participant,
    Domain\Model\Participant\ViewLearningMaterialActivityLog
};
use Query\ {
    Application\Service\Firm\Program\Mission\ViewLearningMaterial,
    Application\Service\User\AsProgramParticipant\ViewLearningMaterialDetail,
    Domain\Model\Firm\Program\Mission\LearningMaterial,
    Domain\Model\User\UserParticipant,
    Domain\Service\LearningMaterialFinder
};
use Resources\Application\Event\Dispatcher;

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildViewDetailService();
        $learningMaterial = $service->execute($this->userId(), $programId, $learningMaterialId);

        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($firmId, $programId, $missionId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);

        $service = $this->buildViewService();
        $learningMaterials = $service->showAll($firmId, $programId, $missionId, $this->getPage(), $this->getPageSize());
        
        return $this->commonIdNameListQueryResponse($learningMaterials);
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
        ];
    }

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }

    protected function buildViewDetailService()
    {
        $userProgramParticipationRepository = $this->em->getRepository(UserParticipant::class);
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $learningMaterialFinder = new LearningMaterialFinder($learningMaterialRepository);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::LEARNING_MATERIAL_VIEWED_BY_PARTICIPANT, $this->buildLearningMaterialAccessedListener());

        return new ViewLearningMaterialDetail($userProgramParticipationRepository, $learningMaterialFinder, $dispatcher);
    }
    protected function buildLearningMaterialAccessedListener()
    {
        $viewLearningMaterialActivityLogRepository = $this->em->getRepository(ViewLearningMaterialActivityLog::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $logViewLearningMaterialActivity = new LogViewLearningMaterialActivity($viewLearningMaterialActivityLogRepository,
                $participantRepository);
        return new LearningMaterialAccessedByParticipantListener($logViewLearningMaterialActivity);
    }

}
