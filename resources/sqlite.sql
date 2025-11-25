-- #! sqlite
-- #{ skyblockIsland

-- # { init
CREATE TABLE IF NOT EXISTS skyblockIsland (
    player TEXT PRIMARY KEY,
    data TEXT NOT NULL
);
-- # }

-- # { getIslandData
-- #   :player string
SELECT data FROM skyblockIsland WHERE player=:player;
-- # }

-- # { saveIslandData
-- #   :player string
-- #   :data string
INSERT OR REPLACE INTO skyblockIsland (player, data) VALUES (:player, :data);
-- # }

-- # { deleteIslandData
-- #   :player string
DELETE FROM skyblockIsland WHERE player=:player;
-- # }

-- # }

-- #{ skyblock

-- # { init
CREATE TABLE IF NOT EXISTS skyblock (
    uuid TEXT PRIMARY KEY,
    data TEXT NOT NULL
);
-- # }

-- # { load
-- #   :uuid string
SELECT data FROM skyblock WHERE uuid=:uuid;
-- # }

-- # { create
-- #   :uuid string
-- #   :data string
INSERT INTO skyblock (uuid, data) VALUES (:uuid, :data);
-- # }

-- # { update
-- #   :uuid string
-- #   :data string
UPDATE skyblock SET data=:data WHERE uuid=:uuid;
-- # }

-- # }

-- #{ economy

-- # { init
CREATE TABLE IF NOT EXISTS economy (
    uuid TEXT PRIMARY KEY,
    data TEXT NOT NULL
);
-- # }

-- # { load
-- #   :uuid string
SELECT data FROM economy WHERE uuid=:uuid;
-- # }

-- # { loadAll
SELECT uuid, data FROM economy;
-- # }

-- # { create
-- #   :uuid string
-- #   :data string
INSERT OR REPLACE INTO economy (uuid, data) VALUES (:uuid, :data);
-- # }

-- # { update
-- #   :uuid string
-- #   :data string
UPDATE economy SET data=:data WHERE uuid=:uuid;
-- # }

-- # }

-- #{ rank

-- # { init
CREATE TABLE IF NOT EXISTS rank (
    uuid TEXT PRIMARY KEY,
    data TEXT NOT NULL
);
-- # }

-- # { load
-- #   :uuid string
SELECT data FROM rank WHERE uuid=:uuid;
-- # }

-- # { create
-- #   :uuid string
-- #   :data string
INSERT INTO rank (uuid, data) VALUES (:uuid, :data);
-- # }

-- # { update
-- #   :uuid string
-- #   :data string
UPDATE rank SET data=:data WHERE uuid=:uuid;
-- # }

-- # }

-- #{ area

-- # { init
CREATE TABLE IF NOT EXISTS area (
    uuid TEXT PRIMARY KEY,
    data TEXT NOT NULL
);
-- # }

-- # { load
-- #   :uuid string
SELECT data FROM area WHERE uuid=:uuid;
-- # }

-- # { create
-- #   :uuid string
-- #   :data string
INSERT INTO area (uuid, data) VALUES (:uuid, :data);
-- # }

-- # { update
-- #   :uuid string
-- #   :data string
UPDATE area SET data=:data WHERE uuid=:uuid;
-- # }

-- # }