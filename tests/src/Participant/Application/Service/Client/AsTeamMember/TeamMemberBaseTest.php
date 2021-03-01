<?php

namespace Tests\src\Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Participant\Application\Service\Client\AsTeamMember\TeamParticipantRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\TeamProgramParticipation;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TeamMemberBaseTest extends TestBase
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
    /**
     * 
     * @var MockObject
     */
    protected $teamParticipantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $teamParticipant;
    protected $teamParticipantId = 'participantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMemberRepository->expects($this->any())
                ->method('aTeamMembershipCorrespondWithTeam')
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
    }
}
