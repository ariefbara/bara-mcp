<?php

namespace ActivityCreator\Domain\Service;

use ActivityCreator\Domain\DependencyModel\Firm\ {
    Manager,
    Personnel\Consultant,
    Personnel\Coordinator,
    Program\Participant
};
use DateTimeImmutable;
use SplObjectStorage;
use Tests\TestBase;

class ActivityDataProviderTest extends TestBase
{

    protected $managerRepository, $manager;
    protected $coordinatorRepository, $coordinator;
    protected $consultantRepository, $consultant;
    protected $participantRepository, $participant;
    protected $invitedManagerList;
    protected $invitedCoordinatorList;
    protected $invitedConsultantList;
    protected $invitedParticipantList;
    protected $activityDataProvider;
    protected $name = "new name";
    protected $description = "new description";
    protected $startTime;
    protected $endTime;
    protected $location = "new location";
    protected $note = "new note";
    protected $managerId = "managerId", $coordinatorId = "coordinatorId", $consultantId = "consultantId",
            $participantId = "participantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())->method("ofId")->with($this->managerId)->willReturn($this->manager);

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())->method("ofId")->with($this->coordinatorId)->willReturn($this->coordinator);

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->any())->method("ofId")->with($this->consultantId)->willReturn($this->consultant);

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())->method("ofId")->with($this->participantId)->willReturn($this->participant);

        $this->activityDataProvider = new TestableActivityDataProvider(
                $this->managerRepository, $this->coordinatorRepository, $this->consultantRepository,
                $this->participantRepository, null, null, null, null, null, null);
        
        $this->invitedManagerList = $this->buildMockOfClass(\SplObjectStorage::class);
        $this->activityDataProvider->invitedManagerList = $this->invitedManagerList;
        
        $this->invitedCoordinatorList = $this->buildMockOfClass(\SplObjectStorage::class);
        $this->activityDataProvider->invitedCoordinatorList = $this->invitedCoordinatorList;
        
        $this->invitedConsultantList = $this->buildMockOfClass(\SplObjectStorage::class);
        $this->activityDataProvider->invitedConsultantList = $this->invitedConsultantList;
        
        $this->invitedParticipantList = $this->buildMockOfClass(\SplObjectStorage::class);
        $this->activityDataProvider->invitedParticipantList = $this->invitedParticipantList;

        $this->startTime = new DateTimeImmutable();
        $this->endTime = new DateTimeImmutable("+2 hours");
    }

    public function test_construct_setProperties()
    {
        $activityDataProvider = new TestableActivityDataProvider(
                $this->managerRepository, $this->coordinatorRepository, $this->consultantRepository,
                $this->participantRepository, $this->name, $this->description, $this->startTime, $this->endTime,
                $this->location, $this->note);
        
        $this->assertEquals($this->managerRepository, $activityDataProvider->managerRepository);
        $this->assertEquals($this->coordinatorRepository, $activityDataProvider->coordinatorRepository);
        $this->assertEquals($this->consultantRepository, $activityDataProvider->consultantRepository);
        $this->assertEquals($this->participantRepository, $activityDataProvider->participantRepository);
        $this->assertEquals($this->name, $activityDataProvider->name);
        $this->assertEquals($this->description, $activityDataProvider->description);
        $this->assertEquals($this->startTime, $activityDataProvider->startTime);
        $this->assertEquals($this->endTime, $activityDataProvider->endTime);
        $this->assertEquals($this->location, $activityDataProvider->location);
        $this->assertEquals($this->note, $activityDataProvider->note);
        
        $this->assertInstanceOf(SplObjectStorage::class, $activityDataProvider->invitedManagerList);
        $this->assertInstanceOf(SplObjectStorage::class, $activityDataProvider->invitedCoordinatorList);
        $this->assertInstanceOf(SplObjectStorage::class, $activityDataProvider->invitedConsultantList);
        $this->assertInstanceOf(SplObjectStorage::class, $activityDataProvider->invitedParticipantList);
    }
    
    public function test_addManagerInvitation_addManagerToInvitedList()
    {
        $this->invitedManagerList->expects($this->once())
                ->method("attach")
                ->with($this->manager);
        $this->activityDataProvider->addManagerInvitation($this->managerId);
    }
    
    public function test_addCoordinatorInvitation_addCoordinatorToInvitedList()
    {
        $this->invitedCoordinatorList->expects($this->once())
                ->method("attach")
                ->with($this->coordinator);
        $this->activityDataProvider->addCoordinatorInvitation($this->coordinatorId);
    }
    
    public function test_addConsultantInvitation_addConsultantToInvitedList()
    {
        $this->invitedConsultantList->expects($this->once())
                ->method("attach")
                ->with($this->consultant);
        $this->activityDataProvider->addConsultantInvitation($this->consultantId);
    }
    
    public function test_addParticipantInvitation_addParticipantToInvitedList()
    {
        $this->invitedParticipantList->expects($this->once())
                ->method("attach")
                ->with($this->participant);
        $this->activityDataProvider->addParticipantInvitation($this->participantId);
    }
    
    public function test_iterateInvitedManagerList_returnManagerList()
    {
        $this->invitedManagerList->expects($this->at(0))->method("valid")->willReturn(true);
        $this->invitedManagerList->expects($this->at(1))->method("valid")->willReturn(true);
        $this->invitedManagerList->expects($this->once())->method("current")->willReturn($this->manager);
        
        $this->assertEquals([$this->manager], $this->activityDataProvider->iterateInvitedManagerList());
    }
    
    public function test_iterateInvitedCoordinatorList_returnCoordinatorList()
    {
        $this->invitedCoordinatorList->expects($this->at(0))->method("valid")->willReturn(true);
        $this->invitedCoordinatorList->expects($this->at(1))->method("valid")->willReturn(true);
        $this->invitedCoordinatorList->expects($this->once())->method("current")->willReturn($this->coordinator);
        
        $this->assertEquals([$this->coordinator], $this->activityDataProvider->iterateInvitedCoordinatorList());
    }
    
    public function test_iterateInvitedConsultantList_returnConsultantList()
    {
        $this->invitedConsultantList->expects($this->at(0))->method("valid")->willReturn(true);
        $this->invitedConsultantList->expects($this->at(1))->method("valid")->willReturn(true);
        $this->invitedConsultantList->expects($this->once())->method("current")->willReturn($this->consultant);
        
        $this->assertEquals([$this->consultant], $this->activityDataProvider->iterateInvitedConsultantList());
    }
    
    public function test_iterateInvitedParticipantList_returnParticipantList()
    {
        $this->invitedParticipantList->expects($this->at(0))->method("valid")->willReturn(true);
        $this->invitedParticipantList->expects($this->at(1))->method("valid")->willReturn(true);
        $this->invitedParticipantList->expects($this->once())->method("current")->willReturn($this->participant);
        
        $this->assertEquals([$this->participant], $this->activityDataProvider->iterateInvitedParticipantList());
    }
    
    public function test_containManager_returnInvitedManagerListContainResult()
    {
        $this->invitedManagerList->expects($this->once())
                ->method("contains")
                ->with($this->manager)
                ->willReturn(true);
        $this->activityDataProvider->containManager($this->manager);
    }
    
    public function test_containCoordinator_returnInvitedCoordinatorListContainResult()
    {
        $this->invitedCoordinatorList->expects($this->once())
                ->method("contains")
                ->with($this->coordinator)
                ->willReturn(true);
        $this->activityDataProvider->containCoordinator($this->coordinator);
    }
    
    public function test_containConsultant_returnInvitedConsultantListContainResult()
    {
        $this->invitedConsultantList->expects($this->once())
                ->method("contains")
                ->with($this->consultant)
                ->willReturn(true);
        $this->activityDataProvider->containConsultant($this->consultant);
    }
    
    public function test_containParticipant_returnInvitedParticipantListContainResult()
    {
        $this->invitedParticipantList->expects($this->once())
                ->method("contains")
                ->with($this->participant)
                ->willReturn(true);
        $this->activityDataProvider->containParticipant($this->participant);
    }

}

class TestableActivityDataProvider extends ActivityDataProvider
{

    public $managerRepository;
    public $coordinatorRepository;
    public $consultantRepository;
    public $participantRepository;
    public $name;
    public $description;
    public $startTime;
    public $endTime;
    public $location;
    public $note;
    public $invitedManagerList;
    public $invitedCoordinatorList;
    public $invitedConsultantList;
    public $invitedParticipantList;

}
