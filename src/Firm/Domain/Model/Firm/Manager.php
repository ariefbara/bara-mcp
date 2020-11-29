<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program\ActivityType,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Service\ActivityTypeDataProvider
};
use Resources\ {
    Domain\ValueObject\Password,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;

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

    public function canInvolvedInProgram(Program $program): bool
    {
        return !$this->removed && $program->belongsToFirm($this->firm);
    }

    public function registerAsAttendeeCandidate(Attendee $attendee): void
    {
        $attendee->setManagerAsAttendeeCandidate($this);
    }

    public function roleCorrespondWith(ActivityParticipantType $role): bool
    {
        return $role->isManagerType();
    }
    
    public function initiateMeeting(string $meetingId, ActivityType $meetingType, MeetingData $meetingData): Meeting
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active manager can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$meetingType->belongsToFirm($this->firm)) {
            $errorDetail = "forbidden: unable to manage meeting type from other firm";
            throw RegularException::forbidden($errorDetail);
        }
        return $meetingType->createMeeting($meetingId, $meetingData, $this);
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
    
    
    protected function assertAssetBelongsToSameFirm(\Firm\Domain\Model\AssetBelongsToFirm $asset): void
    {
        if (!$asset->belongsToFirm($this->firm)) {
            $errorDetail = "forbidden: unable to manage asset from other firm";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
