<?php

declare(strict_types=1);

namespace skh6075\vanillasign;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIdHelper;
use pocketmine\block\BlockToolType;
use pocketmine\block\utils\TreeType;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use skh6075\vanillasign\block\FloorSign;
use skh6075\vanillasign\block\WallSign;

final class Loader extends PluginBase implements Listener{

	private BlockFactory $blockFactory;

	protected function onLoad() : void{
		$this->blockFactory = BlockFactory::getInstance();
		$signBreakInfo = new BlockBreakInfo(1.0, BlockToolType::AXE);
		foreach(TreeType::getAll() as $treeType){
			$name = $treeType->getDisplayName();

			$this->registerAllMeta(new FloorSign(BlockLegacyIdHelper::getWoodenFloorSignIdentifier($treeType), $name . " Sign", $signBreakInfo));
			$this->registerAllMeta(new WallSign(BlockLegacyIdHelper::getWoodenWallSignIdentifier($treeType), $name . " Wall Sign", $signBreakInfo));
		}
	}

	private function registerAllMeta(Block $default, Block ...$additional) : void{
		var_dump($default->getName() . "호출");
		$ids = [];
		$this->blockFactory->register($default, true);
		foreach($default->getIdInfo()->getAllBlockIds() as $id){
			$ids[$id] = $id;
		}
		foreach($additional as $block){
			$this->blockFactory->register($block, true);
			foreach($block->getIdInfo()->getAllBlockIds() as $id){
				$ids[$id] = $id;
			}
		}

		foreach($ids as $id){
			for($meta = 0; $meta < 1 << Block::INTERNAL_METADATA_BITS; ++$meta){
				if(!$this->blockFactory->isRegistered($id, $meta)){
					$this->blockFactory->remap($id, $meta, $default);
				}
			}
		}
	}
}