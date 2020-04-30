<?php

namespace Client\Application\Listener;

use Resources\Application\Event\Event;

interface ConsultantCommentNotificationEventInterface extends Event
{
    public function getFirmId(): string;
    public function getPersonnelId(): string;
    public function getConsultantId(): string;
    public function getConsultantCommentId(): string;
    public function getMessageForParticipant(): string;
}
