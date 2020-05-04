<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Firm\FeedbackFormRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program
};
use Tests\TestBase;

class ConsultationSetupAddTest extends TestBase
{

    protected $service;
    protected $firmId = 'firm-id';
    protected $consultationSetupRepository;
    protected $programRepository, $program, $programId = 'program-id';
    protected $consultationFeedbackFormRepository, $consultationFeedbackForm,
            $consultationFeedbackFormId = 'consultation-feedback-form-id';
    protected $name = 'consultation setup name', $sessionDuration = 60;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfClass(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);

        $this->consultationFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultationFeedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->consultationFeedbackFormRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->consultationFeedbackFormId)
                ->willReturn($this->consultationFeedbackForm);

        $this->service = new ConsultationSetupAdd(
                $this->consultationSetupRepository, $this->programRepository, $this->consultationFeedbackFormRepository);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->programId, $this->name, $this->sessionDuration,
                        $this->consultationFeedbackFormId, $this->consultationFeedbackFormId);
    }

    public function test_execute_addConsultationSetupToRepository()
    {
        $this->consultationSetupRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }

    public function test_execute_returnNewId()
    {
        $this->consultationSetupRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }

}
