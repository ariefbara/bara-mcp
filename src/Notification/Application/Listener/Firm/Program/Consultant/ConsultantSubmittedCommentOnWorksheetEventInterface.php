<?php

namespace Notification\Application\Listener\Firm\Program\Consultant;

use Resources\Application\Event\Event;

interface ConsultantSubmittedCommentOnWorksheetEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getProgramConsultationId(): string;

    public function getConsultantCommentId(): string;
}
