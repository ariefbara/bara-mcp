<?php

namespace Notification\Domain\Model;

interface HasDynamicLinkInMessageInterface extends CanBePersonalizeMailInterface
{
    public function prependApiPath(string $apiPath): void;
}
