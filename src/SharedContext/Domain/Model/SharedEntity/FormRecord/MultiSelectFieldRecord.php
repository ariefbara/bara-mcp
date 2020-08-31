<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\ {
    Form\MultiSelectField,
    Form\SelectField\Option,
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

    function getMultiSelectField(): MultiSelectField
    {
        return $this->multiSelectField;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * 
     * @param FormRecord $formRecord
     * @param string $id
     * @param MultiSelectField $multiSelectField
     * @param Option[] $options
     */
    public function __construct(
            FormRecord $formRecord, string $id, MultiSelectField $multiSelectField, array $options)
    {
        $this->formRecord = $formRecord;
        $this->id = $id;
        $this->multiSelectField = $multiSelectField;
        $this->removed = false;

        $this->selectedOptions = new ArrayCollection();
        foreach ($options as $option) {
            $this->selectedOptions->add(new SelectedOption($this, Uuid::generateUuid4(), $option));
        }
    }

    /**
     *
     * @param Option[] $options
     */
    public function setSelectedOptions(array $options)
    {
        foreach ($options as $option) {
            $criteria = Criteria::create()
                    ->andWhere(Criteria::expr()->eq('option', $option))
                    ->andWhere(Criteria::expr()->eq('removed', false));
            if (empty($this->selectedOptions->matching($criteria)->count())) {
                $this->selectedOptions->add(new SelectedOption($this, Uuid::generateUuid4(), $option));
            }
        }
        $excludeCriteria = Criteria::create()->andWhere(Criteria::expr()->notIn('option', $options))
                ->andWhere(Criteria::expr()->eq('removed', false));
        foreach ($this->selectedOptions->matching($excludeCriteria)->getIterator() as $selectedOption) {
            $selectedOption->remove();
        }
    }

    public function isReferToRemovedField(): bool
    {
        return $this->multiSelectField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        foreach ($this->selectedOptions->matching($criteria)->getIterator() as $selectedOption) {
            $selectedOption->remove();
        }
    }

}
