<?php

namespace Joinapi\PoliticalFlow\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Joinapi\PoliticalFlow\Contracts\AddsPoliticalEmployees;
use Joinapi\PoliticalFlow\PoliticalFlow;
use Livewire\Features\SupportRedirects\Redirector;

class PoliticalInvitationController extends Controller
{
    /**
     * Accept a political invitation.
     */
    public function accept(Request $request, int $invitationId): Redirector | RedirectResponse | null
    {
        $model = PoliticalFlow::politicalInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();
        $user = PoliticalFlow::userModel()::where('email', $invitation->email)->first();

        app(AddsPoliticalEmployees::class)->add(
            $invitation->political->owner,
            $invitation->political,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        $title = __('political-flow::default.banner.political_invitation_accepted', ['political' => $invitation->political->name]);
        $notification = Notification::make()->title(Str::inlineMarkdown($title))->success()->persistent()->send();

        if ($user) {
            Filament::auth()->login($user);

            return redirect(url(filament()->getHomeUrl()))->with('notification.success.political_invitation_accepted', $notification);
        }

        return redirect(url(filament()->getLoginUrl()));
    }

    /**
     * Cancel the given political invitation.
     *
     * @throws AuthorizationException
     */
    public function destroy(Request $request, int $invitationId): Redirector | RedirectResponse
    {
        $model = PoliticalFlow::politicalInvitationModel();

        $invitation = $model::whereKey($invitationId)->firstOrFail();

        if (! Gate::forUser($request->user())->check('removePoliticalEmployee', $invitation->political)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
