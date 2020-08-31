<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\Form\SelectField\Option;

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
    protected $field;

    /**
     *
     * @var ArrayCollection
     */
    protected $options;

    public function getName(): string
    {
        return $this->field->getName();
    }

    protected function __construct()
    {
        ;
    }

    public function getOptionOrDie($optionId): Option
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $optionId))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $option = $this->options->matching($criteria)->first();
        if (empty($option)) {
            $errorDetail = 'not found: option not found';
            throw RegularException::notFound($errorDetail);
        }
        return $option;
    }

    public function assertMandatoryRequirementSatisfied($value): void
    {
        $this->field->assertMandatoryRequirementSatisfied($value);
    }

}
