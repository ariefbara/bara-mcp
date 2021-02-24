<?php

namespace Bara\Domain\Model;

use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;

class WorksheetForm implements GlobalAsset
{

    protected $firmId = null;

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

    function __construct(string $id, FormData $formData)
    {
        $this->firmId = null;
        $this->id = $id;
        $this->form = new Form($id, $formData);
        $this->removed = false;
    }

    public function isGlobalAsset(): bool
    {
        return empty($this->firmId);
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
