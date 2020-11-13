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

use pocketmine\nbt\{NBT, tag\NamedTag};
use pocketmine\utils\TextFormat as TF;

class NBTInspectPluginUtils {

	public static function shortenTagType(NamedTag $tag, bool $color = true) : ?string {
	
		switch ($tag->getType()) {
		
			case NBT::TAG_End:
				return ($color ? TF::GRAY : '') . 'E';
				break;
				
			case NBT::TAG_Byte:
				return ($color ? TF::DARK_RED : '') . 'B';
				break;
				
			case NBT::TAG_Short:
				return ($color ? TF::DARK_PURPLE : '') . 'S';
				break;
				
			case NBT::TAG_Int:
				return ($color ? TF::DARK_BLUE : '') . 'I';
				break;
				
			case NBT::TAG_Long:
				return ($color ? TF::DARK_AQUA : '') . 'L';
				break;
				
			case NBT::TAG_Float:
				return ($color ? TF::YELLOW : '') . 'F';
				break;
				
			case NBT::TAG_Double:
				return ($color ? TF::DARK_GREEN : '') . 'D';
				break;
				
			case NBT::TAG_Byte_Array:
				return ($color ? TF::DARK_RED : '') . 'BA';
				break;
				
			case NBT::TAG_String:
				return ($color ? TF::DARK_GRAY : '') . 'S';
				break;
		
			case NBT::TAG_List:
				return ($color ? TF::GOLD : '') . 'LI';
				break;
				
			case NBT::TAG_Compound:
				return ($color ? TF::BLACK : '') . 'C';
				break;
				
			case NBT::TAG_IntArray:
				return ($color ? TF::DARK_BLUE : '') . 'IA';
				break;
		
		}
		return null;
	}

}