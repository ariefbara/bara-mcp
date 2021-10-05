<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Application\Service\Manager\ManageableByFirm;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Manager\ManagerAttendee;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\MissionData;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use Firm\Domain\Model\Shared\FormData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Resources\Domain\ValueObject\Password;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

class Manager implements CanAttendMeeting
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var string
     */
    protected $phone;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    /**
     *
     * @var bool
     */
    protected $removed = false;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $meetingInvitations;

    private function setName($name)
    {
        $errorDetail = 'bad request: manager name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    private function setEmail($email)
    {
        $errorDetail = 'bad request: manager email is required and must be in valid email format';
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    private function setPhone($phone)
    {
        $errorDetail = 'bad request: manager phone must be in valid phone format';
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::phone()))
                ->execute($phone, $errorDetail);
        $this->phone = $phone;
    }

    function __construct(Firm $firm, string $id, ManagerData $managerData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($managerData->getName());
        $this->setEmail($managerData->getEmail());
        $this->password = new Password($managerData->getPassword());
        $this->setPhone($managerData->getPhone());
        $this->joinTime = new DateTimeImmutable();
        $this->removed = false;
    }

    protected function assertActive()
    {
        if ($this->removed) {
            throw RegularException::forbidden('forbidden: inactive manager');
        }
    }

    protected function assertAssetManageable(ManageableByFirm $asset): void
    {
        if (!$asset->isManageableByFirm($this->firm)) {
            throw RegularException::forbidden('forbidden: unamanaged asset');
        }
    }

    public function createActivityTypeInProgram(
            Program $program, string $activityTypeId, ActivityTypeDataProvider $activityTypeDataProvider): ActivityType
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active manager can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$program->belongsToFirm($this->firm)) {
            $errorDetail = "forbidden: can only manage asset of same firm";
            throw RegularException::forbidden($errorDetail);
        }
        return $program->createActivityType($activityTypeId, $activityTypeDataProvider);
    }

    public function updateActivityType(ActivityType $activityType, ActivityTypeDataProvider $activityTypeDataProvider): void
    {
        $this->assertAssetBelongsToSameFirm($activityType);
        $activityType->update($activityTypeDataProvider);
    }

    public function disableActivityType(ActivityType $activityType): void
    {
        $this->assertAssetBelongsToSameFirm($activityType);
        $activityType->disable();
    }

    public function enableActivityType(ActivityType $activityType): void
    {
        $this->assertAssetBelongsToSameFirm($activityType);
        $activityType->enable();
    }

    public function disableCoordinator(Coordinator $coordinator): void
    {
        $this->assertAssetBelongsToSameFirm($coordinator);
        $coordinator->disable();
    }

    public function disableConsultant(Consultant $consultant): void
    {
        $this->assertAssetBelongsToSameFirm($consultant);
        $consultant->disable();
    }

    public function disablePersonnel(Personnel $personnel): void
    {
        $this->assertAssetBelongsToSameFirm($personnel);
        $personnel->disable();
    }

    public function enablePersonnel(Personnel $personnel): void
    {
        $this->assertAssetBelongsToSameFirm($personnel);
        $personnel->enable();
    }

    public function createEvaluationPlanInProgram(
            Program $program, string $evaluationPlanId, EvaluationPlanData $evaluationPlanData,
            FeedbackForm $reportForm, ?Mission $mission): EvaluationPlan
    {
        $this->assertAssetBelongsToSameFirm($program);
        $this->assertAssetBelongsToSameFirm($reportForm);
        return $program->createEvaluationPlan($evaluationPlanId, $evaluationPlanData, $reportForm, $mission);
    }

    public function updateEvaluationPlan(
            EvaluationPlan $evaluationPlan, EvaluationPlanData $evaluationPlanData, FeedbackForm $reportForm,
            ?Mission $mission): void
    {
        $this->assertAssetBelongsToSameFirm($evaluationPlan);
        $this->assertAssetBelongsToSameFirm($reportForm);
        $evaluationPlan->update($evaluationPlanData, $reportForm, $mission);
    }

    public function disableEvaluationPlan(EvaluationPlan $evaluationPlan): void
    {
        $this->assertAssetBelongsToSameFirm($evaluationPlan);
        $evaluationPlan->disable();
    }

    public function enableEvaluationPlan(EvaluationPlan $evaluationPlan): void
    {
        $this->assertAssetBelongsToSameFirm($evaluationPlan);
        $evaluationPlan->enable();
    }

    public function updateConsultationSetup(
            ConsultationSetup $consultationSetup, string $name, int $sessionDuration,
            FeedbackForm $participantFeedbackForm, FeedbackForm $consultantFeedbackForm): void
    {
        $this->assertAssetBelongsToSameFirm($consultationSetup);
        $this->assertAssetBelongsToSameFirm($participantFeedbackForm);
        $this->assertAssetBelongsToSameFirm($consultantFeedbackForm);
        $consultationSetup->update($name, $sessionDuration, $participantFeedbackForm, $consultantFeedbackForm);
    }

    public function createProfileForm(string $profileFormId, FormData $formData): ProfileForm
    {
        return new ProfileForm($this->firm, $profileFormId, $formData);
    }

    public function updateProfileForm(ProfileForm $profileForm, FormData $formData): void
    {
        $this->assertAssetBelongsToSameFirm($profileForm);
        $profileForm->update($formData);
    }

    public function assignProfileFormToProgram(Program $program, ProfileForm $profileForm): string
    {
        $this->assertAssetBelongsToSameFirm($program);
        $this->assertAssetBelongsToSameFirm($profileForm);
        return $program->assignProfileForm($profileForm);
    }

    public function disableProgramsProfileForm(ProgramsProfileForm $programsProfileForm): void
    {
        $this->assertAssetBelongsToSameFirm($programsProfileForm);
        $programsProfileForm->disable();
    }

    public function removeProgram(Program $program): void
    {
        $this->assertAssetBelongsToSameFirm($program);
        $program->remove();
    }

    protected function assertAssetBelongsToSameFirm(AssetBelongsToFirm $asset): void
    {
        if (!$asset->belongsToFirm($this->firm)) {
            $errorDetail = "forbidden: unamanaged asset";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function createBioForm(string $bioFormId, FormData $formData): BioForm
    {
        return new BioForm($this->firm, $bioFormId, $formData);
    }

    public function updateBioForm(BioForm $bioForm, FormData $formData): void
    {
        $this->assertAssetBelongsToSameFirm($bioForm);
        $bioForm->update($formData);
    }

    public function disableBioForm(BioForm $bioForm): void
    {
        $this->assertAssetBelongsToSameFirm($bioForm);
        $bioForm->disable();
    }

    public function enableBioForm(BioForm $bioForm): void
    {
        $this->assertAssetBelongsToSameFirm($bioForm);
        $bioForm->enable();
    }

    public function createRootMission(
            string $missionId, Program $program, WorksheetForm $worksheetForm, MissionData $missionData): Mission
    {
        $this->assertActive();
        $this->assertAssetManageable($program);
        $this->assertAssetManageable($worksheetForm);
        return $program->createRootMission($missionId, $worksheetForm, $missionData);
    }

    public function createBranchMission(
            string $missionId, Mission $parentMission, WorksheetForm $worksheetForm, MissionData $missionData): Mission
    {
        $this->assertActive();
        $this->assertAssetManageable($parentMission);
        $this->assertAssetManageable($worksheetForm);
        return $parentMission->createBranch($missionId, $worksheetForm, $missionData);
    }

    public function updateMission(Mission $mission, MissionData $missionData): void
    {
        $this->assertActive();
        $this->assertAssetManageable($mission);
        $mission->update($missionData);
    }

    public function publishMission(Mission $mission): void
    {
        $this->assertActive();
        $this->assertAssetManageable($mission);
        $mission->publish();
    }

    public function changeMissionsWorksheetForm(Mission $mission, WorksheetForm $worksheetForm): void
    {
        $this->assertActive();
        $this->assertAssetManageable($mission);
        $this->assertAssetManageable($worksheetForm);
        $mission->changeWorksheetForm($worksheetForm);
    }

    public function handleMutationTask(MutationTaskExecutableByManager $task): void
    {
        $this->assertActive();
        $task->execute($this->firm);
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $activityType, MeetingData $meetingData): Meeting
    {
        $this->assertActive();
        $activityType->assertUsableInFirm($this->firm);
        $meeting = $activityType->createMeeting($meetingId, $meetingData);
        
        $id = Uuid::generateUuid4();
        $managerAttendee = new ManagerAttendee($this, $id, $meeting, true);
        $this->meetingInvitations->add($managerAttendee);
        
        return $meeting;
    }

    public function inviteToMeeting(Meeting $meeting): void
    {
        $this->assertActive();
        $meeting->assertUsableInFirm($this->firm);
        
        $p = function (ManagerAttendee $managerAttendee) use ($meeting) {
            return $managerAttendee->isActiveAttendeeOfMeeting($meeting);
        };
        if (empty($this->meetingInvitations->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $mangaerAttendee = new ManagerAttendee($this, $id, $meeting, false);
            $this->meetingInvitations->add($mangaerAttendee);
        }
    }
    
    public function executeTaskInProgram(Program $program, ITaskInProgramExecutableByManager $task): void
    {
        if ($this->removed) {
            throw RegularException::forbidden("forbidden: only active manager can make this request");
        }
        if (!$program->isManageableByFirm($this->firm)) {
            throw RegularException::forbidden('forbidden: can only manage program owned by firm');
        }
        $task->executeInProgram($program);
    }

}
