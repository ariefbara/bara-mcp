<?php

namespace Tests\src\Participant\Application\Service\User;

use Participant\Application\Service\User\UserParticipantRepository;
use Participant\Domain\Model\UserParticipant;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class UserTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $userParticipantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $userParticipant;
    protected $userId = 'userId', $participantId = 'participantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('aUserParticipant')
                ->with($this->userId, $this->participantId)
                ->willReturn($this->userParticipant);
    }
}
