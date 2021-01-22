<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\BioForm;

interface BioFormRepository
{

    public function aBioFormInFirm(string $firmId, string $bioFormId): BioForm;

    public function allBioFormsInFirm(string $firmId, int $page, int $pageSize, ?bool $disableStatus);
}
