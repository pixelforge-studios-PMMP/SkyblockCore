<?php

namespace Biswajit\Core\Tasks\AsynTasks;

use Biswajit\Core\API;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Skyblock;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class loadDataTask extends AsyncTask
{
    private const TIMEOUT = 10;
    private const VALID_TYPES = ['hub', 'island', 'pack'];

    public function __construct(
        private string $url,
        private string $targetPath,
        private string $type
    ) {
        if (!in_array($this->type, self::VALID_TYPES)) {
            throw new \InvalidArgumentException("Invalid type: {$this->type}");
        }
    }

    public function onRun(): void
    {
        try {
            $data = Internet::getURL($this->url, self::TIMEOUT, [], $err);
            if ($data === null || $err !== null) {
                throw new \RuntimeException("Download failed: " . ($err ?? 'Unknown error'));
            }
            $this->setResult($data->getBody());
        } catch (\Throwable $e) {
            $this->setResult(['error' => $e->getMessage()]);
        }
    }

    public function onCompletion(): void
    {
        $result = $this->getResult();

        if (is_array($result) && isset($result['error'])) {
            Server::getInstance()->getLogger()->error("Failed to download file: " . $result['error']);
            return;
        }

        try {
            if (!file_put_contents($this->targetPath, $result)) {
                throw new \RuntimeException("Failed to write file to: {$this->targetPath}");
            }

            $loaders = $this->getLoaders();

            if (!empty($loaders)) {
                Skyblock::getInstance()->getScheduler()->scheduleTask(
                    new ClosureTask(
                        fn () => array_map(fn ($loader) => $loader(), $loaders)
                    ),
                    120
                );
            }
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->error($e->getMessage());
        }
    }

    private function getLoaders(): array
    {
        return match($this->type) {
            'hub' => [
                fn () => API::loadHub(),
                fn () => API::setHubTime()
            ],
            'island' => [
                fn () => IslandManager::loadIslands()
            ],
            'pack' => [
                fn () => API::applyResourcePack()
            ],
            default => []
        };
    }
}
