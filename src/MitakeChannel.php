<?php

namespace TaiwanSms\Mitake;

use Illuminate\Notifications\Notification;
use Psr\Http\Client\ClientExceptionInterface;

class MitakeChannel
{
    /**
     * @var Client
     */
    private $client;

    /**
     * __construct.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return array|void
     * @throws ClientExceptionInterface
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('Mitake')) {
            return;
        }

        $message = $notification->toMitake($notifiable);

        if (is_string($message)) {
            $message = new MitakeMessage($message);
        }

        return $this->client->send([
            'to' => $to,
            'text' => trim($message->content),
        ]);
    }
}
