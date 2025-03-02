-- #!mysql

-- #{ table
    -- #{ homes
        CREATE TABLE IF NOT EXISTS homes (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            player TEXT NOT NULL,
            home_name TEXT NOT NULL,
            x REAL NOT NULL,
            y REAL NOT NULL,
            z REAL NOT NULL,
            world TEXT NOT NULL
        );
    -- #}
-- #}

-- #{ homes
    -- #{ insert
        -- # :player string
        -- # :home_name string
        -- # :x float
        -- # :y float
        -- # :z float
        -- # :world string
        INSERT INTO homes (player, home_name, x, y, z, world)
        VALUES (:player, :home_name, :x, :y, :z, :world);
    -- #}

    -- #{ select_home
        -- # :player string
        -- # :home_name string
        SELECT * FROM homes WHERE player = :player AND home_name = :home_name;
    -- #}

    -- #{ select_all
        -- # :player string
        SELECT * FROM homes WHERE player = :player;
    -- #}

    -- #{ delete
        -- # :player string
        -- # :home_name string
        DELETE FROM homes WHERE player = :player AND home_name = :home_name;
    -- #}

    -- #{ update
        -- # :player string
        -- # :home_name string
        -- # :x float
        -- # :y float
        -- # :z float
        -- # :world string
        UPDATE homes
        SET x = :x, y = :y, z = :z, world = :world
        WHERE player = :player AND home_name = :home_name;
    -- #}
-- #}