<?php

namespace Notifier\Application\Service;

interface MailAttachmentInterface
{
    public function getFileName(): string;

    public function getContent(): string;

    public function getContentType(): ?string;
}
