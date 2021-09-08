<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\IField;
use Query\Domain\Model\Shared\Form;
use Query\Domain\Model\Shared\FormRecord;
use Resources\Domain\ValueObject\IntegerRange;

class TextAreaField implements IField
{

    /**
     *
     * @var Form
     */
    protected $form;

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
     * @var IntegerRange
     */
    protected $minMaxValue;

    /**
     *
     * @var string||null
     */
    protected $placeholder = null;

    /**
     *
     * @var string||null
     */
    protected $defaultValue = null;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getForm(): Form
    {
        return $this->form;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

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

    function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }
    
    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

    public function extractCorrespondingValueFromRecord(FormRecord $formRecord)
    {
        return $formRecord->getTextAreaFieldRecordValueCorrespondWith($this);
    }

    public function getCorrespondingValueFromRecord(IContainFieldRecord $containFieldRecord)
    {
        return $containFieldRecord->getTextAreaFieldRecordValueCorrespondWith($this);
    }

    public function getLabel(): string
    {
        return $this->getName();
    }

}
