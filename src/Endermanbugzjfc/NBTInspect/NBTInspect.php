<?php

/*

     					_________	  ______________		
     				   /        /_____|_           /
					  /————/   /        |  _______/_____    
						  /   /_     ___| |_____       /
						 /   /__|    ||    ____/______/
						/   /    \   ||   |   |   
					   /__________\  | \   \  |
					       /        /   \   \ |
						  /________/     \___\|______
						                   |         \ 
							  PRODUCTION   \__________\	

							   翡翠出品 。 正宗廢品  
 
*/

declare(strict_types=1);
namespace Endermanbugzjfc\NBTInspect;

use pocketmine\{Player,
	nbt\tag\NamedTag,
	item\Item,
	entity\Entity,
	level\Level,
	command\Command,
	command\CommandSender,
	utils\TextFormat as TF
};

use function is_a;

final class NBTInspect extends \pocketmine\plugin\PluginBase implements \pocketmine\event\Listener {
	use API;

	public const UI_DEFAULT = uis\FormUI::class;

	protected $players = [];
	protected $uis = [];

	private static $instance = null;

	public function onEnable() : void {
		self::$instance = $this;
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function playerQuitEvent(\pocketmine\event\player\PlayerQuitEvent $ev) : void {
		unset($this->players[$ev->getPlayer()->getId()]);
	}

	public static function getInstance() : ?self {
		return self::$instance;
	}

	public static function inspect(Player $p, NamedTag $nbt, ?callable $onsave) : sessions\InspectSession {
		$s = new sessions\InspectSession($p, $nbt, $onsave);
		$s->inspectCurrentTag();
	}

	public static function inspectItem(Player $p, Item $item) : sessions\InspectSession {
		return $this->inspect($p, $item->getNamedTag(), function(NamedTag $nbt) use ($item) : void {
			self::disclaimerScreen($p, function(Player $p, $data = false) {
				if (!$data) return;
				if (!$item instanceof Item) return;
				$item->setNamedTag($nbt);
			});
		});
	}

	public static function inspectEntity(Player $p, Entity $entity) : sessions\InspectSession {
		return $this->inspect($p, $entity->namedtag, function(NamedTag $nbt) use ($entity) : void {
			self::disclaimerScreen($p, function(Player $p, $data = false) {
				if (!$data) return;
				if (!$entity instanceof Entity) return;
				$entity->namedtag = $nbt;
			});
		});
	}

	public static function inspectLevel(Player $p, Level $w) : sessions\InspectSession {
		return $this->inspect($p, $w->getLevelData(), function(NamedTag $nbt) use ($w) : void {
			if (!$w instanceof Level) return;
			$reflect = new \ReflectionProperty($w, 'levelData');
			$reflect->setAccessible(true);
			$reflect->setValue($reflect->class, $nbt);
		});
	}

	private static function disclaimerScreen(Player $p, \closure $callback) : \jojoe77777\FormAPI\ModalForm {
		$f = \jojoe77777\FormAPI\ModalForm($callback);
		$f->addTitle(TF::BOLD . TF::BLUE . '>> ' . TF::DARK_AQUA . '!WARNING!' . TF::BLUE . ' <<');
		$f->setContent(TF::YELLOW . 'This plugin should only be use on ' . TF::BOLD . 'debug purpose,' . TF::RESET . TF::YELLOW . ' there ' . TF::BOLD . TF::RED . 'might be a chance to break your server or corrupt your world files!' . TF::RESET . TF::YELLOW . 'It is not my fault if this happens to you.');
		$f->setButton1(TF::BLUE . 'Continue Inspecting');
		$f->setButton2(TF::DARK_AQU . 'Back');
		$p->sendForm($f);
	}

	public function switchPlayerUI(Player $p, uis\UIInterface $ui) {
		$this->players[$p->getId()] = $ui;
		return $this;
	}

	public function getPlayerUI(Player $p) : string {
		return $this->players[$p->getId()] ?? self::UI_DEFAULT;
	}

	public function registerUI(uis\UIInterface $ui) : void {
		if (!is_a($ui, uis\UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $rui) if ($ui::getName() === $rui::getName()) throw new \InvalidArgumentException('Theres is already an registered UI having the same name!');
		$this->uis[] = $ui;
	}

	public function unregisterUI(uis\UIInterface $ui) : bool {
		if (!is_a($ui, uis\UIInterface::class, true)) throw new \InvalidArgumentException('Argument 1 must be a namespace of a class that implements UIInterface');
		foreach ($this->uis as $i => $rui) if ($rui === $ui) {
			unset($this->uis[$i]);
			return true;
		}
		return false;
	}

	public function getAllUI() : array {
		return $this->uis;
	}

	public function onCommand(CommandSender $p, Command $cmd, string $aliase, array $args) : bool {}
}