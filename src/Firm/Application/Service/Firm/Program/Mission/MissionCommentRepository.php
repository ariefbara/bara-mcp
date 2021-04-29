<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\MissionComment;

interface MissionCommentRepository
{

    public function nextIdentity(): string;

    public function add(MissionComment $missionComment): void;
    
    public function ofId(string $missionCommentId): MissionComment;
}
