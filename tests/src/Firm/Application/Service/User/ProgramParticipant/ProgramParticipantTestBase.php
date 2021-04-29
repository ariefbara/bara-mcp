<?php

namespace Tests\src\Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\User\ProgramParticipant\UserParticipantRepository;
use Firm\Domain\Model\Firm\Program\UserParticipant;
use Tests\TestBase;


class ProgramParticipantTestBase extends TestBase
{
    protected $userParticipantRepository;
    protected $userParticipant;
    protected $userId = 'user-id', $programId = 'program-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('aUserParticipantCorrespondWithProgram')
                ->with($this->userId, $this->programId)
                ->willReturn($this->userParticipant);
    }
}
