<?php

namespace Firm\Application\Service\Firm;

use Firm\{
    Application\Service\FirmRepository,
    Domain\Model\Firm,
    Domain\Model\Shared\FormData
};
use Tests\TestBase;

class FeedbackFormAddTest extends TestBase
{

    protected $feedbackFormRepository;
    protected $firmRepository, $firm, $firmId = 'firm-id';
    protected $service;
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);

        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firmRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId)
                ->willReturn($this->firm);

        $this->service = new FeedbackFormAdd($this->feedbackFormRepository, $this->firmRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->formData);
    }

    public function test_execute_addFeedbackFormToRepository()
    {
        $this->feedbackFormRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }

    public function test_execute_returnId()
    {
        $this->feedbackFormRepository->expects($this->once())
                ->method('nextIdentity')
                ->willReturn($id = 'id');
        $this->assertEquals($id, $this->execute());
    }

}
