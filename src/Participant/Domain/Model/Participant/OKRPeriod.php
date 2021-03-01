<?php

namespace Participant\Domain\Model\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Resources\Domain\ValueObject\DateInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class OKRPeriod implements ManageableByParticipant
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

    /**
     * 
     * @var DateInterval
     */
    protected $period;
    
    /**
     * 
     * @var OKRPeriodApprovalStatus
     */
    protected $approvalStatus;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $objectives;

    protected function aggregateObjectives(OKRPeriodData $okrPeriodData): void
    {
        foreach ($okrPeriodData->getObjectiveDataCollectionIterator() as $objectiveData) {
            $id = Uuid::generateUuid4();
            $objective = new Objective($this, $id, $objectiveData);
            $this->objectives->add($objective);
        }
    }

    protected function assertHasActiveObjective(): void
    {
        $p = function (Objective $objective) {
            return $objective->isActive();
        };
        if (empty($this->objectives->filter($p)->count())) {
            $errorDetail = 'forbidden: okr period must have at least one active objective';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertActive(): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: okr period already cancelled');
        }
    }
    protected function assertUnconcluded(): void
    {
        if ($this->approvalStatus->isConcluded()) {
            throw RegularException::forbidden('forbidden: okr period already concluded');
        }
    }

    public function __construct(Participant $participant, string $id, OKRPeriodData $okrPeriodData)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->label = new Label($okrPeriodData->getLabelData());
        $this->period = new DateInterval($okrPeriodData->getStartDate(), $okrPeriodData->getEndDate());
        $this->approvalStatus = new OKRPeriodApprovalStatus(OKRPeriodApprovalStatus::UNCONCLUDED);
        $this->cancelled = false;
        $this->objectives = new ArrayCollection();
        $this->aggregateObjectives($okrPeriodData);
        $this->assertHasActiveObjective();
    }

    public function isManageableByParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

    public function update(OKRPeriodData $okrPeriodData): void
    {
        $this->assertActive();
        $this->assertUnconcluded();
        $this->label = new Label($okrPeriodData->getLabelData());
        $this->period = new DateInterval($okrPeriodData->getStartDate(), $okrPeriodData->getEndDate());
        foreach ($this->objectives->getIterator() as $objective) {
            $objective->updateAggregate($okrPeriodData);
        }
        $this->aggregateObjectives($okrPeriodData);
        $this->assertHasActiveObjective();
    }

    public function cancel(): void
    {
        $this->assertActive();
        $this->assertUnconcluded();
        $this->cancelled = true;
        foreach ($this->objectives->getIterator() as $objective) {
            $objective->disable();
        }
    }

    public function inConflictWith(OKRPeriod $other): bool
    {
        return !$this->cancelled 
                && !$this->approvalStatus->isRejected() 
                && $this->id !== $other->id
                && $this->period->intersectWith($other->period);
    }

}
