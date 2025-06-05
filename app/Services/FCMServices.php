<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FCMServices
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/presensi-dinkop-firebase-adminsdk-fbsvc-4dd6ffd60f.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendToDevice(string $token, string $title, string $body): void
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body))
            ->withData([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'sound' => 'default',
            ]);

        $this->messaging->send($message);
    }
}
