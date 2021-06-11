<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm\BioSearchFilter\IntegerFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\MultiSelectFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\SingleSelectFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\StringFieldSearchFilterData;
use Firm\Domain\Model\Firm\BioSearchFilter\TextAreaFieldSearchFilterData;
use Firm\Domain\Model\Shared\Form\IntegerField;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use Firm\Domain\Model\Shared\Form\StringField;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use SplObjectStorage;

class BioSearchFilterData
{

    /**
     * 
     * @var SplObjectStorage
     */
    protected $integerFieldStorage;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $stringFieldStorage;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $textAreaFieldStorage;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $singleSelectFieldStorage;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $multiSelectFieldStorage;

    public function __construct()
    {
        $this->integerFieldStorage = new SplObjectStorage();
        $this->stringFieldStorage = new SplObjectStorage();
        $this->textAreaFieldStorage = new SplObjectStorage();
        $this->singleSelectFieldStorage = new SplObjectStorage();
        $this->multiSelectFieldStorage = new SplObjectStorage();
    }

    public function addIntegerFieldFilter(IntegerField $integerField, int $comparisonType): void
    {
        $this->integerFieldStorage->attach($integerField, $comparisonType);
    }
    public function pullComparisonTypeCorrespondWithIntegerField(IntegerField $integerField): ?int
    {
        $comparisonType = null;
        if ($this->integerFieldStorage->contains($integerField)) {
            $comparisonType = $this->integerFieldStorage[$integerField];
            $this->integerFieldStorage->detach($integerField);
        }
        return $comparisonType;
    }
    /**
     * 
     * @return IntegerFieldSearchFilterData[]
     */
    public function getIntegerFieldsSearchFilterDataIterator(): array
    {
        $data = [];
        foreach ($this->integerFieldStorage as $integerField) {
            $data[] = new IntegerFieldSearchFilterData($integerField, $this->integerFieldStorage[$integerField]);
        }
        return $data;
    }

    public function addStringFieldFilter(StringField $stringField, int $comparisonType): void
    {
        $this->stringFieldStorage->attach($stringField, $comparisonType);
    }
    public function pullComparisonTypeCorrespondWithStringField(StringField $stringField): ?int
    {
        $comparisonType = null;
        if ($this->stringFieldStorage->contains($stringField)) {
            $comparisonType = $this->stringFieldStorage[$stringField];
            $this->stringFieldStorage->detach($stringField);
        }
        return $comparisonType;
    }
    /**
     * 
     * @return StringFieldSearchFilterData[]
     */
    public function getStringFieldsSearchFilterDataIterator(): array
    {
        $data = [];
        foreach ($this->stringFieldStorage as $stringField) {
            $data[] = new StringFieldSearchFilterData($stringField, $this->stringFieldStorage[$stringField]);
        }
        return $data;
    }

    public function addTextAreaFieldFilter(TextAreaField $textAreaField, int $comparisonType): void
    {
        $this->textAreaFieldStorage->attach($textAreaField, $comparisonType);
    }
    public function pullComparisonTypeCorrespondWithTextAreaField(TextAreaField $textAreaField): ?int
    {
        $comparisonType = null;
        if ($this->textAreaFieldStorage->contains($textAreaField)) {
            $comparisonType = $this->textAreaFieldStorage[$textAreaField];
            $this->textAreaFieldStorage->detach($textAreaField);
        }
        return $comparisonType;
    }
    /**
     * 
     * @return TextAreaFieldSearchFilterData[]
     */
    public function getTextAreaFieldsSearchFilterDataIterator(): array
    {
        $data = [];
        foreach ($this->textAreaFieldStorage as $textAreaField) {
            $data[] = new TextAreaFieldSearchFilterData($textAreaField, $this->textAreaFieldStorage[$textAreaField]);
        }
        return $data;
    }

    public function addSingleSelectFieldFilter(SingleSelectField $singleSelectField, int $comparisonType): void
    {
        $this->singleSelectFieldStorage->attach($singleSelectField, $comparisonType);
    }
    public function pullComparisonTypeCorrespondWithSingleSelectField(SingleSelectField $singleSelectField): ?int
    {
        $comparisonType = null;
        if ($this->singleSelectFieldStorage->contains($singleSelectField)) {
            $comparisonType = $this->singleSelectFieldStorage[$singleSelectField];
            $this->singleSelectFieldStorage->detach($singleSelectField);
        }
        return $comparisonType;
    }
    /**
     * 
     * @return SingleSelectFieldSearchFilterData[]
     */
    public function getSingleSelectFieldsSearchFilterDataIterator(): array
    {
        $data = [];
        foreach ($this->singleSelectFieldStorage as $singleSelectField) {
            $data[] = new SingleSelectFieldSearchFilterData($singleSelectField, $this->singleSelectFieldStorage[$singleSelectField]);
        }
        return $data;
    }

    public function addMultiSelectFieldFilter(MultiSelectField $multiSelectField, int $comparisonType): void
    {
        $this->multiSelectFieldStorage->attach($multiSelectField, $comparisonType);
    }
    public function pullComparisonTypeCorrespondWithMultiSelectField(MultiSelectField $multiSelectField): ?int
    {
        $comparisonType = null;
        if ($this->multiSelectFieldStorage->contains($multiSelectField)) {
            $comparisonType = $this->multiSelectFieldStorage[$multiSelectField];
            $this->multiSelectFieldStorage->detach($multiSelectField);
        }
        return $comparisonType;
    }
    /**
     * 
     * @return MultiSelectFieldSearchFilterData[]
     */
    public function getMultiSelectFieldsSearchFilterDataIterator(): array
    {
        $data = [];
        foreach ($this->multiSelectFieldStorage as $multiSelectField) {
            $data[] = new MultiSelectFieldSearchFilterData($multiSelectField, $this->multiSelectFieldStorage[$multiSelectField]);
        }
        return $data;
    }

}
