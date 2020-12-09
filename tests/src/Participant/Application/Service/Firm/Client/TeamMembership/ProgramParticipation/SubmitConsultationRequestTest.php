<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository;
use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Application\Service\Firm\Program\ConsultantRepository;
use Participant\Application\Service\Firm\Program\ConsultationSetupRepository;
use Participant\Application\Service\Participant\ConsultationRequestRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\TeamProgramParticipation;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class SubmitConsultationRequestTest extends TestBase
{

    protected $consultationRequestRepository, $nextId = 'nextId';
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $consultationSetupRepository, $consultationSetup;
    protected $consultantRepository, $consultant;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $programParticipationId = "programParticipationId", $consultationSetupId = "consultationSetupid",
            $consultantId = "consultantId";
    protected $consultationRequestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamProgramParticipationRepository = $this->buildMockOfInterface(TeamProgramParticipationRepository::class);
        $this->teamProgramParticipationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->programParticipationId)
                ->willReturn($this->teamProgramParticipation);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultantId)
                ->willReturn($this->consultant);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultationSetupRepository = $this->buildMockOfInterface(ConsultationSetupRepository::class);
        $this->consultationSetupRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationSetupId)
                ->willReturn($this->consultationSetup);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new SubmitConsultationRequest(
                $this->consultationRequestRepository, $this->teamMembershipRepository,
                $this->teamProgramParticipationRepository, $this->consultationSetupRepository,
                $this->consultantRepository, $this->dispatcher);

        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $this->programParticipationId,
                        $this->consultationSetupId, $this->consultantId, $this->consultationRequestData);
    }

    public function test_execute_addConsultationRequestToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitConsultationRequest")
                ->with(
                        $this->teamProgramParticipation, $this->nextId, $this->consultationSetup, $this->consultant,
                        $this->consultationRequestData);

        $this->consultationRequestRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheTeamMembership()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->teamMembership);
        $this->execute();
    }

}
