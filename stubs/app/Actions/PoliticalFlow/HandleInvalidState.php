<?php

namespace App\Actions\PoliticalFlow;

use Illuminate\Http\Response;
use Laravel\Socialite\Two\InvalidStateException;
use Joinapi\PoliticalFlow\Contracts\HandlesInvalidState;

class HandleInvalidState implements HandlesInvalidState
{
    /**
     * Handle an invalid state exception from a Socialite provider.
     */
    public function handle(InvalidStateException $exception, ?callable $callback = null): Response
    {
        if ($callback) {
            return $callback($exception);
        }

        throw $exception;
    }
}
