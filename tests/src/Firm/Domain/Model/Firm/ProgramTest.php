<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Event\Firm\Program\ClientRegistrationAccepted,
    Event\Firm\Program\UserRegistrationAccepted,
    Model\Firm,
    Model\Firm\Program\ClientParticipant,
    Model\Firm\Program\ClientRegistrant,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\UserParticipant,
    Model\Firm\Program\UserRegistrant
};
use Query\Domain\Model\ {
    Firm\ParticipantTypes,
    User
};
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program, $participantTypes;
    protected $firm;
    protected $id = 'program-id', $name = 'new name', $description = 'new description', $types = ['user', 'client'];
    protected $consultant;
    protected $coordinator;
    protected $clientRegistrant;
    protected $clientParticipant;
    protected $userRegistrant;
    protected $userParticipant;

    protected $personnel;
    
    protected $client, $clientRegistrantId = 'clientRegistrantId';
    protected $user, $userId = 'userId', $userRegistrantId = 'userRegistrantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $programData = new ProgramData('name', 'description');
        $this->program = new TestableProgram($this->firm, 'id', $programData);
        $this->participantTypes = $this->buildMockOfClass(ParticipantTypes::class);
        $this->program->participantTypes = $this->participantTypes;
        
        $this->program->consultants = new ArrayCollection();
        $this->program->coordinators = new ArrayCollection();
        $this->program->clientRegistrants = new ArrayCollection();
        $this->program->clientParticipants = new ArrayCollection();
        $this->program->userRegistrants = new ArrayCollection();
        $this->program->userParticipants = new ArrayCollection();

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->program->consultants->add($this->consultant);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->program->coordinators->add($this->coordinator);
        
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->program->clientRegistrants->add($this->clientRegistrant);
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->program->clientParticipants->add($this->clientParticipant);
        
        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->program->userRegistrants->add($this->userRegistrant);
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->program->userParticipants->add($this->userParticipant);
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->user = $this->buildMockOfClass(User::class);
        $this->user->expects($this->any())->method('getId')->willReturn($this->userId);
    }

    protected function getProgramData()
    {
        $programData = new ProgramData($this->name, $this->description);
        foreach ($this->types as $type) {
            $programData->addParticipantType($type);
        }
        return $programData;
    }

    protected function executeConstruct()
    {
        return new TestableProgram($this->firm, $this->id, $this->getProgramData());
    }
    function test_construct_setProperties()
    {
        $program = $this->executeConstruct();
        $this->assertEquals($this->firm, $program->firm);
        $this->assertEquals($this->id, $program->id);
        $this->assertEquals($this->name, $program->name);
        $this->assertEquals($this->description, $program->description);
        $this->assertFalse($program->published);
        $this->assertFalse($program->removed);
        
        $participantTypes = new ParticipantTypes($this->types);
        $this->assertEquals($participantTypes, $program->participantTypes);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: program name is required';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

    protected function executeUpdate()
    {
        $this->program->update($this->getProgramData());
    }
    function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->program->name);
        $this->assertEquals($this->description, $this->program->description);
        
        $participantTypes = new ParticipantTypes($this->types);
        $this->assertEquals($participantTypes, $this->program->participantTypes);
    }

    public function test_publish_setPublishFlagTrue()
    {
        $this->program->publish();
        $this->assertTrue($this->program->published);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->program->remove();
        $this->assertTrue($this->program->removed);
    }

    protected function executeAssignPersonnelAsConsultant()
    {
        return $this->program->assignPersonnelAsConsultant($this->personnel);
    }
    public function test_assignPersonnelAsConsultant_addConsultantToCollection()
    {
        $this->executeAssignPersonnelAsConsultant();
        $this->assertEquals(2, $this->program->consultants->count());
    }
    function test_assignPersonnelAsConsultant_aConsultantReferToSamePersonnelExistInCollection_throwEx()
    {
        $this->consultant->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $operation = function () {
            $this->executeAssignPersonnelAsConsultant();
        };
        $errorDetails = 'forbidden: personnel already assigned as consultant';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetails);
    }
    public function test_assignPersonnelAsConsultant_consultantReferedToSamePersonnelAlreadyRemove_reassignConsultant()
    {
        $this->consultant->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $this->consultant->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->consultant->expects($this->once())
            ->method('reassign');
        $this->executeAssignPersonnelAsConsultant();
    }
    public function test_assignePersonnelAsConsultant_returnConsultantId()
    {
        $this->consultant->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $this->consultant->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->consultant->expects($this->once())
            ->method('getId')
            ->willReturn($id = 'id');
        $this->assertEquals($id, $this->executeAssignPersonnelAsConsultant());
    }
    
    protected function executeAssignPersonnelAsCoordinator()
    {
        return $this->program->assignPersonnelAsCoordinator($this->personnel);
    }
    public function test_assignPersonnelAsCoordinator_addCoordinatorToCollection()
    {
        $this->executeAssignPersonnelAsCoordinator();
        $this->assertEquals(2, $this->program->coordinators->count());
    }
    public function test_assignePersonnelAsCoordinator_personnelAlreadyAssignAsCoordinator_throwEx()
    {
        $this->coordinator->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $operation = function () {
            $this->executeAssignPersonnelAsCoordinator();
        };
        $errorDetail = "forbidden: personnel already assigned as coordinator";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_assignPersonnelAsCoordinator_coordinatorReferToSamePersonnelAlreadyRemoved_reassignCoordinator()
    {
        $this->coordinator->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $this->coordinator->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->coordinator->expects($this->once())
            ->method('reassign');
        $this->executeAssignPersonnelAsCoordinator();
    }
    public function test_assignPersonnelAsCoordiantor_returnCoordinatorId()
    {
        $this->coordinator->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $this->coordinator->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->coordinator->expects($this->once())
            ->method('getId')
            ->willReturn($id = 'id');
        $this->assertEquals($id, $this->executeAssignPersonnelAsCoordinator());
    }
    
    protected function executeAcceptClientRegistration()
    {
        $this->clientRegistrant->expects($this->any())
                ->method('getId')
                ->willReturn($this->clientRegistrantId);
        $this->program->acceptClientRegistration($this->clientRegistrantId);
    }
    public function test_acceptClientRegistration_acceptClientRegistrant()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('accept');
        $this->executeAcceptClientRegistration();
    }
    public function test_acceptClientRegistration_noExistringRegistrantCorrespondToId_notFoundError()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('getId')
                ->willReturn('unmatchId');
        $operation = function (){
            $this->executeAcceptClientRegistration();
        };
        $errorDetail = 'not found: client registrant not found';
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_acceptClientRegistrant_addClientParticipantToCollection()
    {
        $clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientRegistrant->expects($this->once())
                ->method('createParticipant')
                ->willReturn($clientParticipant);
        
        $this->executeAcceptClientRegistration();
        $this->assertEquals($clientParticipant, $this->program->clientParticipants->last());
    }
    public function test_acceptClientRegistration_clientIsFormerParticipant_reenrollExistingParticipant()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->clientRegistrant)
                ->willReturn(true);
        $this->clientParticipant->expects($this->once())
                ->method('reenroll');
        $this->executeAcceptClientRegistration();
    }
    public function test_acceptClientRegistration_clientIsFormerParticipant_preventAddNewParticipantInCollection()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->clientRegistrant)
                ->willReturn(true);
        $this->executeAcceptClientRegistration();
        $this->assertEquals(1, $this->program->clientParticipants->count());
    }
    public function test_acceptClientRegistration_recordClientRegistrationAcceptedEvent()
    {
        $this->firm->expects($this->once())->method('getId')->willReturn($firmId = 'firmId');
        $this->clientRegistrant->expects($this->once())->method('getClientId')->willReturn($clientId = 'clientId');
        
        $this->program->clearRecordedEvents();
        $this->executeAcceptClientRegistration();
        
        $event = new ClientRegistrationAccepted($firmId, $this->program->id, $clientId);
        $this->assertEquals($event, $this->program->getRecordedEvents()[0]);
    }
    
    protected function executeAcceptUserRegistration()
    {
        $this->userRegistrant->expects($this->any())
                ->method('getId')
                ->willReturn($this->userRegistrantId);
        $this->program->acceptUserRegistration($this->userRegistrantId);
    }
    public function test_acceptUserRegistration_acceptUserRegistrant()
    {
        $this->userRegistrant->expects($this->once())
                ->method('accept');
        $this->executeAcceptUserRegistration();
    }
    public function test_acceptUserRegistration_noRegistrantCorrespontToId_notFoundError()
    {
        $this->userRegistrant->expects($this->once())
                ->method('getId')
                ->willReturn('unmatchId');
        $operation = function (){
            $this->executeAcceptUserRegistration();
        };
        $errorDetail = 'not found: user registrant not found';
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_acceptUserRegistration_addNewUserParticipantToCollection()
    {
        $userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userRegistrant->expects($this->once())
                ->method('createParticipant')
                ->willReturn($userParticipant);
        $this->executeAcceptUserRegistration();
        $this->assertEquals($userParticipant, $this->program->userParticipants->last());
    }
    public function test_acceptUserRegistration_userIsFormerParticipant_reenrollPastParticipation()
    {
        $this->userParticipant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->userRegistrant)
                ->willReturn(true);
        $this->userParticipant->expects($this->once())
                ->method('reenroll');
        $this->executeAcceptUserRegistration();
    }
    public function test_acceptUserRegistration_userIsFormerParticipant_preventAddAsNewParticipant()
    {
        $this->userParticipant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->userRegistrant)
                ->willReturn(true);
        $this->executeAcceptUserRegistration();
        $this->assertEquals(1, $this->program->userParticipants->count());
    }
    public function test_acceptUserRegistration_recordUserRegistrationAcceptedEvent()
    {
        $this->firm->expects($this->once())->method('getId')->willReturn($firmId = 'firmId');
        $this->userRegistrant->expects($this->once())->method('getUserId')->willReturn($userId = 'userId');
        
        $this->program->clearRecordedEvents();
        $this->executeAcceptUserRegistration();
        
        $event = new UserRegistrationAccepted($firmId, $this->program->id, $userId);
        $this->assertEquals($event, $this->program->getRecordedEvents()[0]);
    }
    
}

class TestableProgram extends Program
{

    public $firm, $id, $name, $description, $participantTypes, $published, $removed;
    public $consultants, $coordinators;
    public $clientParticipants, $clientRegistrants;
    public $userParticipants, $userRegistrants;
}
