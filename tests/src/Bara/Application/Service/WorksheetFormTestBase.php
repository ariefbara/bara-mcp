<?php

namespace Tests\src\Bara\Application\Service;

use Bara\Application\Service\WorksheetFormRepository;
use Bara\Domain\Model\WorksheetForm;
use PHPUnit\Framework\MockObject\MockObject;

class WorksheetFormTestBase extends AdminTestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $worksheetFormRepository;
    /**
     * 
     * @var MockObject
     */
    protected $worksheetForm;
    protected $worksheetFormId = 'worksheetFormId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository->expects($this->any())
                ->method('ofId')
                ->with($this->worksheetFormId)
                ->willReturn($this->worksheetForm);
    }
}
