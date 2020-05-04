<?php

namespace Query\Domain\Model\Shared\Form;

use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Shared\Form\SelectField\Option;

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

    protected function __construct()
    {
        ;
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

}
