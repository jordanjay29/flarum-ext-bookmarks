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

use Flarum\Event\ConfigureDiscussionGambits;
use Flarum\Event\ConfigureDiscussionSearch;
use Flarum\Event\ConfigureForumRoutes;
use Jordanjay29\Bookmarks\Gambit\SubscriptionGambit;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Query\Expression;

class FilterDiscussionListBySubscription
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureDiscussionGambits::class, [$this, 'addGambit']);
        $events->listen(ConfigureDiscussionSearch::class, [$this, 'filterIgnored']);
        $events->listen(ConfigureForumRoutes::class, [$this, 'addRoutes']);
    }

    /**
     * @param ConfigureDiscussionGambits $event
     */
    public function addGambit(ConfigureDiscussionGambits $event)
    {
        $event->gambits->add(SubscriptionGambit::class);
    }

    /**
     * @param ConfigureDiscussionSearch $event
     */
    public function filterIgnored(ConfigureDiscussionSearch $event)
    {
        if (! $event->criteria->query) {
            // might be better as `id IN (subquery)`?
            $actor = $event->search->getActor();
            $event->search->getQuery()->whereNotExists(function ($query) use ($actor) {
                $query->selectRaw(1)
                      ->from('users_discussions')
                      ->where('discussions.id', new Expression('discussion_id'))
                      ->where('user_id', $actor->id)
                      ->where('subscription', 'ignore');
            });

            $search = $event->search;
            $query = $search->getQuery();
            $query->leftJoin('users_discussions', function ($join) use ($search) {
                $join->on('users_discussions.discussion_id', '=', 'discussions.id')
                     ->where('discussions.is_sticky', '=', true)
                     ->where('users_discussions.subscription', '=', 'bookmark');
            });

//            if (! is_array($query->orders)) {
//               $query->orders = [];
//           }

//            array_unshift($query->orders, [
//                'type' => 'raw',
//                'sql' => "(is_sticky OR (discussions.subscription == 'bookmark')) desc"
//            ]);

            dd($query->toSql());
        }
    }

    /**
     * @param ConfigureForumRoutes $event
     */
    public function addRoutes(ConfigureForumRoutes $event)
    {
        $event->get('/following', 'following');
    }
}
