<?php

namespace Bara\Application\Service;

use Firm\Domain\Model\Shared\FormData;

class CreateWorksheetForm
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
    
    public function execute(string $adminId, FormData $formData): string
    {
        $id = $this->worksheetFormRepository->nextIdentity();
        $worksheetForm = $this->adminRepository->ofId($adminId)
                ->createWorksheetForm($id, $formData);
        $this->worksheetFormRepository->add($worksheetForm);
        return $id;
    }


}
