<?php

namespace Firm\Application\Service\Firm;

use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm\WorksheetForm,
    Domain\Model\Shared\Form,
    Domain\Model\Shared\FormData
};

class WorksheetFormAdd
{

    protected $worksheetFormRepository;
    protected $firmRepository;

    function __construct(
        WorksheetFormRepository $worksheetFormRepository, FirmRepository $firmRepository)
    {
        $this->worksheetFormRepository = $worksheetFormRepository;
        $this->firmRepository = $firmRepository;
    }

    public function execute(string $firmId, FormData $formData): string
    {
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->worksheetFormRepository->nextIdentity();
        $form = new Form($id, $formData);

        $worksheetForm = new WorksheetForm($firm, $id, $form);
        $this->worksheetFormRepository->add($worksheetForm);

        return $id;
    }

}
