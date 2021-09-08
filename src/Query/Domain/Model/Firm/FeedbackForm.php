<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Program\EvaluationPlan\IContainSummaryTable;
use Query\Domain\Model\Shared\ContainFormInterface;
use Query\Domain\Model\Shared\Form;

class FeedbackForm implements ContainFormInterface
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function getDescription(): ?string
    {
        return $this->form->getDescription();
    }

    public function getName(): string
    {
        return $this->form->getName();
    }

    public function getUnremovedAttachmentFields()
    {
        return $this->form->getUnremovedAttachmentFields();
    }

    public function getUnremovedIntegerFields()
    {
        return $this->form->getUnremovedIntegerFields();
    }

    public function getUnremovedMultiSelectFields()
    {
        return $this->form->getUnremovedMultiSelectFields();
    }

    public function getUnremovedSingleSelectFields()
    {
        return $this->form->getUnremovedSingleSelectFields();
    }

    public function getUnremovedStringFields()
    {
        return $this->form->getUnremovedStringFields();
    }

    public function getUnremovedTextAreaFields()
    {
        return $this->form->getUnremovedTextAreaFields();
    }
    
//    public function toArrayOfSummaryTableHeader(): array
//    {
//        return $this->form->toArrayOfSummaryTableHeader();
//    }
//    
//    public function generateSummaryTableEntryFromRecord(FormRecord $formRecord): array
//    {
//        return $this->form->generateSummaryTableEntryFromRecord($formRecord);
//    }
    
    public function appendAllFieldsAsHeaderColumnOfSummaryTable(
            IContainSummaryTable $containSummaryTable, int $startColNumber): void
    {
        $this->form->appendAllFieldsAsHeaderColumnOfSummaryTable($containSummaryTable, $startColNumber);
    }

}
