<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;
use Resources\Exception\RegularException;

class BioForm implements AssetBelongsToFirm
{

    /**
     * 
     * @var Firm
     */
    protected $firm;

    /**
     * 
     * @var Form
     */
    protected $form;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Firm $firm, string $id, FormData $formData)
    {
        $this->firm = $firm;
        $this->form = new Form($id, $formData);
        $this->disabled = false;
    }
    protected function assertEnabled(): void
    {
        if ($this->disabled) {
            $errorDetail = "forbidden: this request only valid on enabled bio form";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
    public function update(FormData $formData): void
    {
        $this->assertEnabled();
        $this->form->update($formData);
    }

    public function disable(): void
    {
        $this->assertEnabled();
        $this->disabled = true;
    }

    public function enable(): void
    {
        if (!$this->disabled) {
            $errorDetail = "forbidden: bio form already enabled";
            throw RegularException::forbidden($errorDetail);
        }
        $this->disabled = false;
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

}
