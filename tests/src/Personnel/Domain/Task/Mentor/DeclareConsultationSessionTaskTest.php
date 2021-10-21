<?php

namespace Personnel\Domain\Task\Mentor;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\src\Personnel\Domain\Task\Mentor\ConsultationSessionTaskTestBase;

class DeclareConsultationSessionTaskTest extends ConsultationSessionTaskTestBase
{

    protected $participantRepository;
    protected $participant;
    protected $participantId = 'participantId';
    protected $consultationSetupRepository;
    protected $consultationSetup;
    protected $consultationSetupId = 'consultationSessionId';
    protected $startTime, $endTime, $media = 'new media', $address = 'new location';
    protected $payload;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository->expects($this->once())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);

        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository->expects($this->once())
                ->method('ofId')
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->endTime = new DateTimeImmutable('+25 hours');

        $this->payload = new DeclareConsultationSessionPayload(
                $this->consultationSetupId, $this->participantId, $this->startTime, $this->endTime, $this->media,
                $this->address);

        $this->task = new DeclareConsultationSessionTask(
                $this->consultationSessionRepository, $this->consultationSetupRepository, $this->participantRepository,
                $this->payload);
    }

    protected function execute()
    {
        $this->task->execute($this->mentor);
    }

    public function test_execute_addConsultationSessionDeclaredByMentorToRepository()
    {
        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $consultationChannel = new ConsultationChannel($this->media, $this->address);

        $this->mentor->expects($this->once())
                ->method('declareConsultationSession')
                ->with($this->consultationSessionId, $this->participant, $this->consultationSetup, $startEndTime,
                        $consultationChannel)
                ->willReturn($this->consultationSession);

        $this->consultationSessionRepository->expects($this->once())
                ->method('add')
                ->with($this->consultationSession);

        $this->execute();
    }
    public function test_execute_setId()
    {
        $this->execute();
        $this->assertEquals($this->consultationSessionId, $this->task->id);
    }

}
