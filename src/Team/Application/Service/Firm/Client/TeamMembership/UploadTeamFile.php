<?php

namespace Team\Application\Service\Firm\Client\TeamMembership;

use SharedContext\Domain\{
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use Team\Application\Service\Firm\Client\TeamMembershipRepository;

class UploadTeamFile
{

    /**
     *
     * @var TeamFileInfoRepository
     */
    protected $teamFileInfoRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    public function __construct(
            TeamFileInfoRepository $teamFileInfoRepository, TeamMembershipRepository $teamMembershipRepository,
            UploadFile $uploadFile)
    {
        $this->teamFileInfoRepository = $teamFileInfoRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->teamFileInfoRepository->nextIdentity();
        $teamFileInfo = $this->teamMembershipRepository->aTeamMembershipOfClient($firmId, $clientId, $teamMembershipId)
                ->uploadFile($id, $fileInfoData);
        $teamFileInfo->uploadContents($this->uploadFile, $contents);
        
        $this->teamFileInfoRepository->add($teamFileInfo);
        return $id;
    }

}
