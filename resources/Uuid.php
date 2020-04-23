<?php

namespace Resources;

class Uuid {
    public static function generateUuid4(): string {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
