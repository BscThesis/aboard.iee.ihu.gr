<?php

namespace App\Listeners\V2;

use App\Events\V2\NewAnnouncementWasCreatedEvent;
use App\Services\FcmClient;

class SendNotificationsToFirebaseTopicListener
{
    /**
     * Handle the event.
     *
     * @param NewAnnouncementWasCreatedEvent $event
     * @return void
     */

    public function handle(NewAnnouncementWasCreatedEvent $event)
    {
        $fcm = app(FcmClient::class);

        if (!$fcm->enabled()) {
            return;
        }

        try {
            $tags = $event->announcement->tags->pluck('id');
            $payload = [
                'type' => 'announcement_created',
                'title' => (string)$event->announcement->title,
                'announcement_id' => (string)$event->announcement->id,
            ];

            foreach ($tags as $tagId) {
                $fcm->sendToTopic($fcm->topicName($tagId), $payload);
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
