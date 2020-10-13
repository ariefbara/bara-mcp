<?php

namespace Participant\Domain\Model\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\AssetBelongsToParticipantInterface,
    Model\Participant,
    Model\Participant\Worksheet\Comment,
    Model\Participant\Worksheet\WorksheetActivityLog
};
use Resources\ {
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

class Worksheet implements AssetBelongsToTeamInterface, AssetBelongsToParticipantInterface
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

    /**
     *
     * @var ArrayCollection
     */
    protected $worksheetActivityLogs;

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
            Participant $participant, string $id, string $name, Mission $mission, FormRecordData $formRecordData,
            ?TeamMembership $teamMember = null)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->setName($name);
        $this->mission = $mission;
        $this->formRecord = $this->mission->createWorksheetFormRecord($id, $formRecordData);
        $this->removed = false;

        $this->worksheetActivityLogs = new ArrayCollection();
        $this->addActivityLog("submitted worksheet", $teamMember);
    }
    
    public function belongsToTeam(Team $team): bool
    {
        return $this->participant->belongsToTeam($team);
    }

    public static function createRootWorksheet(
            Participant $participant, string $id, string $name, Mission $mission, FormRecordData $formRecordData,
            ?TeamMembership $teamMember): self
    {
        if (!$mission->isRootMission()) {
            $errorDetail = 'forbidden: root worksheet can only refer to root mission';
            throw RegularException::forbidden($errorDetail);
        }
        return new static($participant, $id, $name, $mission, $formRecordData, $teamMember);
    }

    public function createBranchWorksheet(
            string $id, string $name, Mission $mission, FormRecordData $formRecordData,
            ?TeamMembership $teamMember = null): self
    {
        if (!$this->mission->hasBranch($mission)) {
            $errorDetail = "forbidden: parent worksheet mission doesn't contain this mission";
            throw RegularException::forbidden($errorDetail);
        }
        $branch = new static($this->participant, $id, $name, $mission, $formRecordData, $teamMember);
        $branch->parent = $this;
        return $branch;
    }

    public function update(string $name, FormRecordData $formRecordData, ?TeamMembership $teamMember = null): void
    {
        $this->setName($name);
        $this->formRecord->update($formRecordData);
        $this->addActivityLog("updated worksheet", $teamMember);
    }

    public function remove(?TeamMembership $teamMember = null): void
    {
        $this->removed = true;
        $this->addActivityLog("removed worksheet", $teamMember);
    }

    public function createComment(string $commentId, string $message, ?TeamMembership $teamMember): Comment
    {
        return new Comment($this, $commentId, $message, $teamMember);
    }

    protected function addActivityLog(string $message, ?TeamMembership $teamMember): void
    {
        $message = isset($teamMember) ? "team member $message" : "participant $message";
        $id = Uuid::generateUuid4();
        $worksheetActivityLog = new WorksheetActivityLog($this, $id, $message, $teamMember);

        $this->worksheetActivityLogs->add($worksheetActivityLog);
    }

    public function belongsToParticipant(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

}
