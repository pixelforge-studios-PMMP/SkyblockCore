<?php

declare(strict_types=1);

namespace Biswajit\Core;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

trait Database
{
    private DataConnector $dataBase;

    public function initDatabase(): void
    {
        $db = libasynql::create($this, $this->getConfig()->get('database'), ['mysql' => 'mysql.sql', 'sqlite' => 'sqlite.sql']);
        $db->executeGeneric('skyblockIsland.init');
        $db->executeGeneric('skyblock.init');
        $db->executeGeneric('economy.init');
        $db->executeGeneric('rank.init');
        $db->executeGeneric('area.init');
        $db->waitAll();
        $this->dataBase = $db;
    }

    /**
     * @return DataConnector
     */
    public function getDataBase(): DataConnector
    {
        return $this->dataBase;
    }

}
