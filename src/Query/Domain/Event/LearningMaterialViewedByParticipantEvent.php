<?php

namespace Query\Domain\Event;

use Config\EventList;
use Participant\Application\Listener\EventTriggeredByParticipantInterface;

class LearningMaterialViewedByParticipantEvent implements EventTriggeredByParticipantInterface
{

    /**
     *
     * @var string
     */
    protected $participantId;

    /**
     *
     * @var string
     */
    protected $learningMaterialId;

    public function __construct(string $participantId, string $learningMaterialId)
    {
        $this->participantId = $participantId;
        $this->learningMaterialId = $learningMaterialId;
    }

    public function getId(): string
    {
        return $this->learningMaterialId;
    }

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getName(): string
    {
        return EventList::LEARNING_MATERIAL_VIEWED_BY_PARTICIPANT;
    }

}
