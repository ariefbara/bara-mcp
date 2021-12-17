<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord\SelectedOption;
use Query\Domain\Model\Shared\IFieldRecord;

class MultiSelectFieldRecord implements IFieldRecord
{

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MultiSelectField
     */
    protected $multiSelectField;

    /**
     *
     * @var ArrayCollection
     */
    protected $selectedOptions;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getMultiSelectField(): MultiSelectField
    {
        return $this->multiSelectField;
    }

    function getId(): string
    {
        return $this->id;
    }

    /**
     * 
     * @return SelectedOption[]
     */
    function getUnremovedSelectedOptions()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->selectedOptions->matching($criteria)->getIterator();
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
    }
    
    public function isActiveFieldRecordCorrespondWith(MultiSelectField $multiSelectField): bool
    {
        return !$this->removed && $this->multiSelectField === $multiSelectField;
    }
    
    public function getStringOfSelectedOptionNameList(): ?string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        
        $result = null;
        foreach ($this->selectedOptions->matching($criteria)->getIterator() as $selectedOption) {
            $result .= empty($result)? $selectedOption->getOptionName() : "\r\n{$selectedOption->getOptionName()}";
        }
        return $result;
    }

    public function correspondWithFieldName(string $fieldName): bool
    {
        return $this->multiSelectField->getName() === $fieldName;
    }

    public function getValue()
    {
        return $this->getStringOfSelectedOptionNameList();
    }

}
