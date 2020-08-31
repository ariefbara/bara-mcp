<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    FeedbackForm,
    Program
};
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\ {
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
            FeedbackForm $participantFeedbackForm, FeedbackForm $consultantFeedbackForm)
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
    
    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->program->getFirmWhitelableInfo();
    }
    
    public function getProgramId(): string
    {
        return $this->program->getId();
    }

}
