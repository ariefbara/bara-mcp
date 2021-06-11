<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\MultiSelectFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\SingleSelectFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\StringFieldSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilter\TextAreaFieldSearchFilter;
use Resources\DateTimeImmutableBuilder;
use Resources\Uuid;

class BioSearchFilter
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
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var ArrayCollection
     */
    protected $integerFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $stringFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $textAreaFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $singleSelectFieldSearchFilters;

    /**
     * 
     * @var ArrayCollection
     */
    protected $multiSelectFieldSearchFilters;
    
    public function __construct(Firm $firm, string $id, BioSearchFilterData $bioSearchFilterData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->disabled = false;
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->integerFieldSearchFilters = new ArrayCollection();
        $this->stringFieldSearchFilters = new ArrayCollection();
        $this->textAreaFieldSearchFilters = new ArrayCollection();
        $this->singleSelectFieldSearchFilters = new ArrayCollection();
        $this->multiSelectFieldSearchFilters = new ArrayCollection();
        
        $this->addFieldSearchFilters($bioSearchFilterData);
    }
    protected function addFieldSearchFilters(BioSearchFilterData $bioSearchFilterData): void
    {
        foreach ($bioSearchFilterData->getIntegerFieldsSearchFilterDataIterator() as $integerFieldSearchFilterData) {
            $integerFieldSearchFilter = new IntegerFieldSearchFilter(
                    $this, Uuid::generateUuid4(), $integerFieldSearchFilterData);
            $this->integerFieldSearchFilters->add($integerFieldSearchFilter);
        }
        foreach ($bioSearchFilterData->getStringFieldsSearchFilterDataIterator() as $stringFieldSearchFilterData) {
            $stringFieldSearchFilter = new StringFieldSearchFilter(
                    $this, Uuid::generateUuid4(), $stringFieldSearchFilterData);
            $this->stringFieldSearchFilters->add($stringFieldSearchFilter);
        }
        foreach ($bioSearchFilterData->getTextAreaFieldsSearchFilterDataIterator() as $textAreaFieldSearchFilterData) {
            $textAreaFieldSearchFilter = new TextAreaFieldSearchFilter(
                    $this, Uuid::generateUuid4(), $textAreaFieldSearchFilterData);
            $this->textAreaFieldSearchFilters->add($textAreaFieldSearchFilter);
        }
        foreach ($bioSearchFilterData->getSingleSelectFieldsSearchFilterDataIterator() as $singleSelectFieldSearchFilterData) {
            $singleSelectFieldSearchFilter = new SingleSelectFieldSearchFilter(
                    $this, Uuid::generateUuid4(), $singleSelectFieldSearchFilterData);
            $this->singleSelectFieldSearchFilters->add($singleSelectFieldSearchFilter);
        }
        foreach ($bioSearchFilterData->getMultiSelectFieldsSearchFilterDataIterator() as $multiSelectFieldSearchFilterData) {
            $multiSelectFieldSearchFilter = new MultiSelectFieldSearchFilter(
                    $this, Uuid::generateUuid4(), $multiSelectFieldSearchFilterData);
            $this->multiSelectFieldSearchFilters->add($multiSelectFieldSearchFilter);
        }
    }
    
    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        foreach ($this->integerFieldSearchFilters->getIterator() as $integerFieldSearchFilter) {
            $integerFieldSearchFilter->update($bioSearchFilterData);
        }
        foreach ($this->stringFieldSearchFilters->getIterator() as $stringFieldSearchFilter) {
            $stringFieldSearchFilter->update($bioSearchFilterData);
        }
        foreach ($this->textAreaFieldSearchFilters->getIterator() as $textAreaFieldSearchFilter) {
            $textAreaFieldSearchFilter->update($bioSearchFilterData);
        }
        foreach ($this->singleSelectFieldSearchFilters->getIterator() as $singleSelectFieldSearchFilter) {
            $singleSelectFieldSearchFilter->update($bioSearchFilterData);
        }
        foreach ($this->multiSelectFieldSearchFilters->getIterator() as $multiSelectFieldSearchFilter) {
            $multiSelectFieldSearchFilter->update($bioSearchFilterData);
        }
        
        $this->addFieldSearchFilters($bioSearchFilterData);
    }
    
    public function disable(): void
    {
        $this->disabled = true;
    }
    
    public function enable(): void
    {
        $this->disabled = false;
    }


}
