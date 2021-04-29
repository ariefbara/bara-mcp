<?php

namespace Tests\src\Firm\Application\Service\Client\AsTeamMember;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Domain\Model\Firm\Team\Member;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class AsTeamMemberTestBase extends TestBase
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
                ->method('aTeamMemberCorrespondWithTeam')
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
    }
}
