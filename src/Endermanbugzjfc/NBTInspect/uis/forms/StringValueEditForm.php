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

use function str_split;
use function is_null;
use function array_shift;
use function implode;
use function method_exists;

class StringValueEditForm extends ValueEditForm {
	
	protected function form() : \jojoe77777\FormAPI\Form {
		$f = parent::form();
		$s = $this->getUIInstance()->getSession();
		$f->addSwitch(TF::RED . 'Delete tag');
		$t = str_split($s->getCurrentTag()->getValue(), 75);
		foreach ($t as $v) $f->addInput('', '', $v);
		return $f;
	}

	protected function react($data = null) : void {
		$s = $this->getUIInstance()->getSession();
		if (is_null($data)) {
			if ($s->getRootTag() === $s->getCurrentTag()) return;
			$s->closeTag();
			$s->inspectCurrentTag();
			return;
		}
		array_shift($data);
		if ($data[0]) {
			$s->deleteCurrentTag();
			$s->inspectCurrentTag();
			return;
		}
		array_shift($data);
		$data = implode('', $data);
		if (method_exists($s->getCurrentTag(), 'setValue')) $s->getCurrentTag()->setValue($data);
		else {
			$reflection = new \ReflectionProperty($s->getCurrentTag(), 'value');
			$reflection->setAccessible(true);
			$reflection->setValue($reflection->class, $data);
		}
		if ($s->getRootTag() === $s->getCurrentTag()) {
			$f = new ApplyConfirmationForm($this->getUIInstance());
			$s->getPlayer()->sendForm($f->form());
			return;
		}
		$s->closeTag();
		$s->inspectCurrentTag();
	}
	
}
