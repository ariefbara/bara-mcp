<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    Firm,
    Shared\Form,
    Shared\FormData
};

class FeedbackForm
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
     * @var Form
     */
    protected $form;

    /**
     *
     * @var bool
     */
    protected $removed;

    function __construct(Firm $firm, $id, Form $form)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->form = $form;
        $this->removed = false;
    }

    public function update(FormData $formData): void
    {
        $this->form->update($formData);
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
