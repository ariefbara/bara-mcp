<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\ConsultationSetupAdd,
    Application\Service\Firm\Program\ConsultationSetupRemove,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\ConsultationSetup
};
use Query\ {
    Application\Service\Firm\Program\ConsultationSetupView,
    Domain\Model\Firm\Program\ConsultationSetup as ConsultationSetup2
};

class ConsultationSetupController extends ManagerBaseController
{

    public function add($programId)
    {
        $service = $this->buildAddService();
        $name = $this->stripTagsInputRequest('name');
        $sessionDuration = $this->integerOfInputRequest('sessionDuration');
        $participantFeedbackFormId = $this->stripTagsInputRequest('participantFeedbackFormId');
        $consultantFeedbackFormId = $this->stripTagsInputRequest('consultantFeedbackFormId');

        $consultationSetupId = $service->execute(
                $this->firmId(), $programId, $name, $sessionDuration, $participantFeedbackFormId,
                $consultantFeedbackFormId);
        
        $viewService = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $consultationSetup = $viewService->showById($programCompositionId, $consultationSetupId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function remove($programId, $consultationSetupId)
    {
        $service = $this->buildRemoveService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $service->execute($programCompositionId, $consultationSetupId);

        return $this->commandOkResponse();
    }

    public function show($programId, $consultationSetupId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $consultationSetup = $service->showById($programCompositionId, $consultationSetupId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $consultationSetups = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($consultationSetups);
    }

    protected function arrayDataOfConsultationSetup(ConsultationSetup2 $consultationSetup)
    {
        return [
            "id" => $consultationSetup->getId(),
            "name" => $consultationSetup->getName(),
            "sessionDuration" => $consultationSetup->getSessionDuration(),
            "participantFeedbackForm" => [
                "id" => $consultationSetup->getParticipantFeedbackForm()->getId(),
                "name" => $consultationSetup->getParticipantFeedbackForm()->getName(),
            ],
            "consultantFeedbackForm" => [
                "id" => $consultationSetup->getConsultantFeedbackForm()->getId(),
                "name" => $consultationSetup->getConsultantFeedbackForm()->getName(),
            ],
        ];
    }

    protected function buildAddService()
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $programRepository = $this->em->getRepository(Program::class);
        $consultationFeedbackFormRepository = $this->em->getRepository(FeedbackForm::class);

        return new ConsultationSetupAdd($consultationSetupRepository, $programRepository,
                $consultationFeedbackFormRepository);
    }

    protected function buildRemoveService()
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        return new ConsultationSetupRemove($consultationSetupRepository);
    }

    protected function buildViewService()
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup2::class);
        return new ConsultationSetupView($consultationSetupRepository);
    }

}
