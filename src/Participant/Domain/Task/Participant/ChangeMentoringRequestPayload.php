<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant\MentoringRequestData;

class ChangeMentoringRequestPayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var MentoringRequestData|null
     */
    protected $mentoringRequestData;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMentoringRequestData(): ?MentoringRequestData
    {
        return $this->mentoringRequestData;
    }

    public function __construct(?string $id, ?MentoringRequestData $mentoringRequestData)
    {
        $this->id = $id;
        $this->mentoringRequestData = $mentoringRequestData;
    }

}
