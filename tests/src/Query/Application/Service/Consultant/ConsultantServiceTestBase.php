<?php

namespace Tests\src\Query\Application\Service\Consultant;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Consultant\ConsultantRepository;
use Query\Domain\Model\Firm\Program\Consultant;
use Tests\TestBase;

class ConsultantServiceTestBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $consultantRepository;

    /**
     * 
     * @var MockObject
     */
    protected $consultant;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $consultantId = 'consultantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method('aConsultantBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->consultantId)
                ->willReturn($this->consultant);
    }

}
