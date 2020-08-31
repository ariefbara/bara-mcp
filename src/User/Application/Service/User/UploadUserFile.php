<?php

namespace User\Application\Service\User;

use SharedContext\Domain\ {
    Model\SharedEntity\FileInfoData,
    Service\UploadFile
};
use User\Application\Service\UserRepository;

class UploadUserFile
{

    /**
     *
     * @var UserFileInfoRepository
     */
    protected $userFileInfoRepository;

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     *
     * @var UploadFile
     */
    protected $uploadFile;

    function __construct(
            UserFileInfoRepository $userFileInfoRepository, UserRepository $userRepository, UploadFile $uploadFile)
    {
        $this->userFileInfoRepository = $userFileInfoRepository;
        $this->userRepository = $userRepository;
        $this->uploadFile = $uploadFile;
    }

    public function execute(string $userId, FileInfoData $fileInfoData, $contents): string
    {
        $id = $this->userFileInfoRepository->nextIdentity();
        $userFileInfo = $this->userRepository->ofId($userId)->createUserFileInfo($id, $fileInfoData);
        
        $userFileInfo->uploadContents($this->uploadFile, $contents);
        $this->userFileInfoRepository->add($userFileInfo);
        
        return $id;
    }

}
