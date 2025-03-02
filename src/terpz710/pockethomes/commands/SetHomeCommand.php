<?php

declare(strict_types=1);

namespace terpz710\pockethomes\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\pockethomes\api\HomeAPI;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class SetHomeCommand extends BaseCommand {

    protected function prepare() : void{
        $this->registerArgument(0, new RawStringArgument("name"));

        $this->setPermission("pockethomes.cmd");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game!");
            return;
        }

        $homeName = $args["name"];

        HomeAPI::getInstance()->setHome($sender, $homeName, function (bool $success, $error) use ($sender, $homeName) {
            if ($error !== null) {
                $sender->sendMessage("§cAn error occurred while setting your home...");
                return;
            }

            if ($success) {
                $sender->sendMessage("§aHome '{$homeName}' has been set!");
            } else {
                $sender->sendMessage("§cFailed to set home '{$homeName}'");
            }
        });
    }
}