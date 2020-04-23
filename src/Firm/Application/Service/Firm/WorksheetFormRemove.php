<?php

namespace Firm\Application\Service\Firm;

class WorksheetFormRemove
{
    protected $worksheetFormRepository;
    
    function __construct(WorksheetFormRepository $worksheetFormRepository)
    {
        $this->worksheetFormRepository = $worksheetFormRepository;
    }
    
    public function execute($firmId, $worksheetFormId): void
    {
        $this->worksheetFormRepository->ofId($firmId, $worksheetFormId)
            ->remove();
        $this->worksheetFormRepository->update();
    }

}
