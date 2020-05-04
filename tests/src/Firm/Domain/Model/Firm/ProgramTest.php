<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Event\ProgramManageParticipantEvent,
    Model\Firm,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Registrant
};
use Query\Domain\Model\Client;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $firm;
    protected $id = 'program-id', $name = 'new name', $description = 'new description';
    protected $consultant;
    protected $coordinator;
    protected $participant;
    protected $registrant, $registrantId = 'registrantId';


    protected $personnel;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $programData = new ProgramData('name', 'description');
        $this->program = new TestableProgram($this->firm, 'id', $programData);
        
        $this->program->consultants = new ArrayCollection();
        $this->program->coordinators = new ArrayCollection();
        $this->program->registrants = new ArrayCollection();
        $this->program->participants = new ArrayCollection();

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->program->consultants->add($this->consultant);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->program->coordinators->add($this->coordinator);
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->program->registrants->add($this->registrant);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->program->participants->add($this->participant);
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->client = $this->buildMockOfClass(Client::class);
    }

    protected function getProgramData()
    {
        return new ProgramData($this->name, $this->description);
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

    protected function executeAcceptRegistrant()
    {
        $this->registrant->expects($this->any())
                ->method('getId')
                ->willReturn($this->registrantId);
        $this->registrant->expects($this->any())
                ->method('getClient')
                ->willReturn($this->client);
        $this->program->acceptRegistrant($this->registrantId);
    }
    public function test_acceptRegistrant_executeRegistrantsAcceptMethod()
    {
        $this->registrant->expects($this->once())
                ->method('accept');
        $this->executeAcceptRegistrant();
    }
    public function test_acceptRegistrant_registrantNotFound_throwEx()
    {
        $this->registrant->expects($this->once())
                ->method('getId')
                ->willReturn('nonMatchRegistrant');
        $operation = function (){
            $this->executeAcceptRegistrant();
        };
        $errorDetail = 'not found: registrant not found';
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_accepRegistrant_addParticipantToCollection()
    {
        $this->executeAcceptRegistrant();
        $this->assertEquals(2, $this->program->participants->count());
        $this->assertInstanceOf(Participant::class, $this->program->participants->last());
    }
    public function test_acceptRegistrant_alreadyHasParticipantOfSameClient_reactivateParticipant()
    {
        $this->participant->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);
        $this->participant->expects($this->once())
            ->method('reActivate');
        $this->executeAcceptRegistrant();
    }
    public function test_acceptRegistrant_alreadyHasParticipantOfSameClient_preventAddNewParticipantOfTheClient()
    {
        $this->participant->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);
        $this->executeAcceptRegistrant();
        $this->assertEquals(1, $this->program->participants->count());
    }
    public function test_acceptRegistrant_recordRegistrantAcceptedEvent()
    {
        $this->executeAcceptRegistrant();
        $this->assertInstanceOf(ProgramManageParticipantEvent::class, $this->program->getRecordedEvents()[0]);
    }
    
}

class TestableProgram extends Program
{

    public $firm, $id, $name, $description, $published, $removed;
    public $consultants, $coordinators;
    public $participants, $registrants;

}
