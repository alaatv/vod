<?php

namespace App\Repositories;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\Slideshow;

class SlideshowRepo
{
    /**
     * @param  Slideshow  $slide
     * @param  Block|array|int  $blocks
     */
    public static function syncBlockBanner(Slideshow $slide, $blocks)
    {
        $slide->blocks()->sync($blocks);
    }

    public static function slideBlocks()
    {
        return Block::whereIn('type', [BlockType::TYPE_4TH_ID, BlockType::TYPE_5TH_ID, BlockType::TYPE_7TH_ID]);
    }

    public static function futureSlide($block, $now)
    {
        return $block->banners()->where('validSince', '>=', $now);
    }

    public static function getSlideshowBlocks(Slideshow $slide)
    {
        return $slide->blocks();
    }
}
