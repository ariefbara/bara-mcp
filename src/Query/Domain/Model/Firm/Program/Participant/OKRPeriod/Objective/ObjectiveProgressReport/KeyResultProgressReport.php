<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport\KeyResultProgressReportAttachment;

class KeyResultProgressReport
{

    /**
     * 
     * @var ObjectiveProgressReport
     */
    protected $objectiveProgressReport;

    /**
     * 
     * @var KeyResult
     */
    protected $keyResult;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $value;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attachments;

    public function getObjectiveProgressReport(): ObjectiveProgressReport
    {
        return $this->objectiveProgressReport;
    }

    public function getKeyResult(): KeyResult
    {
        return $this->keyResult;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * 
     * @return KeyResultProgressReportAttachment
     */
    public function getAttachments()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->attachments->matching($criteria)->getIterator();
    }

    protected function __construct()
    {
        
    }

}
