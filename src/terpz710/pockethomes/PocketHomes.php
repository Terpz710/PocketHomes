<?php

declare(strict_types=1);

namespace terpz710\pockethomes;

use pocketmine\plugin\PluginBase;

use terpz710\pockethomes\commands\HomeCommand;
use terpz710\pockethomes\commands\HomesCommand;
use terpz710\pockethomes\commands\SetHomeCommand;
use terpz710\pockethomes\commands\DeleteHomeCommand;

use terpz710\pockethomes\HomeAPI;

use CortexPE\Commando\PacketHooker;

final class PocketHomes extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("PocketHomes", [
            new HomeCommand($this, "home", "Teleport to one of your homes"),
            new HomesCommand($this, "homes", "View a list of your homes"),
            new SetHomeCommand($this, "sethome", "Set a home at your current location"),
            new DeleteHomeCommand($this, "deletehome", "Remove one of your homes", ["removehome", "delhome"])
        ]);

        HomeAPI::getInstance()->init();
    }

    protected function onDisable() : void{
        HomeAPI::getInstance()->close();
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}
