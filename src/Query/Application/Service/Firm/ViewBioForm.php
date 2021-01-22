<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\BioForm;

class ViewBioForm
{

    /**
     * 
     * @var BioFormRepository
     */
    protected $bioFormRepository;

    public function __construct(BioFormRepository $bioFormRepository)
    {
        $this->bioFormRepository = $bioFormRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $disableStatus
     * @return BioForm[]
     */
    public function showAll(string $firmId, int $page, int $pageSize, ?bool $disableStatus)
    {
        return $this->bioFormRepository->allBioFormsInFirm($firmId, $page, $pageSize, $disableStatus);
    }

    public function showById(string $firmId, string $bioFormId): BioForm
    {
        return $this->bioFormRepository->aBioFormInFirm($firmId, $bioFormId);
    }

}
