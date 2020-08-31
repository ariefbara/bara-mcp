<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserFileInfo;

class ViewUserFileInfo
{

    /**
     *
     * @var UserFileInfoRepository
     */
    protected $userFileInfoRepository;

    public function __construct(UserFileInfoRepository $userFileInfoRepository)
    {
        $this->userFileInfoRepository = $userFileInfoRepository;
    }

    public function showById(string $user, string $userFileInfoId): UserFileInfo
    {
        return $this->userFileInfoRepository->ofId($user, $userFileInfoId);
    }

}
