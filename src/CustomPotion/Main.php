<?php

namespace CustomPotion;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\PotionType;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitBlockEvent;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onHit(ProjectileHitBlockEvent $event): void{
        $projectile = $event->getEntity();

        if (!$projectile instanceof SplashPotion) return;
        
        if($projectile->getPotionType() === PotionType::STRONG_HEALING()){
            $player = $projectile->getOwningEntity();

            if($player instanceof Player){
                $distance = $projectile->getPosition()->distance($player->getPosition());

                if($distance <= 3.5 && $player->isAlive()){
                    $health = $player->getHealth() + 4.3;
                    $player->setHealth($health > $player->getMaxHealth() ? $player->getMaxHealth() : $health);
                }
            }
        }
    }

    /**
     * @handleCancelled true
     */
    public function onInteract(PlayerInteractEvent $event): void{
        $item = $event->getItem();

        if($item instanceof \pocketmine\item\SplashPotion){
            if ($item->getType() !== PotionType::STRONG_HEALING()) return;
            $player = $event->getPlayer();
            $entity = new SplashPotion(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->yaw, $player->getLocation()->pitch), $player, PotionType::STRONG_HEALING());
            $entity->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));
            $entity->spawnToAll();
            $item->pop();
            $player->getInventory()->setItemInHand($item);
        }
    }

}
