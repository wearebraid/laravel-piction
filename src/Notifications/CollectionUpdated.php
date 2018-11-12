<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CollectionUpdated extends Notification
{
    use Queueable;

    protected $totalRecords;
    protected $collection;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($collection, $totalRecords)
    {
        $this->collection = $collection;
        $this->totalRecords = $totalRecords;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from(env('PICTION_SLACK_USER', 'Piction API'), env('PICTION_SLACK_ICON', ':gear:'))
            ->to(env('PICTION_SLACK_CHANNEL', '#general'))
            ->content($this->collection->title . ': '.$this->totalRecords.' have been added or updated!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
