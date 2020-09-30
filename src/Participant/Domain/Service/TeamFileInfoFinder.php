<?php

namespace Participant\Domain\Service;

use SharedContext\Domain\Model\SharedEntity\{
    FileInfo,
    FormRecordData\IFileInfoFinder
};

class TeamFileInfoFinder implements IFileInfoFinder
{

    /**
     *
     * @var FileInfoRepository
     */
    protected $fileInfoRepository;

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $clientId;

    /**
     *
     * @var string
     */
    protected $teamMembershipId;

    public function __construct(FileInfoRepository $fileInfoRepository, string $firmId, string $clientId,
            string $teamMembershipId)
    {
        $this->fileInfoRepository = $fileInfoRepository;
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->teamMembershipId = $teamMembershipId;
    }

    public function ofId(string $fileInfoId): FileInfo
    {
        return $this->fileInfoRepository->fileInfoOfTeamWhereClientIsMember(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $fileInfoId);
    }

}
