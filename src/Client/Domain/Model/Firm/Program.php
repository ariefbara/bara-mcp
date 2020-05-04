<?php

namespace Client\Domain\Model\Firm;

use Client\Domain\Model\Firm\Program\RegistrationPhase;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Firm;

class Program
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     *
     * @var ArrayCollection
     */
    protected $registrationPhases;

    protected function __construct()
    {
        ;
    }

    public function canAcceptRegistration(): bool
    {
        return $this->published && $this->hasOpenRegistrationPhase();
    }

    protected function hasOpenRegistrationPhase(): bool
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        $p = function (RegistrationPhase $registrationPhase) {
            return $registrationPhase->isOpen();
        };
        return !empty($this->registrationPhases->matching($criteria)->filter($p)->count());
    }

}
