<?php

namespace Participant\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

interface FileInfoRepository
{

    public function fileInfoOfUser(string $userId, string $fileInfoId): FileInfo;

    public function fileInfoOfClient(string $firmId, string $clientId, string $fileInfoId): FileInfo;

    public function fileInfoOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $fileInfoId): FileInfo;
}
