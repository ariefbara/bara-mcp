<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\{
    FeedbackForm,
    Program
};

class ConsultationSetup
{

    /**
     *
     * @var Program
     */
    protected $program;

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
     * @var int
     */
    protected $sessionDuration;

    /**
     *
     * @var FeedbackForm
     */
    protected $participantFeedbackForm;

    /**
     *
     * @var FeedbackForm
     */
    protected $consultantFeedbackForm;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSessionDuration(): int
    {
        return $this->sessionDuration;
    }

    function getParticipantFeedbackForm(): FeedbackForm
    {
        return $this->participantFeedbackForm;
    }

    function getConsultantFeedbackForm(): FeedbackForm
    {
        return $this->consultantFeedbackForm;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

}
