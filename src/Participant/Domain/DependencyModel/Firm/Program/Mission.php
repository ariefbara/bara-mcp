<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\WorksheetForm;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Mission
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var Mission
     */
    protected $parent = null;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     *
     * @var WorksheetForm
     */
    protected $worksheetForm;

    /**
     *
     * @var ArrayCollection
     */
    protected $branches;

    protected function __construct()
    {
        ;
    }

    public function isRootMission(): bool
    {
        return empty($this->parent);
    }

    public function hasBranch(Mission $mission): bool
    {
        return $this->branches->contains($mission);
    }

    //
    public function programEquals(Program $program): bool
    {
        return $this->program === $program;
    }

    public function isSameProgramAsParticipant(Participant $participant): bool
    {
        return $participant->isProgramEquals($this->program);
    }

    //
    public function createWorksheetFormRecord(string $formRecordId, FormRecordData $formRecordData): ?FormRecord
    {
        return isset($this->worksheetForm) ? $this->worksheetForm->createFormRecord($formRecordId, $formRecordData) : null;
    }

}
