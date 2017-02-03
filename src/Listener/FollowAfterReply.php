<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jordanjay29\Bookmarks\Listener;

use Flarum\Core\Access\AssertPermissionTrait;
use Flarum\Event\ConfigureUserPreferences;
use Flarum\Event\PostWasPosted;
use Illuminate\Contracts\Events\Dispatcher;

class FollowAfterReply
{
    use AssertPermissionTrait;

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureUserPreferences::class, [$this, 'addUserPreference']);
        $events->listen(PostWasPosted::class, [$this, 'whenPostWasPosted']);
    }

    /**
     * @param ConfigureUserPreferences $event
     */
    public function addUserPreference(ConfigureUserPreferences $event)
    {
        $event->add('followAfterReply', 'boolval', false);
    }

    /**
     * @param PostWasPosted $event
     */
    public function whenPostWasPosted(PostWasPosted $event)
    {
        $actor = $event->actor;

        if ($actor && $actor->exists && $actor->getPreference('followAfterReply')) {
            $this->assertRegistered($actor);

            $state = $event->post->discussion->stateFor($actor);

            if ($state->subscription != 'bookmark') {
                $state->subscription = 'follow';
            }
            $state->save();
        }
    }
}
