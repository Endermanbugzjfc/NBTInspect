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
namespace Endermanbugzjfc\NBTInspect\uis;

use pocketmine\{
	Player,
	utils\TextFormat as TF,
	scheduler\ClosureTask,
	scheduler\TaskHandler
};
use pocketmine\nbt\tag\{NamedTag, CompoundTag, ByteTag, ShortTag, IntTag, LongTag, FloatTag, DoubleTag, StringTag, ByteArrayTag, IntArrayTag};

use Endermanbugzjfc\NBTInspect\NBTInspect;

class FormUI extends BaseUI {

	protected $preinspect = null;

	public static function getName() : string {
		return 'Form';
	}
	
	public static function accessibleBy(InspectSession $session) : bool {
		return $session->getSessionOwner() instanceof Player;
	}

	public function preInspect() {
		$this->preinspect = NBTInspect::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(int $ct) : void {
			$this->getSession()->getSessionOwner()->sendPopup(TF::YELLOW . ‘Loading NBT tag to inspect...’);
		}), 40);

		return $this;
	}

	public function inspect() {
		if ($this->preinspect instanceof TaskHandler) $this->preinspect->cancel();
		switch (true) {
			case $tag instanceof CompoundTag:
			case $tag instanceof ListTag:
				return new forms\NestedTagInspectForm($this);

			case $tag instanceof StringTag:
			case $tag instanceof ByteArrayTag:
			case $tag instanceof IntArrayTag:
				$hotbar = hotbar\BookEditorHotbar($this);
				if (!$tag instanceof IntArrayTag) $hotbar->allowMode($hotbar::MODE_RAW);
				$hotbar->allowMode($hotbar::MODE_BINARY);
				$hotbar->setValue($tag->getValue());
				$hotbar->setCallback(function(string $value) use ($tag) : void {
					$tag->setValue($value);
					$this->getSession()->inspectCurrentTag();
				});

			case $tag instanceof ByteTag:
			case $tag instanceof ShortTag:
			case $tag instanceof IntTag:
			case $tag instanceof LongTag:
			case $tag instanceof FloatTag:
			case $tag instanceof DoubleTag:
				return new forms\NumbericValueEditForm($this);

			default:
				throw new \RuntimeException(‘An invalid tag type has given’);
		}
		return $this;
	}

	public function close() {return $this;}
	
}
