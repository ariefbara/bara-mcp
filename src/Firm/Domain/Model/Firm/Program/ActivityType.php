<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Model\AssetBelongsToFirm,
    Model\Firm,
    Model\Firm\Program,
    Model\Firm\Program\ActivityType\ActivityParticipant,
    Service\ActivityTypeDataProvider
};
use Resources\ {
    Uuid,
    ValidationRule,
    ValidationService
};

class ActivityType implements AssetBelongsToFirm
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
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var ArrayCollection
     */
    protected $participants;

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: activity type name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    public function __construct(Program $program, string $id, ActivityTypeDataProvider $activityTypeDataProvider)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($activityTypeDataProvider->getName());
        $this->description = $activityTypeDataProvider->getDescription();
        
        $this->participants = new ArrayCollection();
        foreach ($activityTypeDataProvider->iterateActivityParticipantData() as $activityParticipantData) {
            $id = Uuid::generateUuid4();
            $activityParticipant = new ActivityParticipant($this, $id, $activityParticipantData);
            $this->participants->add($activityParticipant);
        }
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

}
