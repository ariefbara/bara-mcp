<?php

namespace Personnel\Domain\Model\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

interface ContainMentorReport
{
    public function processReport(Form $form, FormRecordData $formRecordData, ?int $participantRating): void;
}
