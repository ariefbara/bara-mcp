<?php

namespace Notification\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm;

class Team
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
    protected $id;

    /**
     *
     * @var ArrayCollection
     */
    protected $members;

}
