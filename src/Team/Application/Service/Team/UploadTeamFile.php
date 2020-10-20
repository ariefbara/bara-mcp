<?php

namespace Team\Application\Service\Team;

use SharedContext\Domain\{
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};

class UploadTeamFile
{

    /**
     *
     * @var TeamFileInfoRepository
     */
    protected $teamFileInfoRepository;

    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    public function __construct(
            TeamFileInfoRepository $teamFileInfoRepository, MemberRepository $memberRepository, UploadFile $uploadFile)
    {
        $this->teamFileInfoRepository = $teamFileInfoRepository;
        $this->memberRepository = $memberRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->teamFileInfoRepository->nextIdentity();
        $teamFileInfo = $this->memberRepository->aMemberCorrespondWithClient($firmId, $teamId, $clientId)
                ->uploadFile($id, $fileInfoData);
        $teamFileInfo->uploadContents($this->uploadFile, $contents);

        $this->teamFileInfoRepository->add($teamFileInfo);
        return $id;
    }

}
