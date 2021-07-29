<?php

namespace Tests\src\Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\TeamParticipantRepository;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\src\Firm\Application\Service\Client\AsTeamMember\AsTeamMemberTestBase;

class AsProgramParticipantTestBase extends AsTeamMemberTestBase
{
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
    protected $participantId = 'participant-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->teamParticipant);
    }
}
