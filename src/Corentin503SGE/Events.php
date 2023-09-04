<?php

namespace Corentin503SGE;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\GameMode;

class Events implements Listener
{
    public function onConsume(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $config = Main::getInstance()->getConfig();

        if ($config->get("gapple") === true) {
            if ($item->getTypeId() === Main::getInstance()->gapple->getTypeId()) {
                Main::getInstance()->gapple($player, $event);
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $soup = Main::getInstance()->soup;
        $config = Main::getInstance()->getConfig();

        if ($config->get("soup") === true) {
            if ($config->get("soup_on_block") === true) {
                if ($item->getTypeId() === $soup->getTypeId()) {
                    Main::getInstance()->soup($player);
                }
            }
        }
    }

    public function onUse(PlayerItemUseEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $soup = Main::getInstance()->soup;
        $config = Main::getInstance()->getConfig();

        if ($config->get("soup") === true) {
            if ($item->getTypeId() === $soup->getTypeId()) {
               Main::getInstance()->soup($player);
            }
        }

        if ($item->getTypeId() === ItemTypeIds::ENDER_PEARL) {
            Main::getInstance()->enderpearl($player, $event);
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $config = Main::getInstance()->getConfig();

        if ($player->getGamemode() === GameMode::CREATIVE()) return;

        if ($config->get("soup") === true) {
            $soup = Main::getInstance()->soup;
            $count = 0;

            foreach ($player->getInventory()->getContents() as $items) {
                if ($items->getTypeId() === $soup->getTypeId()) $count += $items->getCount();
            }

            if ($count > $config->get("max_soup")) {
                $player->getInventory()->remove($soup);
                $player->getInventory()->addItem($soup->setCount($config->get("max_soup")));
            }
        }
        if ($config->get("gapple") === true) {
            $gapple = Main::getInstance()->gapple;
            $count = 0;

            foreach ($player->getInventory()->getContents() as $items) {
                if ($items->getTypeId() === $gapple->getTypeId()) $count += $items->getCount();
            }

            if ($count > $config->get("max_gapple")) {
                $player->getInventory()->remove($gapple);
                $player->getInventory()->addItem($gapple->setCount($config->get("max_gapple")));
            }
        }
    }
}