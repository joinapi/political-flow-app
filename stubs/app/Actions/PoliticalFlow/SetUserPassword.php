<?php

namespace App\Actions\PoliticalFlow;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Joinapi\PoliticalFlow\Contracts\SetsUserPasswords;

class SetUserPassword implements SetsUserPasswords
{
    /**
     * Validate and update the user's password.
     */
    public function set(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validateWithBag('setPassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
