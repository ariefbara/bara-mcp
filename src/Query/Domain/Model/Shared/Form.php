<?php

namespace Query\Domain\Model\Shared;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\EvaluationPlan\IContainSummaryTable;
use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField;
use Resources\Exception\RegularException;

class Form
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $stringFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $integerFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $textAreaFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $attachmentFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $singleSelectFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $multiSelectFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $sections;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): string
    {
        return $this->description;
    }

    function getUnremovedStringFields()
    {
        return $this->stringFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedIntegerFields()
    {
        return $this->integerFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedTextAreaFields()
    {
        return $this->textAreaFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedAttachmentFields()
    {
        return $this->attachmentFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedSingleSelectFields()
    {
        return $this->singleSelectFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedMultiSelectFields()
    {
        return $this->multiSelectFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedSections()
    {
        return $this->sections->matching($this->nonRemovedCriteria())->getIterator();
    }

    protected function __construct()
    {
        ;
    }

    protected function nonRemovedCriteria()
    {
        return Criteria::create()
                        ->andWhere(Criteria::expr()->eq('removed', false));
    }

    /*
      public function toArrayOfSummaryTableHeader(): array
      {
      $summaryTableHeader = [];
      foreach ($this->iterateAllFieldsOrderedByPosition() as $field) {
      $summaryTableHeader[] = $field->getName();
      }
      return $summaryTableHeader;
      }

      public function generateSummaryTableEntryFromRecord(FormRecord $formRecord): array
      {
      $summaryTableEntry = [];
      foreach ($this->iterateAllFieldsOrderedByPosition() as $field) {
      $summaryTableEntry[] = $field->extractCorrespondingValueFromRecord($formRecord);
      }
      return $summaryTableEntry;
      }
     * 
     */

    /**
     * 
     * @var ArrayCollection|null
     */
    protected $sortedFields;

    public function iterateAllFieldsOrderedByPosition()
    {
        if (!isset($this->sortedFields)) {
            $this->sortedFields = new ArrayCollection();
            foreach ($this->integerFields->matching($this->nonRemovedCriteria()) as $integerField) {
                $this->sortedFields->add($integerField);
            }
            foreach ($this->stringFields->matching($this->nonRemovedCriteria()) as $stringField) {
                $this->sortedFields->add($stringField);
            }
            foreach ($this->textAreaFields->matching($this->nonRemovedCriteria()) as $textAreaField) {
                $this->sortedFields->add($textAreaField);
            }
            foreach ($this->attachmentFields->matching($this->nonRemovedCriteria()) as $attachmentField) {
                $this->sortedFields->add($attachmentField);
            }
            foreach ($this->singleSelectFields->matching($this->nonRemovedCriteria()) as $singleSelectField) {
                $this->sortedFields->add($singleSelectField);
            }
            foreach ($this->multiSelectFields->matching($this->nonRemovedCriteria()) as $multiSelectField) {
                $this->sortedFields->add($multiSelectField);
            }
        }

        $criteria = Criteria::create()
                ->orderBy(['position' => Criteria::ASC, "id" => Criteria::ASC]);
        return $this->sortedFields->matching($criteria)->getIterator();
    }

    public function appendAllFieldsAsHeaderColumnOfSummaryTable(
            IContainSummaryTable $containSummaryTable, int $startColNumber): void
    {
        foreach ($this->iterateAllFieldsOrderedByPosition() as $field) {
            $containSummaryTable->addHeaderColumn(new HeaderColumn($startColNumber, $field));
            $startColNumber++;
        }
    }
    
    public function getFieldByIdOrDie(string $fieldId): ?IField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $fieldId));
        if (!empty($field = $this->integerFields->matching($criteria)->first())) {
            return  $field;
        }
        if (!empty($field = $this->stringFields->matching($criteria)->first())) {
            return  $field;
        }
        if (!empty($field = $this->textAreaFields->matching($criteria)->first())) {
            return  $field;
        }
        if (!empty($field = $this->attachmentFields->matching($criteria)->first())) {
            return  $field;
        }
        if (!empty($field = $this->singleSelectFields->matching($criteria)->first())) {
            return  $field;
        }
        if (!empty($field = $this->multiSelectFields->matching($criteria)->first())) {
            return  $field;
        }
        throw RegularException::notFound('not found: field not found');
    }

}
