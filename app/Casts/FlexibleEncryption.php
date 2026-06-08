<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class FlexibleEncryption implements CastsAttributes
{
    /**
     * Cast the given value (Decrypt on retrieval)
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        try {
            // Attempt to decrypt
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            // If decryption fails, it's likely old unencrypted data
            // Return it as is to avoid breaking the UI
            return $value;
        }
    }

    /**
     * Prepare the given value for storage (Encrypt on save)
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Always encrypt on save
        return Crypt::encryptString((string) $value);
    }
}
