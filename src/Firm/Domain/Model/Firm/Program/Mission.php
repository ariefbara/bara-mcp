<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Application\Service\Manager\ManageableByFirm;
use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\WorksheetForm;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;

class Mission implements AssetBelongsToFirm, ManageableByFirm, AssetInProgram
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
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var string
     */
    protected $position = null;

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
    protected $branches = null;


    protected function setName(string $name): void
    {
        $errorDetail = "bad request: mission name is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }
    
    public function __construct(Program $program, string $id, WorksheetForm $worksheetForm, MissionData $missionData)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($missionData->getName());
        $this->description = $missionData->getDescription();
        $this->position = $missionData->getPosition();
        $this->worksheetForm = $worksheetForm;
        $this->published = false;
    }

    public function createBranch(string $id, WorksheetForm $worksheetForm, MissionData $missionData): self
    {
        $branch = new static($this->program, $id, $worksheetForm, $missionData);
        $branch->parent = $this;
        return $branch;
    }

    public function update(MissionData $missionData): void
    {
        $this->setName($missionData->getName());
        $this->description = $missionData->getDescription();
        $this->position = $missionData->getPosition();
    }

    public function publish(): void
    {
        $this->assertUnpublished();
        $this->published = true;
    }
    
    public function changeWorksheetForm(WorksheetForm $worksheetForm): void
    {
        if ($this->published) {
            $errorDetail = "forbidden: can only change worksheet form of unpublished mission";
            throw RegularException::forbidden($errorDetail);
        }
        $this->worksheetForm = $worksheetForm;
    }

    protected function assertUnpublished(): void
    {
        if ($this->published) {
            $errorDetail = "forbidden: request only valid for non published mission";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

    public function isManageableByFirm(Firm $firm): bool
    {
        return $this->program->isManageableByFirm($firm);
    }
    
    public function receiveComment(
            string $missionCommentId, MissionCommentData $missionCommentData, string $userId, string $userName): MissionComment
    {
        return new MissionComment($this, $missionCommentId, $missionCommentData, $userId, $userName);
    }
    
    public function addLearningMaterial(string $learningMaterialId, LearningMaterialData $learningMaterialData): LearningMaterial
    {
        return new LearningMaterial($this, $learningMaterialId, $learningMaterialData);
    }
    
    public function assertAccessibleInFirm(Firm $firm): void
    {
        $this->program->assertAccessibleInFirm($firm);
    }

}
