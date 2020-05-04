<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Shared\ {
    Form\MultiSelectField,
    FormRecord,
    FormRecord\MultiSelectFieldRecord\SelectedOption
};

class MultiSelectFieldRecord
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
        ;
    }

}
