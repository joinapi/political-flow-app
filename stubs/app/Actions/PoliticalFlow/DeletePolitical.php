<?php

namespace App\Actions\PoliticalFlow;

use App\Models\Political;
use Joinapi\PoliticalFlow\Contracts\DeletesPolitical;

class DeletePolitical implements DeletesPolitical
{
    /**
     * Delete the given political.
     */
    public function delete(Political $political): void
    {
        $political->purge();
    }
}
