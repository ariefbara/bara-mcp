<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    AssetBelongsToFirm,
    Firm,
    Firm\Program\ActivityType,
    Shared\Form,
    Shared\FormData
};

class FeedbackForm implements AssetBelongsToFirm
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
    
    public function belongsToSameFirmAs(ActivityType $activityType): bool
    {
        return $activityType->belongsToFirm($this->firm);
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

}
