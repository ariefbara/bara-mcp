<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;

class ProfileForm implements AssetBelongsToFirm
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
    
    function __construct(Firm $firm, string $id, FormData $formData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->form = new Form($id, $formData);
    }
    
    public function update(FormData $formData): void
    {
        $this->form->update($formData);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

}
