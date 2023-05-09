<?php

namespace App\Http\Controllers\Client\AsProgramParticipant\Mission;

use App\Http\Controllers\Client\AsProgramParticipant\AsProgramParticipantBaseController;
use Config\EventList;
use Participant\Application\Listener\LearningMaterialAccessedByParticipantListener;
use Participant\Application\Service\Participant\LogViewLearningMaterialActivity;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ViewLearningMaterialActivityLog;
use Query\Application\Service\Firm\Client\AsProgramParticipant\ViewLearningMaterialDetail;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Service\LearningMaterialFinder;
use Resources\Application\Event\Dispatcher;

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function show($programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildViewDetailService();
        $learningMaterial = $service->execute($this->clientId(), $programId, $learningMaterialId);

        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($programId, $missionId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);

        $service = $this->buildViewService();
        $learningMaterials = $service->showAll($this->firmId(), $programId, $missionId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result['total'] = count($learningMaterials);
        foreach ($learningMaterials as $learningMaterial) {
            $learningAttachments = [];
            foreach ($learningMaterial->iterateAllActiveLearningAttachments() as $learningAttachment) {
                $learningAttachments[] = [
                    'id' => $learningAttachment->getId(),
                    'firmFileInfo' => [
                        'id' => $learningAttachment->getFirmFileInfo()->getId(),
                        'contentType' => $learningAttachment->getFirmFileInfo()->getFileInfo()->getContentType(),
                    ],
                ];
            }
            $result["list"][] = [
                "id" => $learningMaterial->getId(),
                "name" => $learningMaterial->getName(),
                'learningAttachments' => $learningAttachments,
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        $learningAttachments = [];
        foreach ($learningMaterial->iterateAllActiveLearningAttachments() as $learningAttachment) {
            $learningAttachments[] = [
                'id' => $learningAttachment->getId(),
                'firmFileInfo' => [
                    'id' => $learningAttachment->getFirmFileInfo()->getId(),
                    'path' => $learningAttachment->getFirmFileInfo()->getFullyQualifiedFileName($this->createGoogleStorage()),
                    'contentType' => $learningAttachment->getFirmFileInfo()->getFileInfo()->getContentType(),
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

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }

    protected function buildViewDetailService()
    {
        $clientProgramParticipationRepository = $this->em->getRepository(ClientParticipant::class);
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $learningMaterialFinder = new LearningMaterialFinder($learningMaterialRepository);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::LEARNING_MATERIAL_VIEWED_BY_PARTICIPANT,
                $this->buildLearningMaterialAccessedByParticipantListener());

        return new ViewLearningMaterialDetail(
                $clientProgramParticipationRepository, $learningMaterialFinder, $dispatcher);
    }

    protected function buildLearningMaterialAccessedByParticipantListener()
    {
        $viewLearningMaterialActivityLogRepository = $this->em->getRepository(ViewLearningMaterialActivityLog::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $logViewLearningMaterialActivity = new LogViewLearningMaterialActivity($viewLearningMaterialActivityLogRepository,
                $participantRepository);
        return new LearningMaterialAccessedByParticipantListener($logViewLearningMaterialActivity);
    }

}
