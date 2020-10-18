<?php

namespace Participant\Application\Listener;

use Participant\Application\Service\Participant\LogViewLearningMaterialActivity;
use Resources\Application\Event\{
    Event,
    Listener
};

class LearningMaterialAccessedByParticipantListener implements Listener
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

    protected function execute(EventTriggeredByParticipantInterface $event): void
    {
        $participantId = $event->getParticipantId();
        $learningMaterialId = $event->getId();
        $this->logViewLearningMaterialActivity->execute($participantId, $learningMaterialId);
    }

}
