<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ParticipantNotification
};
use Tests\TestBase;

class ParticipantNotificationAddTest extends TestBase
{

    protected $service;
    protected $participantNotificationRepository;
    protected $firmId = 'firmId', $programId = 'programId';
    protected $programParticipationRepository, $programParticipation, $participantId = 'participantId';
    protected $id = 'newId', $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantNotificationRepository = $this->buildMockOfInterface(ParticipantNotificationRepository::class);
        $this->participantNotificationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->id);
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->programParticipationRepository->expects($this->any())
                ->method('aProgramParticipationOfProgram')
                ->with($this->firmId, $this->programId, $this->participantId)
                ->willReturn($this->programParticipation);

        $this->service = new ParticipantNotificationAdd(
                $this->participantNotificationRepository, $this->programParticipationRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->participantId, $this->message);
    }
    public function test_execute_addParticipantNotificationToRepository()
    {
        $this->programParticipation->expects($this->once())
                ->method('createParticipantNotification')
                ->with($this->id, $this->message)
                ->willReturn($participantNotification = $this->buildMockOfClass(ParticipantNotification::class));
        $this->participantNotificationRepository->expects($this->once())
                ->method('add')
                ->with($participantNotification);
        $this->execute();
    }

}
