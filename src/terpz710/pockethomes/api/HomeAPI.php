<?php

declare(strict_types=1);

namespace terpz710\pockethomes\api;

use pocketmine\player\Player;

use pocketmine\utils\SingletonTrait;

use pocketmine\Server;

use pocketmine\world\Position;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use poggit\libasynql\SqlError;

use terpz710\pockethomes\PocketHomes;

final class HomeAPI {
    use SingletonTrait;

    protected DataConnector $database;

    public function init() : void{
        $this->database = libasynql::create(PocketHomes::getInstance(), PocketHomes::getInstance()->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->database->executeGeneric("table.homes");
    }

    public function setHome(Player $player, string $name, ?callable $callback = null) : void{
        $pos = $player->getPosition();
        $worldName = $player->getWorld()->getFolderName();

        $this->database->executeSelect(
            "homes.select_home",
            [
                "player" => $player->getName(),
                "home_name" => $name
            ],
            function (array $rows) use ($player, $name, $pos, $worldName, $callback) {
                if (!empty($rows)) {
                    $this->database->executeChange(
                        "homes.update",
                        [
                            "player" => $player->getName(),
                            "home_name" => $name,
                            "x" => $pos->getX(),
                            "y" => $pos->getY(),
                            "z" => $pos->getZ(),
                            "world" => $worldName
                        ],
                        fn(int $affectedRows) => $callback?($affectedRows > 0, null),
                        fn(SqlError $error) => $callback?(false, $error)
                    );
                } else {
                    $this->database->executeChange(
                        "homes.insert",
                        [
                            "player" => $player->getName(),
                            "home_name" => $name,
                            "x" => $pos->getX(),
                            "y" => $pos->getY(),
                            "z" => $pos->getZ(),
                            "world" => $worldName
                        ],
                        fn(int $affectedRows) => $callback?($affectedRows > 0, null),
                        fn(SqlError $error) => $callback?(false, $error)
                    );
                }
            },
            fn(SqlError $error) => $callback?(false, $error)
        );
    }

    public function teleportHome(Player $player, string $name, ?callable $callback = null) : void{
        $this->database->executeSelect(
            "homes.select_home",
            [
                "player" => $player->getName(),
                "home_name" => $name
            ],
            function (array $rows) use ($player, $callback) {
                if (empty($rows)) {
                    $callback?(false, "Home not found");
                    return;
                }

                $data = $rows[0];
                $worldManager = Server::getInstance()->getWorldManager();
                $world = $worldManager->getWorldByName($data["world"]);

                if ($world === null) {
                    if (!$worldManager->loadWorld($data["world"])) {
                        $callback?(false, "Failed to load world");
                        return;
                    }

                    $world = $worldManager->getWorldByName($data["world"]);
                    if ($world === null) {
                        $callback?(false, "World could not be found even after loading");
                        return;
                    }
                }

                $player->teleport(new Position((float)$data["x"], (float)$data["y"], (float)$data["z"], $world));
                $callback?(true, null);
            },
            fn(SqlError $error) => $callback?(false, $error)
        );
    }

    public function removeHome(Player $player, string $name, ?callable $callback = null) : void{
        $this->database->executeChange(
            "homes.delete",
            [
                "player" => $player->getName(),
                "home_name" => $name
            ],
            fn(int $affectedRows) => $callback?($affectedRows > 0, null),
            fn(SqlError $error) => $callback?(false, $error)
        );
    }

    public function homeList(Player $player, ?callable $callback = null) : void{
        $this->database->executeSelect(
            "homes.select_all",
            ["player" => $player->getName()],
            fn(array $rows) => $callback?(array_map(fn($row) => $row["home_name"], $rows), null),
            fn(SqlError $error) => $callback?(null, $error)
        );
    }

    public function checkHome(Player $player, string $name, ?callable $callback = null) : void{
        $this->database->executeSelect(
            "homes.select_home",
            [
                "player" => $player->getName(),
                "home_name" => $name
            ],
            fn(array $rows) => $callback?(!empty($rows), null),
            fn(SqlError $error) => $callback?(false, $error)
        );
    }

    public function close() : void{
        $this->database->close();
    }
}