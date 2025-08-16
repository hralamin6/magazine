<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\DB;

class SendWebPushNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
    public $chunkSize;

    public function __construct($payload, $chunkSize = 100)
    {
        $this->payload = $payload;
        $this->chunkSize = $chunkSize;
    }

    public function handle()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        // Chunk guest subscriptions
        DB::table('guest_subscriptions')->orderBy('id', 'desc')->chunk($this->chunkSize, function($guests) use ($webPush) {
            foreach ($guests as $guest) {
                $subscription = Subscription::create([
                    'endpoint' => $guest->endpoint,
                    'publicKey' => $guest->public_key,
                    'authToken' => $guest->auth_token,
                    'contentEncoding' => 'aesgcm',
                ]);

                $webPush->queueNotification($subscription, $this->payload);
            }

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    // Optional: remove invalid subscriptions
                    if ($report->getResponse() && $report->getResponse()->getStatusCode() === 410) {
                        DB::table('guest_subscriptions')->where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
                    }
                }
            }
        });
    }
}
