<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;

class EvaluationPlan implements AssetBelongsToFirm, AssetInProgram
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
    protected $interval;

    /**
     *
     * @var bool
     */
    protected $disabled;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;

    /**
     * 
     * @var Mission
     */
    protected $mission;

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: evaluation plan name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }
    
    protected function assertMissionUsable(): void
    {
        if (isset($this->mission) && !$this->mission->belongsToProgram($this->program)) {
            throw RegularException::forbidden('forbidden: mission must be from same program');
        }
    }

    function __construct(
            Program $program, string $id, EvaluationPlanData $evaluationPlanData, FeedbackForm $reportForm,
            ?Mission $mission)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($evaluationPlanData->getName());
        $this->setInterval($evaluationPlanData->getInterval());
        $this->disabled = false;
        $this->reportForm = $reportForm;
        $this->mission = $mission;
        $this->assertMissionUsable();
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

    public function update(EvaluationPlanData $evaluationPlanData, FeedbackForm $reportForm, ?Mission $mission): void
    {
        $this->setName($evaluationPlanData->getName());
        $this->setInterval($evaluationPlanData->getInterval());
        $this->reportForm = $reportForm;
        $this->mission = $mission;
        $this->assertMissionUsable();
    }

    public function disable(): void
    {
        $this->disabled = true;
    }

    public function enable(): void
    {
        $this->disabled = false;
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

}
