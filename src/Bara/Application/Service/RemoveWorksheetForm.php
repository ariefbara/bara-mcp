<?php

namespace Bara\Application\Service;

class RemoveWorksheetForm
{

    /**
     * 
     * @var AdminRepository
     */
    protected $adminRepository;

    /**
     * 
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;

    public function __construct(AdminRepository $adminRepository, WorksheetFormRepository $worksheetFormRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }
    
    public function execute(string $adminId, string $worksheetFormId): void
    {
        $worksheetForm = $this->worksheetFormRepository->ofId($worksheetFormId);
        $this->adminRepository->ofId($adminId)
                ->removeWorksheetForm($worksheetForm);
        $this->adminRepository->update();
    }

}
