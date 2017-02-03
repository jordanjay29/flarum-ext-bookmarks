import { extend } from 'flarum/extend';
import app from 'flarum/app';
import Model from 'flarum/Model';
import Discussion from 'flarum/models/Discussion';
import NotificationGrid from 'flarum/components/NotificationGrid';

import addSubscriptionBadge from 'jordanjay29/bookmarks/addSubscriptionBadge';
import addSubscriptionControls from 'jordanjay29/bookmarks/addSubscriptionControls';
import addSubscriptionFilter from 'jordanjay29/bookmarks/addSubscriptionFilter';
import addSubscriptionSettings from 'jordanjay29/bookmarks/addSubscriptionSettings';

import NewPostNotification from 'jordanjay29/bookmarks/components/NewPostNotification';

app.initializers.add('subscriptions', function() {
  app.notificationComponents.newPost = NewPostNotification;

  Discussion.prototype.subscription = Model.attribute('subscription');

  addSubscriptionBadge();
  addSubscriptionControls();
  addSubscriptionFilter();
  addSubscriptionSettings();

  extend(NotificationGrid.prototype, 'notificationTypes', function(items) {
    items.add('newPost', {
      name: 'newPost',
      icon: 'star',
      label: app.translator.trans('flarum-subscriptions.forum.settings.notify_new_post_label')
    });
  });
});
