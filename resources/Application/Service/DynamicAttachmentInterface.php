<?php

namespace Resources\Application\Service;

interface DynamicAttachmentInterface
{

    public function getFileName(): string;

    public function getContent(): string;

    public function getContentType(): ?string;
}
