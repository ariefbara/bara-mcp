<?php

namespace SharedContext\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

interface FileInfoRepository
{

    public function aFileInfoBelongsToPersonnel(string $firmId, string $personnelId, string $fileInfoId): FileInfo;

    public function aFileInfoBelongsToClient(string $firmId, string $clientId, string $fileInfoId): FileInfo;

    public function aFileInfoBelongsToTeam(string $firmId, string $teamId, string $fileInfoId): FileInfo;

    public function aFileInfoBelongsToUser(string $userId, string $fileInfoId): FileInfo;

    public function aFileInfoBelongsToManager(string $firmId, string $managerId, string $fileInfoId): FileInfo;
}
