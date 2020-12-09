<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class UpdateConsultationSetupTest extends TestBase
{

    protected $consultationSetupRepository;
    protected $managerRepository, $manager;
    protected $feedbackFormRepository, $participantFeedbackForm, $consultantFeedbackForm;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $consultationSetupId = "consultationSetupId";
    protected $name = "new name", $sessionDuration = 99, $participantFeedbackFormId = "participantFeedbackFormId",
            $consultantFeedbackFormId = "consultantFeedbackFormId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->participantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);

        $this->service = new UpdateConsultationSetup(
                $this->consultationSetupRepository, $this->managerRepository, $this->feedbackFormRepository);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->managerId, $this->consultationSetupId, $this->name, $this->sessionDuration,
                $this->participantFeedbackFormId, $this->consultantFeedbackFormId);
    }

    public function test_execute_managerUpdateConsultationSetup()
    {
        $this->consultationSetupRepository->expects($this->once())->method("aConsultationSetupOfId");
        $this->feedbackFormRepository->expects($this->at(0))
                ->method("aFeedbackFormOfId")
                ->with($this->participantFeedbackFormId)
                ->willReturn($this->participantFeedbackForm);
        $this->feedbackFormRepository->expects($this->at(1))
                ->method("aFeedbackFormOfId")
                ->with($this->consultantFeedbackFormId)
                ->willReturn($this->consultantFeedbackForm);

        $this->manager->expects($this->once())
                ->method("updateConsultationSetup")
                ->with($this->anything(), $this->name, $this->sessionDuration,
                        $this->identicalTo($this->participantFeedbackForm),
                        $this->identicalTo($this->consultantFeedbackForm));
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->consultationSetupRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
