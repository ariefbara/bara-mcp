<?php

namespace Personnel\Domain\Model\Firm\Program;

use Personnel\Domain\Model\Firm\ {
    ConsultationFeedbackForm,
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
     * @var ConsultationFeedbackForm
     */
    protected $participantFeedbackForm;

    /**
     *
     * @var ConsultationFeedbackForm
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

    function getParticipantFeedbackForm(): ConsultationFeedbackForm
    {
        return $this->participantFeedbackForm;
    }

    function getConsultantFeedbackForm(): ConsultationFeedbackForm
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
