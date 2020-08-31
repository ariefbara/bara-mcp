<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\ {
    DependencyEntity\Firm\Program\Mission,
    Participant,
    Participant\Worksheet\Comment
};
use Resources\ {
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

class Worksheet
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var Worksheet
     */
    protected $parent = null;

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
     * @var Mission
     */
    protected $mission;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    public function getId(): string
    {
        return $this->id;
    }

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: worksheet name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function __construct(
            Participant $participant, string $id, string $name, Mission $mission, FormRecord $formRecord)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->setName($name);
        $this->mission = $mission;
        $this->formRecord = $formRecord;
        $this->removed = false;
    }

    public static function createRootWorksheet(
            Participant $participant, string $id, string $name, Mission $mission, FormRecord $formRecord): self
    {
        if (!$mission->isRootMission()) {
            $errorDetail = 'forbidden: root worksheet can only refer to root mission';
            throw RegularException::forbidden($errorDetail);
        }
        return new static($participant, $id, $name, $mission, $formRecord);
    }

    public function createBranchWorksheet(string $id, string $name, Mission $mission, FormRecord $formRecord): self
    {
        if (!$this->mission->hasBranch($mission)) {
            $errorDetail = "forbidden: parent worksheet mission doesn't contain this mission";
            throw RegularException::forbidden($errorDetail);
        }
        $branch = new static($this->participant, $id, $name, $mission, $formRecord);
        $branch->parent = $this;
        return $branch;
    }

    public function update(string $name, FormRecordData $formRecordData): void
    {
        $this->setName($name);
        $this->formRecord->update($formRecordData);
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function createComment(string $commentId, string $message): Comment
    {
        return new Comment($this, $commentId, $message);
    }

}
