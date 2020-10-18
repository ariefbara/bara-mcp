<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\Participant\ConsultationRequestRepository,
    Application\Service\UserParticipantRepository,
    Domain\DependencyModel\Firm\Program\Consultant,
    Domain\DependencyModel\Firm\Program\ConsultationSetup,
    Domain\Model\Participant\ConsultationRequest,
    Domain\Model\UserParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UserParticipantSubmitConsultationRequestTest extends TestBase
{
    protected $service;
    protected $consultationRequestRepository, $nextId = 'nextId';
    protected $userParticipantRepository, $userParticipant;
    protected $consultationSetupRepository, $consultationSetup;
    protected $consultantRepository, $consultant;
    protected $dispatcher;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId',
            $consultationSetupId = 'consultationSetupId', $consultantId = 'consultantId', $startTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationRequestRepository = $this->buildMockOfClass(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method('aConsultationSetupInProgramWhereUserParticipate')
                ->with($this->userId, $this->userParticipantId, $this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method("aConsultantInProgramWhereUserParticipate")
                ->with($this->userId, $this->userParticipantId, $this->consultantId)
                ->willReturn($this->consultant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UserParticipantSubmitConsultationRequest(
                $this->consultationRequestRepository, $this->userParticipantRepository,
                $this->consultationSetupRepository, $this->consultantRepository, $this->dispatcher);

        $this->startTime = new DateTimeImmutable();
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->userId, $this->userParticipantId, $this->consultationSetupId,
                        $this->consultantId, $this->startTime);
    }
    public function test_execute_addConsultationRequestFromUserParticipantProposeConsultation()
    {
        $this->userParticipant->expects($this->once())
                ->method('proposeConsultation')
                ->with($this->nextId, $this->consultationSetup, $this->consultant, $this->startTime);
        
        $this->consultationRequestRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatchUserParticipantToDispatcher()
    {
        $this->userParticipant->expects($this->once())
                ->method('proposeConsultation')
                ->willReturn($consultationRequest = $this->buildMockOfClass(ConsultationRequest::class));
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($consultationRequest);
        $this->execute();
    }
}
