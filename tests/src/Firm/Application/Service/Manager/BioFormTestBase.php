<?php

namespace Tests\src\Firm\Application\Service\Manager;

use Firm\Application\Service\Manager\BioFormRepository;
use Firm\Domain\Model\Firm\BioForm;
use PHPUnit\Framework\MockObject\MockObject;

class BioFormTestBase extends ManagerTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $bioForm;

    /**
     * 
     * @var MockObject
     */
    protected $bioFormRepository;
    protected $bioFormId = "bioFormId";
    protected $nextId = "nextId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioForm = $this->buildMockOfClass(BioForm::class);
        $this->bioFormRepository = $this->buildMockOfClass(BioFormRepository::class);

        $this->bioFormRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->bioFormRepository->expects($this->any())
                ->method("ofId")
                ->with($this->bioFormId)
                ->willReturn($this->bioForm);
    }

}
