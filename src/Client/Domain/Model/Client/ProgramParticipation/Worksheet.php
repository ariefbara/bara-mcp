<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\ {
    Client\ClientNotification,
    Client\ProgramParticipation,
    Client\ProgramParticipation\Worksheet\Comment,
    Firm\Program\Mission
};
use Resources\ {
    Exception\RegularException,
    ValidationRule,
    ValidationService
};
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};

class Worksheet
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

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

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: worksheet name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function __construct(
            ProgramParticipation $programParticipation, string $id, string $name, Mission $mission,
            FormRecord $formRecord)
    {
        $this->programParticipation = $programParticipation;
        $this->id = $id;
        $this->setName($name);
        $this->mission = $mission;
        $this->formRecord = $formRecord;
        $this->removed = false;
    }

    public static function createRootWorksheet(
            ProgramParticipation $programParticipation, string $id, string $name, Mission $mission,
            FormRecord $formRecord): self
    {
        if (!$mission->isRootMission()) {
            $errorDetail = 'forbidden: root worksheet can only refer to root mission';
            throw RegularException::forbidden($errorDetail);
        }
        return new static($programParticipation, $id, $name, $mission, $formRecord);
    }

    public function createBranchWorksheet(string $id, string $name, Mission $mission, FormRecord $formRecord): self
    {
        if (!$this->mission->hasBranch($mission)) {
            $errorDetail = "forbidden: parent worksheet mission doesn't contain this mission";
            throw RegularException::forbidden($errorDetail);
        }
        $branch = new static($this->programParticipation, $id, $name, $mission, $formRecord);
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

    public function createClientNotification(string $id, string $message, Comment $comment): ClientNotification
    {
        return $this->programParticipation->createNotificationForComment($id, $message, $comment);
    }

}
