<?php

namespace Corentin503SGE;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;

    public Item $gapple;

    public Item $soup;

    public array $cooldown_gapple = [];

    public array $cooldown_enderpearl = [];

    protected function onEnable(): void
    {
        self::setInstance($this);

        $this->saveDefaultConfig();

        $this->soup = StringToItemParser::getInstance()->parse($this->getConfig()->get("soup_item"));
        $this->gapple = StringToItemParser::getInstance()->parse($this->getConfig()->get("gapple_item"));

        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
    }

    public function soup(Player $player)
    {
        $config = $this->getConfig();
        $heal_add = $config->get("heal");
        $soup = $this->soup;

        if ($config->get("soup_automatic") === true) {
            if ($player->getHealth() < $player->getMaxHealth()) {
                $heal = $player->getMaxHealth() - $player->getHealth();
                $removed = $heal / $heal_add;
                if ($removed < 1) $removed = 1;
                $removed = (int)$removed;
                $player->setHealth($player->getMaxHealth());
                $player->sendPopup(str_replace("{heal}", $heal, $config->get("heal_popup")));
                $player->getInventory()->removeItem($soup->setCount($removed));
            }
        } else {
            if ($player->getHealth() < $player->getMaxHealth()) {
                $player->getInventory()->removeItem($soup);
                $player->setHealth($player->getHealth() + $heal_add);
                $player->sendPopup(str_replace("{heal}", $heal_add, $config->get("heal_popup")));
            }
        }
    }

    public function gapple(Player $player, PlayerItemConsumeEvent $event)
    {
        $config = $this->getConfig();

        if (!isset($this->cooldown_gapple[$player->getName()]) || $this->cooldown_gapple[$player->getName()] - time() <= 0) {
            $this->cooldown_gapple[$player->getName()] = time() + $config->get("gapple_cooldown");
            $explode = explode(":", $config->get("gapple_speed"));
            $eff = new EffectInstance(VanillaEffects::SPEED(), (int)$explode[1] * 20, (int)$explode[0] - 1, $explode[2]);
            $player->getEffects()->add($eff);
        } else {
            $event->cancel();

            $time = $this->cooldown_gapple[$player->getName()] - time();

            $player->sendMessage(str_replace("{second}", $time, $config->get("gapple_cooldown_message")));
        }
    }

    public function enderpearl(Player $player, PlayerItemUseEvent $event)
    {
        $config = $this->getConfig();

        if (!isset($this->cooldown_enderpearl[$player->getName()]) || $this->cooldown_enderpearl[$player->getName()] - time() <= 0) {
            $this->cooldown_enderpearl[$player->getName()] = time() + $config->get("enderpearl_cooldown");
        } else {
            $event->cancel();
            $time = $this->cooldown_enderpearl[$player->getName()] - time();

            $player->sendMessage(str_replace("{second}", $time, $config->get("enderpearl_cooldown_message")));
        }
    }
}