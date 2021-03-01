<?php

namespace Participant\Domain\Model\Participant\OKRPeriod;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\ValueObject\Label;

class Objective
{

    /**
     * 
     * @var OKRPeriod
     */
    protected $okrPeriod;

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
     * @var int
     */
    protected $weight;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $keyResults;
    
    public function isActive(): bool
    {
        return ! $this->disabled;
    }
    
    protected function setWeight(int $weight): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($weight, "bad request: objective's weight is mandatory");
        $this->weight = $weight;
    }
    protected function aggregateKeyResults(ObjectiveData $objectiveData): void
    {
        foreach ($objectiveData->getKeyResultDataIterator() as $keyResultData) {
            $keyResult = new KeyResult($this, Uuid::generateUuid4(), $keyResultData);
            $this->keyResults->add($keyResult);
        }
    }
    protected function assertHasActiveKeyResult(): void
    {
        $p = function (KeyResult $keyResult) {
            return $keyResult->isActive();
        };
        if (empty($this->keyResults->filter($p)->count())) {
            $errorDetail = 'forbidden: objective must have at least one key result';
            throw RegularException::forbidden($errorDetail);
        }
    }
    public function __construct(OKRPeriod $okrPeriod, string $id, ObjectiveData $objectiveData)
    {
        $this->okrPeriod = $okrPeriod;
        $this->id = $id;
        $this->label = new Label($objectiveData->getLabelData());
        $this->setWeight($objectiveData->getWeight());
        $this->disabled = false;
        $this->keyResults = new ArrayCollection();
        $this->aggregateKeyResults($objectiveData);
        $this->assertHasActiveKeyResult();
    }
    
    public function updateAggregate(OKRPeriodData $okrPeriodData): void
    {
        $objectiveData = $okrPeriodData->pullObjectiveDataWithId($this->id);
        if (isset($objectiveData)) {
            $this->disabled = false;
            $this->label = new Label($objectiveData->getLabelData());
            $this->weight = $objectiveData->getWeight();
            foreach ($this->keyResults->getIterator() as $keyResult) {
                $keyResult->updateAggregate($objectiveData);
            }
            $this->aggregateKeyResults($objectiveData);
            $this->assertHasActiveKeyResult();
        } else {
            $this->disable();
        }
    }
    
    public function disable(): void
    {
        $this->disabled = true;
        foreach ($this->keyResults->getIterator() as $keyResult) {
            $keyResult->disable();
        }
    }

}
