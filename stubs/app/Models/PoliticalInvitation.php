<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Joinapi\PoliticalFlow\PoliticalFlow;

class PoliticalInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * Get the political that the invitation belongs to.
     */
    public function political(): BelongsTo
    {
        return $this->belongsTo(PoliticalFlow::politicalModel());
    }
}
