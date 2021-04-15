<?php

namespace Query\Application\Service\Personnel;

use Query\Infrastructure\QueryFilter\InviteeFilter;

interface InviteeRepository
{

    public function allActivityInvitationsToPersonnel(
            string $personnelId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter);
}
