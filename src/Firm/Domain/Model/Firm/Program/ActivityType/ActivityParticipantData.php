<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Firm\Domain\Model\Firm\FeedbackForm;

class ActivityParticipantData
{

    /**
     *
     * @var string|null
     */
    protected $participantType;

    /**
     *
     * @var bool|null
     */
    protected $canInitiate;

    /**
     *
     * @var bool|null
     */
    protected $canAttend;

    /**
     *
     * @var FeedbackForm|null
     */
    protected $reportForm;

    public function getParticipantType(): ?string
    {
        return $this->participantType;
    }

    public function getCanInitiate(): ?bool
    {
        return $this->canInitiate;
    }

    public function getCanAttend(): ?bool
    {
        return $this->canAttend;
    }

    public function getReportForm(): ?FeedbackForm
    {
        return $this->reportForm;
    }

    public function __construct(?string $participantType, ?bool $canInitiate, ?bool $canAttend,
            ?FeedbackForm $reportForm)
    {
        $this->participantType = $participantType;
        $this->canInitiate = $canInitiate;
        $this->canAttend = $canAttend;
        $this->reportForm = $reportForm;
    }

}
