<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm,
    Domain\Model\Shared\FormData
};
use Tests\TestBase;

class ConsultationFeedbackFormAddTest extends TestBase
{

    protected $consultationFeedbackFormRepository;
    protected $firmRepository, $firm, $firmId = 'firm-id';
    protected $service;
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackFormRepository = $this->buildMockOfInterface(ConsultationFeedbackFormRepository::class);

        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId)
            ->willReturn($this->firm);

        $this->service = new ConsultationFeedbackFormAdd($this->consultationFeedbackFormRepository, 
                $this->firmRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->formData);
    }

    public function test_execute_addConsultationFeedbackFormToRepository()
    {
        $this->consultationFeedbackFormRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }

}
