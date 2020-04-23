<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form\SelectField\OptionData;
use Resources\Domain\Data\DataCollection;

class SelectFieldData
{

    /**
     *
     * @var FieldData
     */
    protected $fieldData;

    /**
     *
     * @var DataCollection
     */
    protected $optionDataCollection;

    function getFieldData(): FieldData
    {
        return $this->fieldData;
    }

    function getOptionDataCollection(): DataCollection
    {
        return $this->optionDataCollection;
    }

    function __construct(FieldData $fieldData)
    {
        $this->fieldData = $fieldData;
        $this->optionDataCollection = new DataCollection();
    }

    public function pushOptionData(OptionData $optionData, ?string $optionId): void
    {
        $this->optionDataCollection->push($optionData, $optionId);
    }

    public function pullOptiondataOfId(string $optionId): ?OptionData
    {
        return $this->optionDataCollection->pull($optionId);
    }

}
