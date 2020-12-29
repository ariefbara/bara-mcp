<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;

class WorksheetForm implements AssetBelongsToFirm
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
    protected $removed = false;

    function __construct(Firm $firm, string $id, Form $form)
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

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

}
