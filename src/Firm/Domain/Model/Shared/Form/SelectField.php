<?php

namespace Firm\Domain\Model\Shared\Form;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Firm\Domain\Model\Shared\Form\SelectField\Option;
use Resources\Uuid;

class SelectField
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FieldVO
     */
    protected $fieldVO;

    /**
     *
     * @var ArrayCollection
     */
    protected $options;
    
    function getId(): string
    {
        return $this->id;
    }

    /**
     * 
     * @return Option[]
     */
    function getUnremovedOptions()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->options->matching($criteria)->getIterator();
    }
    
    function __construct(string $id, SelectFieldData $selectFieldData)
    {
        $this->id = $id;
        $this->fieldVO = new FieldVO($selectFieldData->getFieldData());

        $this->options = new ArrayCollection();
        $this->addOptions($selectFieldData);
    }

    public function update(SelectFieldData $selectFieldData): void
    {
        $this->fieldVO = new FieldVO($selectFieldData->getFieldData());

        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        foreach ($this->options->matching($criteria)->getIterator() as $option) {
            $optionData = $selectFieldData->pullOptiondataOfId($option->getId());
            if ($optionData == null) {
                $option->remove();
            } else {
                $option->update($optionData);
            }
        }
        $this->addOptions($selectFieldData);
    }

    protected function addOptions(SelectFieldData $selectFieldData)
    {
        foreach ($selectFieldData->getOptionDataCollection() as $optionData) {
            $id = Uuid::generateUuid4();
            $option = new Option($this, $id, $optionData);
            $this->options->add($option);
        }
    }
    
    function getName(): string
    {
        return $this->fieldVO->getName();
    }

    function getDescription(): ?string
    {
        return $this->fieldVO->getDescription();
    }

    function getPosition(): ?string
    {
        return $this->fieldVO->getPosition();
    }

    function isMandatory(): bool
    {
        return $this->fieldVO->isMandatory();
    }

}
