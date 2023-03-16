<?php

namespace Participant\Domain\Model\Participant;

class LearningProgressData
{

    public $id;

    /**
     * 
     * @var string|null
     */
    protected $progressMark;

    /**
     * 
     * @var bool|null
     */
    protected $markAsCompleted;

    public function getProgressMark(): ?string
    {
        return $this->progressMark;
    }

    public function getMarkAsCompleted(): ?bool
    {
        return $this->markAsCompleted;
    }

    public function setProgressMark(?string $progressMark)
    {
        $this->progressMark = $progressMark;
        return $this;
    }

    public function setMarkAsCompleted(?bool $markAsCompleted)
    {
        $this->markAsCompleted = $markAsCompleted;
        return $this;
    }

}
