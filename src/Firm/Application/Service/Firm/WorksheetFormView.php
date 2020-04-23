<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\WorksheetForm;

class WorksheetFormView
{
    /**
     *
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;
    
    function __construct(WorksheetFormRepository $worksheetFormRepository)
    {
        $this->worksheetFormRepository = $worksheetFormRepository;
    }
    
    public function showById(string $firmId, string $worksheetFormId): WorksheetForm
    {
        return $this->worksheetFormRepository->ofId($firmId, $worksheetFormId);
    }
    
    /**
     * 
     * @param string $firmId
     * @param int $page
     * @param int $pageSize
     * @return WorksheetForm[]
     */
    public function showAll(string $firmId, int $page, int $pageSize)
    {
        return $this->worksheetFormRepository->all($firmId, $page, $pageSize);
    }

}
