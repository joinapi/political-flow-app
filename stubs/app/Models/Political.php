<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Joinapi\PoliticalFlow\Events\PoliticalCreated;
use Joinapi\PoliticalFlow\Events\PoliticalDeleted;
use Joinapi\PoliticalFlow\Events\PoliticalUpdated;
use Joinapi\PoliticalFlow\Political as PoliticalFlowPolitical;

class Political extends PoliticalFlowPolitical implements HasAvatar
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_portal',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => PoliticalCreated::class,
        'updated' => PoliticalUpdated::class,
        'deleted' => PoliticalDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_portal' => 'boolean',
        ];
    }

    public function getFilamentAvatarUrl(): string
    {
        return $this->owner->profile_photo_url;
    }
}
