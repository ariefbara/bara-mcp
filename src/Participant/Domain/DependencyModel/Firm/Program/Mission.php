<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\ {
    Program,
    WorksheetForm
};
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

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

    public function createWorksheetFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->worksheetForm->createFormRecord($formRecordId, $formRecordData);
    }

}
