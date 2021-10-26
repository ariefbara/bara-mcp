<?php

namespace Participant\Domain\Task\Participant;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;
use Participant\Domain\Task\Dependency\Firm\Program\MentorRepository;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use Tests\src\Participant\Domain\Task\Participant\ConsultationSessionTaskTestBase;

class DeclareConsultationSessionTaskTest extends ConsultationSessionTaskTestBase
{

    protected $consultationSetupRepository, $consultationSetup, $consultationSetupId = 'consultationSetupId';
    protected $mentorRepository, $mentor, $mentorId = 'mentorId';
    protected $payload, $startEndTime, $channel;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfClass(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('ofId')
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->mentor = $this->buildMockOfClass(Consultant::class);
        $this->mentorRepository = $this->buildMockOfClass(MentorRepository::class);
        $this->mentorRepository->expects($this->any())
                ->method('ofId')
                ->with($this->mentorId)
                ->willReturn($this->mentor);
        
        $this->payload = $this->buildMockOfClass(DeclareConsultationSessionPayload::class);
        $this->payload->expects($this->any())->method('getConsultationSetupId')->willReturn($this->consultationSetupId);
        $this->payload->expects($this->any())->method('getMentorId')->willReturn($this->mentorId);
        
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->payload->expects($this->any())->method('getStartEndTime')->willReturn($this->startEndTime);
        
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);
        $this->payload->expects($this->any())->method('getConsultationChannel')->willReturn($this->channel);

        $this->task = new DeclareConsultationSessionTask(
                $this->consultationSessionRepository, $this->consultationSetupRepository, $this->mentorRepository,
                $this->payload);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant);
    }
    public function test_execute_addConsultationSessionDeclaredByParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('declareConsultationSession')
                ->with($this->consultationSessionId, $this->consultationSetup, $this->mentor, $this->startEndTime, $this->channel)
                ->willReturn($this->consultationSession);
        
        $this->consultationSessionRepository->expects($this->once())
                ->method('add')
                ->with($this->consultationSession);
        $this->execute();
    }
    public function test_execute_setNextIdentityAsDeclaredSessionId()
    {
        $this->execute();
        $this->assertSame($this->consultationSessionId, $this->task->declaredSessionId);
    }

}
