<?php

namespace App\Libraries;

use Illuminate\Contracts\Hashing\Hasher;

class Sha1Hasher implements Hasher
{

    public function info($hashedValue)
    {
    }

    public function check($value, $hashedValue, array $options = []): bool
    {
        return $this->make($value) === $hashedValue;
    }

    public function make($value, array $options = []): string
    {
        return hash('sha1', $value);
    }

    public function needsRehash($hashedValue, array $options = []): bool
    {
        return false;
    }
}
