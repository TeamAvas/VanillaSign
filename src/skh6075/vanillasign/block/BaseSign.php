<?php

declare(strict_types=1);

namespace skh6075\vanillasign\block;

use pocketmine\block\BaseSign as PMBaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\player\Player;
use UnexpectedValueException;

abstract class BaseSign extends PMBaseSign{

	/**
	 * Called by the player controller (network session) to update the sign text, firing events as appropriate.
	 *
	 * @return bool if the sign update was successful.
	 * @throws UnexpectedValueException if the text payload is too large
	 */

	public function updateText(Player $author, SignText $text) : bool{
		$size = 0;
		foreach($text->getLines() as $line){
			$size += strlen($line);
		}
		if($size > 1000){
			throw new UnexpectedValueException($author->getName() . " tried to write $size bytes of text onto a sign (bigger than max 1000)");
		}
		$ev = new SignChangeEvent($this, $author, new SignText(array_map(static function(string $line) : string{
			return $line;
		}, $text->getLines())));
		if($this->editorEntityRuntimeId === null || $this->editorEntityRuntimeId !== $author->getId()){
			$ev->cancel();
		}
		$ev->call();
		if(!$ev->isCancelled()){
			$this->setText($ev->getNewText());
			$this->position->getWorld()->setBlock($this->position, $this);
			return true;
		}

		return false;
	}
}