<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantPriviledge;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class ActivityParticipant
{

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipantType
     */
    protected $participantType;

    /**
     *
     * @var ActivityParticipantPriviledge
     */
    protected $participantPriviledge;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;
    
    /**
     *
     * @var bool
     */
    protected $disabled;
    
    function __construct(ActivityType $activityType, string $id, ActivityParticipantData $activityParticipantData)
    {
        $this->activityType = $activityType;
        $this->id = $id;
        $this->participantType = new ActivityParticipantType($activityParticipantData->getParticipantType());
        $this->participantPriviledge = new ActivityParticipantPriviledge(
                $activityParticipantData->getCanInitiate(), $activityParticipantData->getCanAttend());
        $this->reportForm = $activityParticipantData->getReportForm();
        $this->disabled = false;

        if (isset($this->reportForm) && !$this->reportForm->belongsToSameFirmAs($this->activityType)) {
            $errorDetail = "forbidden: can only assignt feedback form in your firm";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function update(ActivityTypeDataProvider $activityTypeDataProvider): void
    {
        $activityParticipantData = $activityTypeDataProvider
                ->pullActivityParticipantDataCorrespondWithType($this->participantType->getParticipantType());
        if (empty($activityParticipantData)) {
            $this->disabled = true;
        } else {
            $this->disabled = false;
            $this->participantPriviledge = new ActivityParticipantPriviledge(
                    $activityParticipantData->getCanInitiate(), $activityParticipantData->getCanAttend());
            $this->reportForm = $activityParticipantData->getReportForm();
        }
    }
    
    public function isActiveTypeCorrespondWithRole(ActivityParticipantType $activityParticipantType): bool
    {
        return !$this->disabled && $this->participantType->sameValueAs($activityParticipantType);
    }
    
    public function assertCanAttend(): void
    {
        if (!$this->participantPriviledge->canAttend()) {
            throw RegularException::forbidden('forbidden: insuficient role to cannot attend meeting');
        }
    }
    
    public function assertCanInitiate(): void
    {
        if (!$this->participantPriviledge->canInitiate()) {
            throw RegularException::forbidden('forbidden: insuficient role to cannot initiate meeting');
        }
    }
    
}
