<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecordData;

class MultiSelectFieldRecordData
{

    /**
     *
     * @var string
     */
    protected $multiSelectFieldId;

    /**
     *
     * @var array
     */
    protected $selectedOptionIds = [];

    function getMultiSelectFieldId(): string
    {
        return $this->multiSelectFieldId;
    }

    function getSelectedOptionIds(): array
    {
        return $this->selectedOptionIds;
    }

    function __construct(string $multiSelectFieldId)
    {
        $this->multiSelectFieldId = $multiSelectFieldId;
        $this->selectedOptionIds = [];
    }

    public function add(string $selectedOptionId): void
    {
        if (!in_array($selectedOptionId, $this->selectedOptionIds)) {
            $this->selectedOptionIds[] = $selectedOptionId;
        }
    }

}
