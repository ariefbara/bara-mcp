<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use Firm\Domain\Model\Shared\FormData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ManagerTest extends TestBase
{

    protected $firm;
    protected $id = 'new-id', $name = 'new manager name', $email = 'new_address@email.org', $password = 'password123',
            $phone = '08112313123';
    protected $manager;
    protected $program;
    protected $activityTypeId = "activityTypeId", $activityTypeDataProvider;
    protected $activityType;
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $coordinator;
    protected $consultant;
    protected $personnel;
    protected $feedbackForm;
    protected $evaluationPlan;
    protected $evaluationPlanId = "evaluationPlanId", $evaluationPlanData;
    protected $consultationSetup, $consultationSetupName = "new consultation setup name",
            $sessionDuration = 99, $consultantFeedbackForm;
    protected $profileFormId = "profileFormId", $formData, $profileForm;
    protected $programsProfileForm;
    protected $mission;
    protected $worksheetForm;
    protected $clientCVForm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);

        $managerData = new ManagerData("name", "manager@email.org", "password123", "0823123123123");
        $this->manager = new TestableManager($this->firm, "id", $managerData);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        $this->activityType = $this->buildMockOfClass(ActivityType::class);

        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->consultantFeedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlanData = $this->buildMockOfClass(EvaluationPlanData::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())->method("getName")->willReturn("name");
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->clientCVForm = $this->buildMockOfClass(ClientCVForm::class);
    }

    protected function setAssetBelongsToFirm(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
    }
    protected function setAssetDoesntBelongsToFirm(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
    }
    protected function assertUnmanageableAssetForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: unable to manage asset from other firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function getManagerData()
    {
        return new ManagerData($this->name, $this->email, $this->password, $this->phone);
    }

    private function executeConstruct()
    {
        return new TestableManager($this->firm, $this->id, $this->getManagerData());
    }
    public function test_construct_setProperties()
    {
        $manager = $this->executeConstruct();
        $this->assertEquals($this->firm, $manager->firm);
        $this->assertEquals($this->id, $manager->id);
        $this->assertEquals($this->name, $manager->name);
        $this->assertEquals($this->email, $manager->email);
        $this->assertTrue($manager->password->match($this->password));
        $this->assertEquals($this->phone, $manager->phone);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $manager->joinTime->format('Y-m-d H:i:s'));
        $this->assertFalse($manager->removed);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager name is required';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = 'invalid address';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidPhoneFormat_throwEx()
    {
        $this->phone = 'invalid phone format';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager phone must be in valid phone format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_emptyPhone_processNormally()
    {
        $this->phone = '';
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    protected function executeCreateActivityTypeInProgram()
    {
        $this->program->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        return $this->manager->createActivityTypeInProgram(
                        $this->program, $this->activityTypeId, $this->activityTypeDataProvider);
    }
    
    public function test_createActivityTypeInProgram_returnActivityTypeCreatedInProgram()
    {
        $this->program->expects($this->once())
                ->method("createActivityType")
                ->with($this->activityTypeId, $this->activityTypeDataProvider);
        $this->executeCreateActivityTypeInProgram();
    }
    public function test_createActivityTypeInProgram_programFromDifferentFirm_forbidden()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
        $operation = function () {
            $this->executeCreateActivityTypeInProgram();
        };
        $errorDetail = "forbidden: can only manage asset of same firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_createActivityTypeInProgram_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function () {
            $this->executeCreateActivityTypeInProgram();
        };
        $errorDetail = "forbidden: only active manager can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeUpdateActivityType()
    {
        $this->setAssetBelongsToFirm($this->activityType);
        $this->manager->updateActivityType($this->activityType, $this->activityTypeDataProvider);
    }
    public function test_updateActivityType_updateActivityType()
    {
        $this->activityType->expects($this->once())
                ->method("update")
                ->with($this->activityTypeDataProvider);
        $this->executeUpdateActivityType();
    }
    public function test_updateActivityType_unmanagedActivityType_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->activityType);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeUpdateActivityType();
        });
    }

    protected function executeDisableActivityType()
    {
        $this->setAssetBelongsToFirm($this->activityType);
        $this->manager->disableActivityType($this->activityType);
    }
    public function test_disableActivityType_disableActivityType()
    {
        $this->activityType->expects($this->once())
                ->method("disable");
        $this->executeDisableActivityType();
    }
    public function test_disableActivityType_unmanagedActivityType_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->activityType);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeDisableActivityType();
        });
    }

    protected function executeEnableActivityType()
    {
        $this->setAssetBelongsToFirm($this->activityType);
        $this->manager->enableActivityType($this->activityType);
    }
    public function test_enableActivityType_enableActivityType()
    {
        $this->activityType->expects($this->once())
                ->method("enable");
        $this->executeEnableActivityType();
    }
    public function test_enableActivityType_unmanagedActivityType_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->activityType);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeEnableActivityType();
        });
    }

    public function test_canInvolvedInProgram_returnProgramsBelongsToFirmResult()
    {
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->manager->canInvolvedInProgram($program);
    }
    public function test_canInvolvedInProgram_inactiveManager_returnFalse()
    {
        $this->manager->removed = true;
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        $this->assertFalse($this->manager->canInvolvedInProgram($program));
    }
    
    public function test_roleCorrespondWith_returnActivityParticipantTypeIsManagerResult()
    {
        $activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $activityParticipantType->expects($this->once())
                ->method("isManagerType");
        $this->manager->roleCorrespondWith($activityParticipantType);
    }

    public function test_registerAsAttendeeCandidate_setManagerAsAttendeeCandidate()
    {
        $attendee = $this->buildMockOfClass(Attendee::class);
        $attendee->expects($this->once())
                ->method("setManagerAsAttendeeCandidate")
                ->with($this->manager);
        $this->manager->registerAsAttendeeCandidate($attendee);
    }

    protected function executeInitiateMeeting()
    {
        $this->meetingType->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        return $this->manager->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingCreatedThroughMeetingType()
    {
        $this->meetingType->expects($this->once())
                ->method("createMeeting")
                ->with($this->meetingId, $this->meetingData, $this->manager);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function () {
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: only active manager can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateMeeting_meetingTypeBelongsToDifferentFirm_forbidden()
    {
        $this->meetingType->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
        $operation = function () {
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: unable to manage meeting type from other firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeDisableCoordinator()
    {
        $this->setAssetBelongsToFirm($this->coordinator);
        $this->manager->disableCoordinator($this->coordinator);
    }
    public function test_disableCoordinator_disableCoordinator()
    {
        $this->coordinator->expects($this->once())
                ->method("disable");
        $this->executeDisableCoordinator();
    }
    public function test_disableCoordinator_coordinatorBelongsToDifferentFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->coordinator);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeDisableCoordinator();
        });
    }

    protected function executeDisableConsultant()
    {
        $this->setAssetBelongsToFirm($this->consultant);
        $this->manager->disableConsultant($this->consultant);
    }
    public function test_disableConsultant_disableConsultant()
    {
        $this->consultant->expects($this->once())
                ->method("disable");
        $this->executeDisableConsultant();
    }
    public function test_disableConsultant_consultantFromOtherFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->consultant);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeDisableConsultant();
        });
    }

    protected function executeDisablePersonnel()
    {
        $this->setAssetBelongsToFirm($this->personnel);
        $this->manager->disablePersonnel($this->personnel);
    }
    public function test_disablePersonnel_disablePersonnel()
    {
        $this->personnel->expects($this->once())
                ->method("disable");
        $this->executeDisablePersonnel();
    }
    public function test_disablePersonnel_personnelFromOtherFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->personnel);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeDisablePersonnel();
        });
    }

    protected function executeEnablePersonnel()
    {
        $this->setAssetBelongsToFirm($this->personnel);
        $this->manager->enablePersonnel($this->personnel);
    }
    public function test_enablePersonnel_enablePersonnel()
    {
        $this->personnel->expects($this->once())
                ->method("enable");
        $this->executeEnablePersonnel();
    }
    public function test_enablePersonnel_personnelFromOtherFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->personnel);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeEnablePersonnel();
        });
    }

    protected function executeCreateEvaluationPlanInProgram()
    {
        $this->setAssetBelongsToFirm($this->program);
        $this->setAssetBelongsToFirm($this->feedbackForm);
        return $this->manager->createEvaluationPlanInProgram(
                        $this->program, $this->evaluationPlanId, $this->evaluationPlanData, $this->feedbackForm);
    }
    public function test_createEvaluationPlanInProgram_returnEvaluationPlanCreatedInProgram()
    {
        $this->program->expects($this->once())
                ->method("createEvaluationPlan")
                ->with($this->evaluationPlanId, $this->evaluationPlanData, $this->feedbackForm);
        $this->executeCreateEvaluationPlanInProgram();
    }
    public function test_createEvaluationPlanInProgram_programFromDifferentFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->program);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeCreateEvaluationPlanInProgram();
        });
    }
    public function test_createEvaluationPlanInProgram_reportFormFromDifferentFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->feedbackForm);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeCreateEvaluationPlanInProgram();
        });
    }

    protected function executeUpdateEvaluationPlan()
    {
        $this->setAssetBelongsToFirm($this->evaluationPlan);
        $this->setAssetBelongsToFirm($this->feedbackForm);
        $this->manager->updateEvaluationPlan($this->evaluationPlan, $this->evaluationPlanData, $this->feedbackForm);
    }
    public function test_updateEvaluationPlan_updateEvaluationPlan()
    {
        $this->evaluationPlan->expects($this->once())
                ->method("update")
                ->with($this->evaluationPlanData, $this->feedbackForm);
        $this->executeUpdateEvaluationPlan();
    }
    public function test_updateEvaluationPlan_unmanagerableEvaluationPlan_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->evaluationPlan);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeUpdateEvaluationPlan();
        });
    }
    public function test_updateEvaluationPlan_unmanagerableFeedbackForm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->feedbackForm);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeUpdateEvaluationPlan();
        });
    }

    protected function executeDisableEvaluationPlan()
    {
        $this->setAssetBelongsToFirm($this->evaluationPlan);
        $this->manager->disableEvaluationPlan($this->evaluationPlan, $this->evaluationPlanData);
    }
    public function test_disableEvaluationPlan_disableEvaluationPlan()
    {
        $this->evaluationPlan->expects($this->once())
                ->method("disable");
        $this->executeDisableEvaluationPlan();
    }
    public function test_disableEvaluationPlan_unmanagerableEvaluationPlan_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->evaluationPlan);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeDisableEvaluationPlan();
        });
    }

    protected function executeEnableEvaluationPlan()
    {
        $this->setAssetBelongsToFirm($this->evaluationPlan);
        $this->manager->enableEvaluationPlan($this->evaluationPlan, $this->evaluationPlanData);
    }
    public function test_enableEvaluationPlan_enableEvaluationPlan()
    {
        $this->evaluationPlan->expects($this->once())
                ->method("enable");
        $this->executeEnableEvaluationPlan();
    }
    public function test_enableEvaluationPlan_unmanagerableEvaluationPlan_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->evaluationPlan);
        $this->assertUnmanageableAssetForbiddenError(function () {
            $this->executeEnableEvaluationPlan();
        });
    }

    protected function executeUpdateConsultationSetup()
    {
        $this->setAssetBelongsToFirm($this->consultationSetup);
        $this->setAssetBelongsToFirm($this->feedbackForm);
        $this->setAssetBelongsToFirm($this->consultantFeedbackForm);
        $this->manager->updateConsultationSetup(
                $this->consultationSetup, $this->consultationSetupName, $this->sessionDuration, $this->feedbackForm,
                $this->consultantFeedbackForm);
    }
    public function test_updateConsulataionSetup_updateConsultationSetup()
    {
        $this->consultationSetup->expects($this->once())
                ->method("update")
                ->with($this->consultationSetupName, $this->sessionDuration, $this->feedbackForm, $this->consultantFeedbackForm);
        $this->executeUpdateConsultationSetup();
    }
    public function test_updateConsultationSetup_unmanagedConsultationSetup_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->consultationSetup);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeUpdateConsultationSetup();
        });
    }
    public function test_updateConsultationSetup_unmanagedParticipantFeedbackForm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->feedbackForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeUpdateConsultationSetup();
        });
    }
    public function test_updateConsultationSetup_unmanagedConsultantFeedbackForm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->consultantFeedbackForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeUpdateConsultationSetup();
        });
    }
    
    protected function executeCreateProfileForm()
    {
        return $this->manager->createProfileForm($this->profileFormId, $this->formData);
    }
    public function test_createProfileForm_returnProfileForm()
    {
        $profileForm = new ProfileForm($this->manager->firm, $this->profileFormId, $this->formData);
        $this->assertEquals($profileForm, $this->executeCreateProfileForm());
    }
    
    protected function executeUpdateProfileForm()
    {
        $this->setAssetBelongsToFirm($this->profileForm);
        $this->manager->updateProfileForm($this->profileForm, $this->formData);
    }
    public function test_updateProfileForm_updateProfileForm()
    {
        $this->profileForm->expects($this->once())
                ->method("update")
                ->with($this->formData);
        $this->executeUpdateProfileForm();
    }
    public function test_updateProfileForm_unmanagedProfileForm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->profileForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeUpdateProfileForm();
        });
    }
    
    protected function executeAssignProfileFormToProgram()
    {
        $this->setAssetBelongsToFirm($this->program);
        $this->setAssetBelongsToFirm($this->profileForm);
        $this->manager->assignProfileFormToProgram($this->program, $this->profileForm);
    }
    public function test_assignProfileFormToProgram_returnProgramsAssignProfileFormToProgramResult()
    {
        $this->program->expects($this->once())
                ->method("assignProfileForm")
                ->with($this->profileForm);
        $this->executeAssignProfileFormToProgram();
    }
    public function test_assignProfileFormToProgram_programUnmanaged_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->program);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeAssignProfileFormToProgram();
        });
    }
    public function test_assignProfileFormToProgram_profileFormUnmanaged_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->profileForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeAssignProfileFormToProgram();
        });
    }
    
    protected function executeDisableProgramsProfileForm()
    {
        $this->setAssetBelongsToFirm($this->programsProfileForm);
        $this->manager->disableProgramsProfileForm($this->programsProfileForm);
    }
    public function test_disableProgramsProfileForm_disableProgramsProfileForm()
    {
        $this->programsProfileForm->expects($this->once())
                ->method("disable");
        $this->executeDisableProgramsProfileForm();
    }
    public function test_disableProgramsProfileForm_programsProfileFormUnmanaged_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->programsProfileForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeDisableProgramsProfileForm();
        });
    }
    
    protected function executeRemoveProgram()
    {
        $this->setAssetBelongsToFirm($this->program);
        $this->manager->removeProgram($this->program);
    }
    public function test_removeProgram_removeProgram()
    {
        $this->program->expects($this->once())
                ->method("remove");
        $this->executeRemoveProgram();
    }
    public function test_removeProgram_unamangedProgram_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->program);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeRemoveProgram();
        });
    }
    
    protected function executeChangeMissionsWorksheetForm()
    {
        $this->setAssetBelongsToFirm($this->mission);
        $this->setAssetBelongsToFirm($this->worksheetForm);
        $this->manager->changeMissionsWorksheetForm($this->mission, $this->worksheetForm);
    }
    public function test_changeMissionsWorkshetForm_changeMissionsWorksheetForm()
    {
        $this->mission->expects($this->once())
                ->method("changeWorksheetForm")
                ->with($this->worksheetForm);
        $this->executeChangeMissionsWorksheetForm();
    }
    public function test_changeMissionWorksheetForm_unamanagedMission_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->mission);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeChangeMissionsWorksheetForm();
        });
    }
    public function test_changeMissionWorksheetForm_unamanagedWorksheetForm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->worksheetForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeChangeMissionsWorksheetForm();
        });
    }
    
    protected function executeAssignClientCVForm()
    {
        return $this->manager->assignClientCVForm($this->profileForm);
    }
    public function test_assigneClientCVForm_returnFirmsAssignClientCVFormResult()
    {
        $this->firm->expects($this->once())
                ->method("assignClientCVForm")
                ->with($this->profileForm)
                ->willReturn($id = "clientCVFormId");
        $this->assertEquals($id, $this->executeAssignClientCVForm());
    }
    
    protected function executeDisableClientCVForm()
    {
        $this->setAssetBelongsToFirm($this->clientCVForm);
        $this->manager->disableClientCVForm($this->clientCVForm);
    }
    public function test_disableClientCVForm_disableClientCVForm()
    {
        $this->clientCVForm->expects($this->once())
                ->method("disable");
        $this->executeDisableClientCVForm();
    }
    public function test_disalbeClientCVForm_clientCVFormNotFromSameFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->clientCVForm);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeDisableClientCVForm();
        });
    }
}

class TestableManager extends Manager
{

    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $adminAssignments;

}
