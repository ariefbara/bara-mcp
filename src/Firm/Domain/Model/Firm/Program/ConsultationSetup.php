<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\{
    ConsultationFeedbackForm,
    Program
};
use Resources\{
    ValidationRule,
    ValidationService
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

    protected function setName(string $name): void
    {
        $errorDetail = 'bad request: consultation setup name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setSessionDuration(int $sessionDuration): void
    {

        $errorDetail = 'bad request: consultation setup session duration is required';
        ValidationService::build()
                ->addRule(ValidationRule::integerValue())
                ->addRule(ValidationRule::notEmpty())
                ->execute($sessionDuration, $errorDetail);
        $this->sessionDuration = $sessionDuration;
    }

    function __construct(
            Program $program, string $id, string $name, int $sessionDuration,
            ConsultationFeedbackForm $participantFeedbackForm, ConsultationFeedbackForm $consultantFeedbackForm)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($name);
        $this->setSessionDuration($sessionDuration);
        $this->participantFeedbackForm = $participantFeedbackForm;
        $this->consultantFeedbackForm = $consultantFeedbackForm;
        $this->removed = false;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
