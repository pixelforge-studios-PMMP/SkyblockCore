<?php

declare(strict_types=1);

namespace Biswajit\Core\Tasks\AsynTasks;

use pocketmine\scheduler\AsyncTask;

class DiscordWebhookSendTask extends AsyncTask
{
    private string $payloadJson;
    private const TIMEOUT = 10;

    public function __construct(string $payloadJson)
    {
        $this->payloadJson = $payloadJson;
    }

    public function onRun(): void
    {
        $payload = json_decode($this->payloadJson, true);

        if (!is_array($payload)) {
            $this->setResult([500, "Invalid payload format"]);
            return;
        }

        $requiredFields = ['url', 'username', 'title'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                $this->setResult([400, "Missing required field: {$field}"]);
                return;
            }
        }

        $embed = [
            "title" => $payload["title"],
            "description" => $payload["description"] ?? "",
            "color" => $payload["color"] ?? 0x00ff00,
            "timestamp" => date("c"),
            "footer" => [
                "text" => $payload["footer"] ?? "Powered by SkyblockCore"
            ]
        ];

        if (isset($payload["fields"])) {
            $embed["fields"] = $payload["fields"];
        }

        if (isset($payload["thumbnail"])) {
            $embed["thumbnail"] = ["url" => $payload["thumbnail"]];
        }

        $ch = curl_init($payload["url"]);
        curl_setopt_array($ch, [
            CURLOPT_POSTFIELDS => json_encode([
                "username" => $payload["username"],
                "avatar_url" => $payload["avatar"] ?? null,
                "embeds" => [$embed]
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "User-Agent: SkyblockCore/1.0"
            ]
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if ($error = curl_error($ch)) {
            $this->setResult([500, "CURL Error: " . $error]);
        } else {
            $this->setResult([$code, $response]);
        }

        curl_close($ch);
    }
}
