<?php

namespace Firm\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\Metric,
    Model\Firm\Program\MetricData,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Registrant,
    Service\ActivityTypeDataProvider
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program, $participantTypes;
    protected $firm;
    protected $id = 'program-id', $name = 'new name', $description = 'new description', $types = ['user', 'client'];
    
    protected $consultant;
    protected $coordinator;
    
    protected $registrant, $registrantId = "registrantId";
    protected $participant;

    protected $personnel;
    
    protected $metricId = "metricId", $metricData;
    
    protected $activityTypeId = "activityTypeId", $activityTypeDataProvider;

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
        
        $this->metricData = $this->buildMockOfClass(MetricData::class);
        $this->metricData->expects($this->any())->method("getName")->willReturn("metric name");
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        $this->activityTypeDataProvider->expects($this->any())->method("getName")->willReturn("name");
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
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->program->belongsToFirm($this->program->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->program->belongsToFirm($firm));
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
    function test_assignPersonnelAsConsultant_aConsultantReferToSamePersonnelExistInCollection_enableExistingConsultant()
    {
        $this->consultant->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        
        $this->consultant->expects($this->once())
                ->method("enable");
        $this->executeAssignPersonnelAsConsultant();
    }
    public function test_assignePersonnelAsConsultant_returnConsultantId()
    {
        $this->consultant->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
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
    public function test_assignPersonnelAsCoordinator_personnelAlreadyAssignAsCoordinator_enableCorrespondCoordinator()
    {
        $this->coordinator->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
        $this->coordinator->expects($this->once())
            ->method('enable');
        $this->executeAssignPersonnelAsCoordinator();
    }
    public function test_assignPersonnelAsCoordiantor_returnCoordinatorId()
    {
        $this->coordinator->expects($this->once())
            ->method('getPersonnel')
            ->willReturn($this->personnel);
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
        
        $this->program->acceptRegistrant($this->registrantId);
    }
    public function test_acceptRegistrant_acceptRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('accept');
        $this->executeAcceptRegistrant();
    }
    public function test_acceptRegistrant_noMatchingRegistrantToId_notFoundError()
    {
        $this->registrant->expects($this->once())
                ->method('getId')
                ->willReturn('noMatch');
        $operation = function (){
            $this->executeAcceptRegistrant();
        };
        $errorDetail = "not found: registrant not found";
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }
    public function test_acceptRegistrant_addParticipantToRepository()
    {
        $this->registrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->anything());
        $this->executeAcceptRegistrant();
        $this->assertEquals(2, $this->program->participants->count());
    }
    public function test_acceptRegistrant_alreadyHasParticipantCorrespondWithRegistrant_reenroolParticipant()
    {
        $this->participant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->registrant)
                ->willReturn(true);
        $this->participant->expects($this->once())
                ->method('reenroll');
        $this->executeAcceptRegistrant();
    }
    public function test_acceptRegistrant_reenrollParticipant_preventAddNewParticipant()
    {
        $this->participant->expects($this->once())
                ->method('correspondWithRegistrant')
                ->with($this->registrant)
                ->willReturn(true);
        $this->executeAcceptRegistrant();
        $this->assertEquals(1, $this->program->participants->count());
    }
    public function test_acceptRegistrant_recordRegistrantAcceptedEvent()
    {
        $this->program->recordedEvents = [];
        $this->executeAcceptRegistrant();
        $this->assertInstanceOf(CommonEvent::class, $this->program->recordedEvents[0]);
    }
    
    public function test_addMetric_returnMetric()
    {
        $metric = new Metric($this->program, $this->metricId, $this->metricData);
        $this->assertEquals($metric, $this->program->addMetric($this->metricId, $this->metricData));
    }
    
    public function test_createActivityType_returnActivityType()
    {
        $activityType = new Program\ActivityType($this->program, $this->activityTypeId, $this->activityTypeDataProvider);
        $this->assertEquals($activityType, $this->program->createActivityType($this->activityTypeId, $this->activityTypeDataProvider));
    }
    
}

class TestableProgram extends Program
{

    public $firm, $id, $name, $description, $participantTypes, $published, $removed;
    public $consultants, $coordinators;
    public $participants, $registrants;
    public $recordedEvents;
}
