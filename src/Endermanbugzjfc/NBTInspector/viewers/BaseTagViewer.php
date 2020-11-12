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
namespace Endermanbugzjfc\NBTInspect\viewers;

use pocketmine\{Player, nbt\tag\NamedTag, utils\Utils};

use function array_unshift;
use function array_reverse;
use function is_null;

abstract class BaseTagViewer implements ViewerInterface {

	private $player;
	private $nbt;
	protected $layers = [];
	protected $onsave;

	public function __construct(Player $p, NamedTag $nbt, ?callable $onsave) {
		$this->player = $p;
		$this->nbt = $nbt;
		Utils::validateCallableSignature(function(NamedTag $nbt) {}, $onsave);
		$this->onsave = $onsave;
		array_unshift($this->layers[], $nbt);
	}

	abstract public function open();

	public function getPlayer() : Player {
		return $this->player;
	}

	public function getNBT() : NamedTag {
		return $this->nbt;
	}

	protected function getOpenedLayers() : array {
		return $this->layers;
	}

	protected function openLayer(NamedTag $nbt) : void {
		array_unshift($this->layers, $nbt);
		$this->open();
	}

	protected function rootLayer() : bool {
		if (is_null($l = array_reverse($this->layers)[0] ?? null)) return false;
		$this->layers = [$l];
		$this->open();
		return true;
	}
}