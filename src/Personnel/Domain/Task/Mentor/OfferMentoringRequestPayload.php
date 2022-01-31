<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;

class OfferMentoringRequestPayload
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
