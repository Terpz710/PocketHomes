<?php

declare(strict_types=1);

namespace terpz710\pockethomes\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\pockethomes\api\HomeAPI;

use CortexPE\Commando\BaseCommand;

class HomesCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("pockethomes.cmd");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game!");
            return;
        }

        HomeAPI::getInstance()->homeList($sender, function (?array $homes, $error) use ($sender) {
            if ($error !== null) {
                $sender->sendMessage("§cAn error occurred while retrieving your homes...");
                return;
            }

            if (empty($homes)) {
                $sender->sendMessage("§cYou don't have any homes set");
                return;
            }

            $sender->sendMessage("Your Homes: §e" . implode("§7, §e", $homes));
        });
    }
}