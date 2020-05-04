<?php

namespace Query\Application\Service\Firm\Personnel;

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

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getPersonnelId(): string
    {
        return $this->personnelId;
    }

    function __construct(string $firmId, string $personnelId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
    }

}
