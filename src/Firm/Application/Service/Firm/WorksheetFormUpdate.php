<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\WorksheetForm,
    Shared\FormData
};

class WorksheetFormUpdate
{

    protected $worksheetFormRepository;

    function __construct(WorksheetFormRepository $worksheetFormRepository)
    {
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    public function execute(string $firmId, $worksheetFormId, FormData $formData): WorksheetForm
    {
        $worksheetForm = $this->worksheetFormRepository->ofId($firmId, $worksheetFormId);
        $worksheetForm->update($formData);
        $this->worksheetFormRepository->update();
        return $worksheetForm;
    }

}
