<?php

namespace Query\Domain\Model\Firm\Program\ActivityType;

use Query\Domain\Model\Firm\{
    FeedbackForm,
    Program\ActivityType
};
use SharedContext\Domain\ValueObject\{
    ActivityParticipantPriviledge,
    ActivityParticipantType
};

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
     * @var FeedbackForm|null
     */
    protected $reportForm;

    /**
     *
     * @var bool
     */
    protected $disabled;

    function getActivityType(): ActivityType
    {
        return $this->activityType;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getReportForm(): ?FeedbackForm
    {
        return $this->reportForm;
    }

    function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

    function getParticipantType(): string
    {
        return $this->participantType->getParticipantType();
    }

    public function canInitiate(): bool
    {
        return $this->participantPriviledge->canInitiate();
    }

    public function canAttend(): bool
    {
        return $this->participantPriviledge->canAttend();
    }

}
