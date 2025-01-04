<?php

namespace Joinapi\PoliticalFlow\Mail;

use App\Models\PoliticalInvitation as PoliticalInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Joinapi\PoliticalFlow\PoliticalFlow;

class PoliticalInvitation extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The political invitation instance.
     */
    public PoliticalInvitationModel $invitation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PoliticalInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $acceptUrl = PoliticalFlow::generateAcceptInvitationUrl($this->invitation);

        return $this->markdown('political-flow::mail.political-invitation', compact('acceptUrl'))
            ->subject(__('Political Invitation'));
    }
}
