<?php

namespace Tests\src\Query\Application\Service\User\AsProgramParticipant;

use PHPUnit\Framework\MockObject\MockObject;
use Query\Application\Service\User\AsProgramParticipant\UserParticipantRepository;
use Query\Domain\Model\User\UserParticipant;
use Tests\TestBase;

class UserParticipantTestBase extends TestBase
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
    protected $userId = 'userId', $participantId = 'particiantId';

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
