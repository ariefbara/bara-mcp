<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;

class ConsultationSessionFilter
{

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $minStartTime;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $maxEndTime;

    /**
     *
     * @var bool|null
     */
    protected $containParticipantFeedback;

    /**
     *
     * @var bool|null
     */
    protected $containConsultantFeedback;

    public function setMinStartTime(?DateTimeImmutable $minStartTime)
    {
        $this->minStartTime = $minStartTime;
        return $this;
    }

    public function setMaxEndTime(?DateTimeImmutable $maxEndTime)
    {
        $this->maxEndTime = $maxEndTime;
        return $this;
    }

    public function setContainParticipantFeedback(?bool $containParticipantFeedback)
    {
        $this->containParticipantFeedback = $containParticipantFeedback;
        return $this;
    }

    public function setContainConsultantFeedback(?bool $containConsultantFeedback)
    {
        $this->containConsultantFeedback = $containConsultantFeedback;
        return $this;
    }

    public function __construct()
    {
        ;
    }

    public function getMinStartTime(): ?DateTimeImmutable
    {
        return $this->minStartTime;
    }

    public function getMaxEndTime(): ?DateTimeImmutable
    {
        return $this->maxEndTime;
    }

    public function isContainParticipantFeedback(): ?bool
    {
        return $this->containParticipantFeedback;
    }

    public function isContainConsultantFeedback(): ?bool
    {
        return $this->containConsultantFeedback;
    }

}
