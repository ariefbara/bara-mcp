<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\ProfileForm;

interface ProfileFormRepository
{

    public function aProfileFormInFirm(string $firmId, string $profileFormId): ProfileForm;

    public function allProfileFormsInFirm(string $firmId, int $page, int $pageSize);
}
