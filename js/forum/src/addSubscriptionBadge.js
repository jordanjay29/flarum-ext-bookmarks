import { extend } from 'flarum/extend';
import Discussion from 'flarum/models/Discussion';
import Badge from 'flarum/components/Badge';

export default function addSubscriptionBadge() {
  extend(Discussion.prototype, 'badges', function(badges) {
    let badge;

    switch (this.subscription()) {
      case 'follow':
        badge = Badge.component({
          label: app.translator.trans('flarum-subscriptions.forum.badge.following_tooltip'),
          icon: 'star',
          type: 'following'
        });
        break;

      case 'bookmark':
        badge = Badge.component({
          label: app.translator.trans('flarum-bookmarks.forum.discussion_controls.bookmark_button'),
          icon: 'bookmark',
          type: 'bookmark',
        });
        break;

      case 'ignore':
        badge = Badge.component({
          label: app.translator.trans('flarum-subscriptions.forum.badge.ignoring_tooltip'),
          icon: 'eye-slash',
          type: 'ignoring'
        });
        break;

      default:
        // no default
    }

    if (badge) {
      badges.add('subscription', badge);
    }
  });
}
