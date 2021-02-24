<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\WorksheetForm;

class ViewWorksheetForm
{

    /**
     * 
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;

    public function __construct(WorksheetFormRepository $worksheetFormRepository)
    {
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    /**
     * 
     * @param int $page
     * @param int $pageSize
     * @return WorksheetForm[]
     */
    public function showAll(int $page, int $pageSize)
    {
        return $this->worksheetFormRepository->allGlobalWorksheetForms($page, $pageSize);
    }

    public function showById(string $worksheetFormId): WorksheetForm
    {
        return $this->worksheetFormRepository->aGlobalWorksheetForm($worksheetFormId);
    }

}
