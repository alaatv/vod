<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-24
 * Time: 14:38
 */

namespace App\Classes\SEO;

class SeoDummyTags implements SeoInterface
{
    private $title;

    private $shortDescription;

    private $url;

    private $canonical;

    private $image;

    private $imageWidth;

    private $imageHeight;

    private $tags;

    private $seoMod;

    public function __construct($title, $shortDescription, $url, $canonical, $image, $imageWidth, $imageHeight, $tags)
    {

        $this->title = $title;
        $this->shortDescription = $shortDescription;
        $this->url = $url;
        $this->canonical = $canonical;
        $this->image = $image;
        $this->imageWidth = $imageWidth;
        $this->imageHeight = $imageHeight;
        $this->tags = $tags;
    }

    public function getMetaTags(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->shortDescription,
            'url' => $this->url,
            'canonical' => $this->canonical,
            'site' => 'آلاء',
            'imageUrl' => $this->image,
            'imageWidth' => $this->imageWidth,
            'imageHeight' => $this->imageHeight,
            'tags' => $this->tags,
            'seoMod' => SeoMetaTagsGenerator::SEO_MOD_GENERAL_TAGS,
        ];
    }
}
