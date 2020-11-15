<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;

class FeedbackForm
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
     * @var Form
     */
    protected $form;

    /**
     *
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }

}
