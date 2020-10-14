<?php

namespace Query\Domain\Event;

use Participant\Application\Listener\EventTriggeredByTeamMemberInterface;

class LearningMaterialViewedByTeamMemberEvent implements EventTriggeredByTeamMemberInterface
{

    /**
     *
     * @var string
     */
    protected $teamMemberId;

    /**
     *
     * @var LearningMaterialViewedByParticipantEvent
     */
    protected $learningMaterialViewedByParticipantEvent;

    public function __construct(
            string $teamMemberId, LearningMaterialViewedByParticipantEvent $learningMaterialViewedByParticipantEvent)
    {
        $this->teamMemberId = $teamMemberId;
        $this->learningMaterialViewedByParticipantEvent = $learningMaterialViewedByParticipantEvent;
    }

    public function getId(): string
    {
        return $this->learningMaterialViewedByParticipantEvent->getId();
    }

    public function getParticipantId(): string
    {
        return $this->learningMaterialViewedByParticipantEvent->getParticipantId();
    }

    public function getTeamMemberId(): string
    {
        return $this->teamMemberId;
    }

    public function getName(): string
    {
        return $this->learningMaterialViewedByParticipantEvent->getName();
    }

}
