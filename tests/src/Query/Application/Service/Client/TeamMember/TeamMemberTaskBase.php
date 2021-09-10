<?php

namespace Tests\src\Query\Application\Service\Client\TeamMember;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\Client\TeamMember\TeamMemberRepository;
use Query\Domain\Model\Firm\Team\Member;
use Tests\TestBase;

class TeamMemberTaskBase extends TestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var MockObject
     */
    protected $teamMember;
    protected $firmId = 'firmId', $clientId = 'clientId', $teamId = 'teamId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMember = $this->buildMockOfClass(Member::class);
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method('aTeamMemberOfClientCorrespondWithTeam')
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
    }

}
