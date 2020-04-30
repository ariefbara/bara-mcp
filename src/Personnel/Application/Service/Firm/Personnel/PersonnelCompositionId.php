<?php

namespace Personnel\Application\Service\Firm\Personnel;

class PersonnelCompositionId
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $personnelId;

    function __construct(string $firmId, string $personnelId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
    }

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getPersonnelId(): string
    {
        return $this->personnelId;
    }

}
