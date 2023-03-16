<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

interface LearningProgressRepository
{

    public function learningProgressListBelongsToParticipant(string $participantId, int $page, int $pageSize);

    public function aLearningProgressBelongsToParticipant(string $participantId, string $id);
}
