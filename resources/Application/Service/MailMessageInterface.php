<?php

namespace Resources\Application\Service;

interface MailMessageInterface
{

    public function getSubject(): string;

    public function getTextMessage(): string;

    public function getHtmlMessage(): string;
}
