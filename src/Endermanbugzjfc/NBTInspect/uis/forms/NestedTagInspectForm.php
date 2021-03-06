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
namespace Endermanbugzjfc\NBTInspect\uis\forms;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\utils\TextFormat as TF;

use jojoe77777\FormAPI\Form;

use Endermanbugzjfc\NBTInspect\{NBTInspect, uis\UIInterface, Utils};

use function assert;
use function implode;
use function array_map;

class NestedTagInspectForm extends BaseForm {

	protected const TYPE = self::SIMPLE;

	private $buttons = [];
	protected $switchui;

	protected function form() : Form {
		$f = $this->getForm();
		$s = $this->getUIInstance()->getSession();
		$t = $s->getCurrentTag();

		$f->setTitle(TF::BLUE . 'Browse ' . TF::DARK_AQUA . 'Tag');

		$f->setContent(TF::YELLOW . 'Inspecting in: ' . TF::AQUA . implode(TF::RESET . TF::BLUE . ' >> ' . TF::AQUA, array_map(function(NamedTag $t) : string {
			return $t->getName() . TF::BOLD . '(' . Utils::shortenTagType() . $t . TF::AQUA . ')';
		}, $s->getAllOpenedTags(true))) . "\n" . TF::RESET . TF::YELLOW . 'Tags: ' . TF::AQUA . $t->getCount() . ' of ' . (Utils::getTagType($t) ?? TF::BLACK . 'Mixed') . TF::AQUA . ' type');

		foreach ($t->getValue() as $k => $st) {
			$this->buttons[] = $st;
			if ($t instanceof CompoundTag) $this->getForm()->addButton(TF::BOLD . TF::DARK_AQUA . $st->getName() . "\n" . TF::RESET . Utils::getTagType($st) . ' Tag');
			else $this->getForm()->addButton(TF::BLUE . 'Tag' . "\n" . TF::BOLD . TF::DARK_AQUA . TF::BLUE . $k);
		}

		if (($ui = $this->getUIInstance()->getNextAvailableUI()) !== null and is_a($ui, UIInterface::class, true)) $this->getForm()->addButton(TF::BOLD . TF::DARK_AQUA . 'Switch UI' . TF::RESET . "\n" . TF::BLUE . 'To ' . TF::BOLD . $ui::getName());
		if ($s->getRootTag() !== $s->getCurrentTag()) $this->getForm()->addButton(TF::BOLD . TF::DARK_RED . "Delete\nThis Tag");
		$this->getForm()->addButton(TF::BOLD . TF::DARK_GREEN . "Insert\nNew Tag");
		if ($t instanceof ListTag) $this->getForm()->addButton(TF::BOLD . TF::DARK_AQUA . "Rearrange\nTags");

		return $f;
	}
	
	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		$t = $s->getCurrentTag();
		assert($t instanceof CompoundTag or $t instanceof ListTag);
		if (!isset($data)) {
			if ($s->getRootTag() === $t) {
				$f = new SaveConfirmationForm($this->getUIInstance());
				$s->getSessionOwner()->sendForm($f->form());
				return;
			}
			$s->closeTag();
			$s->inspectCurrentTag();
		}
		if ($data >= count($t)) {
			$data = $data - count($t);
			switch ($data) {
				case 0:
					NBTInspect::getInstance()->switchUserUI($this->getUIInstance()->getSession()->getSessionOwner(), $this->switchui);
					$s->switchUI();
					$s->getUIInstance()->inspect();
					break;
				
				case 1:
					if ($s->getRootTag() === $s->getCurrentTag()) $s->getSessionOwner()->sendForm((new TagRearrangeForm($this->getUIInstance()->getSession()))->form());
					else {
						$s->deleteCurrentTag();
						$s->inspectCurrentTag();
					}
					break;

				case 2:
					if ($t instanceof ListTag) $s->getSessionOwner()->sendForm((new TagRearrangeForm($this->getUIInstance()->getSession()))->form());
					else $s->inspectCurrentTag();
					break;
			}
			return;
		}
		if ($t instanceof CompoundTag) foreach ($t->getValue() as $k => $v) if ($k === $data) $s->openTag($t->getTag($v));
		else $s->openTag($t[$v]);
		$s->inspectCurrentTag();
	}

}