<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Application\Service\Firm\Program\ConsultationSetupAdd;
use Firm\Application\Service\Firm\Program\ConsultationSetupRemove;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\UpdateConsultationSetup;
use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Application\Service\Firm\Program\ViewConsultationSetup;
use Query\Domain\Model\Firm\Program\ConsultationSetup as ConsultationSetup2;

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
        $consultationSetup = $viewService->showById($this->firmId(), $programId, $consultationSetupId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function update($programId, $consultationSetupId)
    {
        $name = $this->stripTagsInputRequest('name');
        $sessionDuration = $this->integerOfInputRequest('sessionDuration');
        $participantFeedbackFormId = $this->stripTagsInputRequest('participantFeedbackFormId');
        $consultantFeedbackFormId = $this->stripTagsInputRequest('consultantFeedbackFormId');
        
        $this->buildUpdateService()->execute(
                $this->firmId(), $this->managerId(), $consultationSetupId, $name, $sessionDuration,
                $participantFeedbackFormId, $consultantFeedbackFormId);
        
        return $this->show($programId, $consultationSetupId);
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
        $consultationSetup = $service->showById($this->firmId(), $programId, $consultationSetupId);
        return $this->singleQueryResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $consultationSetups = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
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
        return new ViewConsultationSetup($consultationSetupRepository);
    }

    protected function buildUpdateService()
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);

        return new UpdateConsultationSetup($consultationSetupRepository, $managerRepository, $feedbackFormRepository);
    }

}
