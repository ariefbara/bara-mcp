<?php

namespace Participant\Application\Listener;

use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\LogViewLearningMaterialActivity;
use Resources\Application\Event\{
    Event,
    Listener
};

class LearningMaterialAccessedByTeamMemberListener implements Listener
{

    /**
     *
     * @var LogViewLearningMaterialActivity
     */
    protected $logViewLearningMaterialActivity;

    public function __construct(LogViewLearningMaterialActivity $logViewLearningMaterialActivity)
    {
        $this->logViewLearningMaterialActivity = $logViewLearningMaterialActivity;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(EventTriggeredByTeamMemberInterface $event): void
    {
        $teamMemberId = $event->getTeamMemberId();
        $participantId = $event->getParticipantId();
        $learningMaterialId = $event->getId();
        $this->logViewLearningMaterialActivity->execute($teamMemberId, $participantId, $learningMaterialId);
    }

}
