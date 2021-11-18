<?php

namespace Tests\src\Firm\Domain\Task\InFirm;

use PHPUnit\Framework\MockObject\MockObject;

class TeamRelatedTaskTestBase extends FirmTaskTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $teamRepository;

    /**
     * 
     * @var MockObject
     */
    protected $team;
    protected $teamId = 'teamId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(\Firm\Domain\Model\Firm\Team::class);
        $this->teamRepository = $this->buildMockOfInterface(\Firm\Domain\Task\Dependency\Firm\TeamRepository::class);
        $this->teamRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamId)
                ->willReturn($this->team);
    }

}
