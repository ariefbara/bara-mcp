<?php

namespace Notification\Domain\SharedModel;

interface UrlCanBeModifiedMailInterface
{
    public function prependUrlPath(string $urlPath): void;
}
