<?php

namespace Bara\Application\Service;

use Firm\Domain\Model\Shared\FormData;

class UpdateWorksheetForm
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
    
    public function execute(string $adminId, string $worksheetFormId, FormData $formData): void
    {
        $worksheetForm = $this->worksheetFormRepository->ofId($worksheetFormId);
        $this->adminRepository->ofId($adminId)
                ->updateWorksheetForm($worksheetForm, $formData);
        $this->adminRepository->update();
    }


}
