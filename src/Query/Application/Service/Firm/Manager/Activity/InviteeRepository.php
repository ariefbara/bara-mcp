<?php

namespace Query\Application\Service\Firm\Manager\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;


interface InviteeRepository
{

    public function anInviteeInManagerActivity(string $firmId, string $managerId, string $inviteeId): Invitee;

    public function allInviteesInManagerActivity(
            string $firmId, string $managerId, string $activityId, int $page, int $pageSize);
}
