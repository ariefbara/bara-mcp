<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use DateTimeImmutable;

class ConsultationSessionFilter
{

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $minStartTime;

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $maxStartTime;

    /**
     *
     * @var bool||null
     */
    protected $containParticipantFeedback;

    /**
     *
     * @var bool||null
     */
    protected $containConsultantFeedback;

    function setMinStartTime(?DateTimeImmutable $minStartTime)
    {
        $this->minStartTime = $minStartTime;
        return $this;
    }

    function setMaxStartTime(?DateTimeImmutable $maxStartTime)
    {
        $this->maxStartTime = $maxStartTime;
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

    function getMinStartTime(): ?DateTimeImmutable
    {
        return $this->minStartTime;
    }

    function getMaxStartTime(): ?DateTimeImmutable
    {
        return $this->maxStartTime;
    }

    public function isContainParticipantFeedback(): ?bool
    {
        return $this->containParticipantFeedback;
    }

    public function isContainConsultantFeedback(): ?bool
    {
        return $this->containConsultantFeedback;
    }

    public function __construct()
    {
        ;
    }

}
