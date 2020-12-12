<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\ProfileForm;

class ViewProfileForm
{

    /**
     * 
     * @var ProfileFormRepository
     */
    protected $profileFormRepository;

    function __construct(ProfileFormRepository $profileFormRepository)
    {
        $this->profileFormRepository = $profileFormRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return ProfileForm[]
     */
    public function showAlll(string $firmId, int $page, int $pageSize)
    {
        return $this->profileFormRepository->allProfileFormsInFirm($firmId, $page, $pageSize);
    }

    public function showById(string $firmId, string $profileFormId): ProfileForm
    {
        return $this->profileFormRepository->aProfileFormInFirm($firmId, $profileFormId);
    }

}
