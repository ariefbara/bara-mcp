<?php

namespace Participant\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

interface FileInfoRepository
{

    public function fileInfoOfUser(string $userId, string $fileInfoId): FileInfo;

    public function fileInfoOfClient(string $firmId, string $clientId, string $fileInfoId): FileInfo;

    public function fileInfoOfTeam(string $teamId, string $fileInfoId): FileInfo;
    
}
