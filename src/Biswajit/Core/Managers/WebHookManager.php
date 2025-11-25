<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Tasks\AsynTasks\DiscordWebhookSendTask;
use pocketmine\Server;

class WebHookManager
{
    public static function sendWebhook(string $url, string $title, string $description, string $username, string $color = '00ff00', ?string $thumbnail = null, ?string $footer = null, ?array $fields = null, ?string $avatar = null): void
    {
        if (!is_string($url) || !str_starts_with($url, "https://")) {
            Server::getInstance()->getLogger()->warning("[WebhookManager] Invalid webhook URL: '$url'");
            return;
        }

        $payload = [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'username' => $username,
            'color' => hexdec($color),
            'avatar' => $avatar ?? "https://i.imgur.com/jjwlRAI.png"
        ];

        if ($thumbnail !== null) {
            $payload['thumbnail'] = $thumbnail;
        }

        if ($footer !== null) {
            $payload['footer'] = $footer;
        }

        if ($fields !== null) {
            $payload['fields'] = $fields;
        }

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($payloadJson === false) {
            Server::getInstance()->getLogger()->warning("[WebhookManager] Failed to encode webhook payload");
            return;
        }

        Server::getInstance()->getAsyncPool()->submitTask(new DiscordWebhookSendTask($payloadJson));
    }
}
