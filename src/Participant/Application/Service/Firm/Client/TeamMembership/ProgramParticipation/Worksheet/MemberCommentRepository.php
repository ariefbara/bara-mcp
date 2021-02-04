<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Domain\DependencyModel\Firm\Client\TeamMember\MemberComment;

interface MemberCommentRepository
{

    public function nextIdentity(): string;

    public function add(MemberComment $memberComment): void;
}
