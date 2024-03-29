<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\Metric;
use Firm\Domain\Model\Firm\Program\MetricData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Program\RegistrationPhase;
use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Model\Firm\Program\SponsorData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\ValueObject\ItemInfo;
use SharedContext\Domain\ValueObject\ProgramType;
use Tests\TestBase;
use TypeError;

class ProgramTest extends TestBase
{
    protected $program, $participantTypes;
    protected $firm;
    protected $id = 'program-id', $name = 'new name', $description = 'new description', $strictMissionOrder = true, 
            $types = ['user', 'client'], $price = 350000, $autoAccept = true;

    protected $consultant;
    protected $coordinator;
    
    protected $registrant, $registrantId = "registrantId";
    protected $participant;
    protected $assignedProfileForm;

    protected $personnel;
    protected $metricId = "metricId", $metricData;
    protected $activityTypeId = "activityTypeId", $activityTypeDataProvider;
    protected $evaluationPlanId = "evaluationPlanId", $evaluationPlanData, $feedbackForm, $mission;
    
    protected $profileForm;
    
    protected $meetingId = 'meeting-id', $activityType, $meetingData;
    protected $meeting, $activityParticipant;
    
    protected $sponsorId = 'sponsor-id', $sponsorData;
    protected $firmFileInfo;
    protected $type = 'client';
    protected $programType = ProgramType::COURSE;
    //
    protected $task, $payload = 'string represent task payload';
    //
    protected $applicant;
    protected $registrationPhase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $programData = new ProgramData('name', 'description', false, null, ProgramType::INCUBATION, null, false);
        $this->program = new TestableProgram($this->firm, 'id', $programData);
        $this->participantTypes = $this->buildMockOfClass(ParticipantTypes::class);
        $this->program->participantTypes = $this->participantTypes;
        
        $this->program->consultants = new ArrayCollection();
        $this->program->coordinators = new ArrayCollection();
        $this->program->registrants = new ArrayCollection();
        $this->program->participants = new ArrayCollection();
        $this->program->assignedProfileForms = new ArrayCollection();
        $this->program->published = true;

        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->program->consultants->add($this->consultant);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->program->coordinators->add($this->coordinator);
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->program->registrants->add($this->registrant);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->program->participants->add($this->participant);
        
        $this->assignedProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->program->assignedProfileForms->add($this->assignedProfileForm);
        
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        
        $this->metricData = $this->buildMockOfClass(MetricData::class);
        $this->metricData->expects($this->any())->method("getName")->willReturn("metric name");
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        $this->activityTypeDataProvider->expects($this->any())->method("getName")->willReturn("name");
        
        $this->evaluationPlanData = $this->buildMockOfClass(EvaluationPlanData::class);
        $this->evaluationPlanData->expects($this->any())->method("getInterval")->willReturn(90);
        $this->evaluationPlanData->expects($this->any())->method("getName")->willReturn("name");
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
        
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = new MeetingData('name', 'description', new DateTimeImmutable('+24 hours'), new DateTimeImmutable('+25 hours'), 'location', 'note');
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->activityParticipant = $this->buildMockOfClass(ActivityParticipant::class);
        
        $this->sponsorData = new SponsorData('sponsor name', null, 'sponsor.web.id');
        $this->firmFileInfo = $this->buildMockOfClass(FirmFileInfo::class);
        //
        $this->task = $this->buildMockOfInterface(IProgramTask::class);
        //
        $this->applicant = $this->buildMockOfInterface(IProgramApplicant::class);
        //
        $this->registrationPhase = $this->buildMockOfClass(RegistrationPhase::class);
        $this->program->registrationPhases = new ArrayCollection();
        $this->program->registrationPhases->add($this->registrationPhase);
    }

    protected function getProgramData()
    {
        $programData = new ProgramData(
                $this->name, $this->description, $this->strictMissionOrder, $this->firmFileInfo, $this->programType, 
                $this->price, $this->autoAccept);
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
        $this->autoAccept = 0;
        $program = $this->executeConstruct();
        $this->assertEquals($this->firm, $program->firm);
        $this->assertEquals($this->id, $program->id);
        $this->assertEquals($this->name, $program->name);
        $this->assertEquals($this->description, $program->description);
        $this->assertEquals($this->firmFileInfo, $program->illustration);
        $this->assertEquals($this->strictMissionOrder, $program->strictMissionOrder);
        $this->assertFalse($program->published);
        $this->assertFalse($program->removed);
        $this->assertEquals($program->price, $this->price);
        $this->assertEquals($program->autoAccept, $this->autoAccept);
        
        $programType = new ProgramType($this->programType);
        $this->assertEquals($programType, $program->programType);
        
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
    public function test_construct_assertIllustrationUsableInFirm()
    {
        $this->firmFileInfo->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeConstruct();
    }
    public function test_construct_nonIntegerPrice_typeError()
    {
        $this->price = 'non integer';
        $this->expectException(TypeError::class);
        $this->executeConstruct();
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
        $this->assertEquals($this->firmFileInfo, $this->program->illustration);
        $this->assertEquals($this->strictMissionOrder, $this->program->strictMissionOrder);
        $this->assertEquals($this->price, $this->program->price);
        $this->assertEquals($this->autoAccept, $this->program->autoAccept);
        
        $programType = new ProgramType($this->programType);
        $this->assertEquals($programType, $this->program->programType);
        $participantTypes = new ParticipantTypes($this->types);
        $this->assertEquals($participantTypes, $this->program->participantTypes);
    }
    public function test_update_assertIllustrationUsable()
    {
        $this->firmFileInfo->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeUpdate();
    }

    public function test_publish_setPublishFlagTrue()
    {
        $this->program->publish();
        $this->assertTrue($this->program->published);
    }

    protected function executeRemove()
    {
        $this->program->remove();
    }
    public function test_remove_setRemovedFlagTrue()
    {
        $this->program->published = false;
        $this->program->remove();
        $this->assertTrue($this->program->removed);
    }
    public function test_remove_publishedProgram_forbidden()
    {
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = "forbidden: can only remove unpublished program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeAssignPersonnelAsConsultant()
    {
        $this->personnel->expects($this->any())->method("isActive")->willReturn(true);
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
        $this->personnel->expects($this->any())->method("isActive")->willReturn(true);
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
        $activityType = new ActivityType($this->program, $this->activityTypeId, $this->activityTypeDataProvider);
        $this->assertEquals($activityType, $this->program->createActivityType($this->activityTypeId, $this->activityTypeDataProvider));
    }
    
    protected function executeCreateEvaluationPlan()
    {
        return $this->program->createEvaluationPlan(
                $this->evaluationPlanId, $this->evaluationPlanData, $this->feedbackForm, $this->mission);
    }
    public function test_createEvaluationPlan_returnNewEvaluationPlan()
    {
        $evaluationPlan = new EvaluationPlan(
                $this->program, $this->evaluationPlanId, $this->evaluationPlanData, $this->feedbackForm, $this->mission);
        $this->assertEquals($evaluationPlan, $this->executeCreateEvaluationPlan());
    }
    
    protected function executeAssignProfileForm()
    {
        return $this->program->assignProfileForm($this->profileForm);
    }
    public function test_assignProfileForm_addProgramsProfileFormToCollection()
    {
        $this->executeAssignProfileForm();
        $this->assertEquals(2, $this->program->assignedProfileForms->count());
        $this->assertInstanceOf(ProgramsProfileForm::class, $this->program->assignedProfileForms->last());
    }
    public function test_assignProfileForm_profileFormAlreadyAssigned_enableAssignedProfileForm()
    {
        $this->assignedProfileForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->with($this->profileForm)
                ->willReturn(true);
        $this->assignedProfileForm->expects($this->once())
                ->method("enable");
        $this->executeAssignProfileForm();
    }
    public function test_assignProfileForm_profileFormAlreadyAssigned_preventAddNewAssignment()
    {
        $this->assignedProfileForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->willReturn(true);
        $this->executeAssignProfileForm();
        $this->assertEquals(1, $this->program->assignedProfileForms->count());
    }
    public function test_assignProfileForm_returnAssignedProfileFormId()
    {
        $this->assignedProfileForm->expects($this->once())
                ->method("getId")
                ->willReturn($id = "assignedProfileFormId");
        $this->assignedProfileForm->expects($this->once())
                ->method("correspondWithProfileForm")
                ->willReturn(true);
        $this->assertEquals($id, $this->executeAssignProfileForm());
    }
    
    public function test_createRootMission_returnMission()
    {
        $worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $missionData = $this->buildMockOfClass(MissionData::class);
        $missionData->expects($this->any())->method('getName')->willReturn('mission name');
        $mission = new Mission($this->program, $missionId = 'missionId', $worksheetForm, $missionData);
        
        $this->assertEquals($mission, $this->program->createRootMission($missionId, $worksheetForm, $missionData));
    }
    
    public function test_isManageableByFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->program->isManageableByFirm($this->firm));
    }
    public function test_isManageableByFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->program->isManageableByFirm($firm));
    }
    
    protected function executeInviteAllActiveParticipantsToMeeting()
    {
        $this->participant->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        $this->program->inviteAllActiveParticipantsToMeeting($this->meeting, $this->activityParticipant);
    }
    public function test_inviteAllActiveParticipantsToMeeting_inviteAllParticipantsToMeeting()
    {
        $this->participant->expects($this->once())
                ->method('inviteToMeeting')
                ->with($this->meeting);
        $this->executeInviteAllActiveParticipantsToMeeting();
    }
    public function test_inviteAllActiveParticipantsToMeeting_containInactiveParticipant_skipInvitingThisParticipant()
    {
        $this->participant->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $this->participant->expects($this->never())
                ->method('inviteToMeeting');
        $this->executeInviteAllActiveParticipantsToMeeting();
    }
    
    protected function createSponsor()
    {
        return $this->program->createSponsor($this->sponsorId, $this->sponsorData);
    }
    public function test_createSponsor_returnSponsor()
    {
        $this->assertInstanceOf(Sponsor::class, $this->createSponsor());
    }
    
    protected function assertFileUsable()
    {
        $this->program->assertFileUsable($this->firmFileInfo);
    }
    public function test_assertFileUsableByFirm_assertFileUsableInFirm()
    {
        $this->firmFileInfo->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->program->firm);
        $this->assertFileUsable();
    }
    
    protected function assertCanAcceptParticipantOfType()
    {
        $this->participantTypes->expects($this->any())
                ->method('hasType')
                ->with($this->type)
                ->willReturn(true);
        $this->program->assertCanAcceptParticipantOfType($this->type);
    }
    public function test_assertCanAcceptParticipantOfType_typeContainedInparticipantTypesList_void()
    {
        $this->assertCanAcceptParticipantOfType();
        $this->markAsSuccess();
    }
    public function test_assertCanAcceptParticipantOfType_typeNotContainedInparticipantTypesList_forbidden()
    {
        $this->participantTypes->expects($this->once())
                ->method('hasType')
                ->with($this->type)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertCanAcceptParticipantOfType();
        }, 'Forbidden', "forbidden: {$this->type} in not accomodate in this program");
    }
    
    protected function assertUsableInFirm()
    {
        $this->program->published = true;
        $this->program->assertUsableInFirm($this->firm);
    }
    public function test_assertUsableInFirm_activeProgramInSameFirm()
    {
        $this->assertUsableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertUsableInFirm_unpublishedProgram_forbidden()
    {
        $this->program->published = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->program->assertUsableInFirm($this->firm);
        }, 'Forbidden', 'forbidden: unable to use unpublished program');
    }
    public function test_assertUsableInFirm_removedProgram_forbidden()
    {
        $this->program->removed = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInFirm();
        }, 'Forbidden', 'forbidden: unable to use removed program');
    }
    public function test_assertUsableInFirm_differentFirm_forbidden()
    {
        $this->program->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInFirm();
        }, 'Forbidden', 'forbidden: can only owned program');
    }
    
    protected function assertAccessibleInFirm()
    {
        $this->program->assertAccessibleInFirm($this->firm);
    }
    public function test_assertAccessibleInFirm_sameFirm_void()
    {
        $this->assertAccessibleInFirm();
        $this->markAsSuccess();
    }
    public function test_assertAccessibleInFirm_differentFirm_forbidden()
    {
        $this->program->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function(){
            $this->assertAccessibleInFirm();
        }, 'Forbidden', 'forbidden: can only access entity belongs to firm');
    }
    
    protected function executeTask()
    {
        $this->program->executeTask($this->task, $this->payload);
    }
    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->program, $this->payload);
        $this->executeTask();
    }
    public function test_executeTask_inactiveProgram_forbidden()
    {
        $this->program->removed = true;
        $this->assertRegularExceptionThrowed(function(){
            $this->executeTask();
        }, 'Forbidden', 'unable to access removed program');
    }
    
    protected function receiveApplication()
    {
        $this->participantTypes->expects($this->any())
                ->method('hasType')
                ->willReturn(true);
        $this->registrationPhase->expects($this->any())
                ->method('isOpen')
                ->willReturn(true);
        $this->program->receiveApplication($this->applicant);
    }
    public function test_receiveApplication_unpublishProgram_forbidden()
    {
        $this->program->published = false;
        $this->assertRegularExceptionThrowed(function(){
            $this->receiveApplication();
        }, 'Forbidden', 'unpublished program unable to accept application');
    }
    public function test_receiveProgram_noOpenRegistrationPhase_forbidden()
    {
        $this->registrationPhase->expects($this->once())
                ->method('isOpen')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function(){
            $this->receiveApplication();
        }, 'Forbidden', 'no open registration phase');
    }
    public function test_receiveApplication_assertApplicantBelongsInSameFirm()
    {
        $this->applicant->expects($this->once())
                ->method('assertBelongsInFirm')
                ->with($this->program->firm);
        $this->receiveApplication();
    }
    public function test_receiveApplication_assertApplicantHasNotActiveParticipationOrOngoingRegistrationInProgram()
    {
        $this->applicant->expects($this->once())
                ->method('assertNoActiveParticipationOrOngoingRegistrationInProgram')
                ->with($this->program);
        $this->receiveApplication();
    }
    public function test_receiveApplication_assertApplicantIncludedInAllowedParticipantType()
    {
        $this->applicant->expects($this->once())
                ->method('assertTypeIncludedIn')
                ->with($this->program->participantTypes);
        $this->receiveApplication();
    }
    public function test_receiveApplication_freeProgramWithAutoAccept_addParticipant()
    {
        $this->program->price = null;
        $this->program->autoAccept = true;
        
        $this->applicant->expects($this->once())
                ->method('addProgramParticipation');
        $this->receiveApplication();
    }
    public function test_receiveApplication_freeProgramWithAutoAccept_addParticipantToAggregatedBranches()
    {
        $this->program->price = null;
        $this->program->autoAccept = true;
        
        $this->receiveApplication();
        $this->assertInstanceOf(Participant::class, $this->program->aggregatedEventsFromBranches[0]);
    }
    public function test_receiveApplication_nonFreeAutoAcceptProgram_addRegistrant()
    {
        $this->receiveApplication();
        $this->applicant->expects($this->once())
                ->method('addProgramRegistration');
        $this->receiveApplication();
    }
    public function test_receiveApplication_nonFreeAutoAcceptProgram_preventAddParticipant()
    {
        $this->applicant->expects($this->never())
                ->method('addProgramParticipation');
        $this->receiveApplication();
    }
    public function test_receiveApplication_freeProgramWithAutoAccept_addRegistrantToAggregatedBrancesEvent()
    {
        $this->receiveApplication();
        $this->assertInstanceOf(Registrant::class, $this->program->aggregatedEventsFromBranches[0]);
    }
    public function test_receiveApplication_freeProgramWithAutoAccept_dontAddRegistrant()
    {
        $this->program->price = null;
        $this->program->autoAccept = true;
        $this->receiveApplication();
        
        $this->applicant->expects($this->never())
                ->method('addProgramRegistration');
        
        $this->receiveApplication();
    }
    
    //
    protected function addApplicantAsParticipant()
    {
        $this->program->addApplicantAsParticipant($this->applicant);
    }
    public function test_addProgramParticipant_addParticipant()
    {
        $this->addApplicantAsParticipant();
        $this->assertEquals(2, $this->program->participants->count());
        $this->assertInstanceOf(Participant::class, $this->program->participants->last());
    }
    public function test_addProgramParticipant_addApplicantProgramParticipation()
    {
        $this->applicant->expects($this->once())
                ->method('addProgramParticipation');
        $this->addApplicantAsParticipant();
    }
    
}

class TestableProgram extends Program
{

    public $firm, $id, $name, $description, $participantTypes, $published, $removed;
    public $programType;
    public $illustration;
    public $strictMissionOrder;
    public $consultants, $coordinators;
    public $participants, $registrants;
    public $recordedEvents;
    public $aggregatedEventsFromBranches;
    public $assignedProfileForms;
    public $price;
    public $autoAccept;
    public $registrationPhases;
}
