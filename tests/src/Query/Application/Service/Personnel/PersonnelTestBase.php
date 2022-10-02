<?php

namespace Tests\src\Query\Application\Service\Personnel;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Personnel\PersonnelRepository;
use Query\Domain\Model\Firm\Personnel;
use Tests\TestBase;

class PersonnelTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $personnelRepository;
    /**
     * 
     * @var MockObject
     */
    protected $personnel;
    protected $personnelId = 'personnelId', $firmId = 'firmId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->personnelRepository = $this->buildMockOfInterface(PersonnelRepository::class);
        $this->personnelRepository->expects($this->any())
                ->method('aPersonnelInFirm')
                ->with($this->firmId, $this->personnelId)
                ->willReturn($this->personnel);
    }
}
