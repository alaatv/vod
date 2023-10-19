<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-27
 * Time: 12:17
 */

namespace App\Classes\SEO;

use Exception;
use SEO;

class SeoMetaTagsGenerator
{
    public const SEO_MOD_VIDEO_TAGS = 1;

    public const SEO_MOD_PDF_TAGS = 2;

    public const SEO_MOD_ARTICLE_TAGS = 3;

    public const SEO_MOD_PRODUCT_TAGS = 4;

    public const SEO_MOD_GENERAL_TAGS = 5;

    private const SEO_TYPE_VIDEO = 'video';

    private const SEO_TYPE_WEBSITE = 'website';

    private const SEO_TYPE_ARTICLE = 'article';

    protected $title;

    protected $description;

    protected $url;

    protected $canonical;

    protected $site;

    protected $imageUrl;

    protected $imageWidth;

    protected $imageHeight;

    protected $seoMod;

    protected $playerUrl;

    protected $playerWidth;

    protected $playerHeight;

    protected $videoDirectUrl;

    protected $videoActorName;

    protected $videoActorRole;

    protected $videoDirector;

    protected $videoWriter;

    protected $videoDuration;

    protected $videoReleaseDate;

    protected $tags;

    protected $videoWidth;

    protected $videoHeight;

    protected $videoType;

    protected $articleAuthor;

    protected $articleModifiedTime;

    protected $articlePublishedTime;

    /**
     * SeoMetaTagsGenerator constructor.
     *
     * @param  SeoInterface  $seo
     *
     * @throws Exception
     */
    public function __construct(SeoInterface $seo)
    {
        foreach ($seo->getMetaTags() as $key => $value) {

            $this->$key = $value;
        }
        $this->build();
    }

    /**
     * @throws Exception
     */
    protected function build(): void
    {
        $this->generateCommonTags();
        switch ($this->seoMod) {
            case self::SEO_MOD_VIDEO_TAGS:
                $this->generateVideoTags();
                break;
            case self::SEO_MOD_PDF_TAGS:
                $this->generatePdfTags();
                break;
            case self::SEO_MOD_ARTICLE_TAGS:
                $this->generateArticleTags();
                break;
            case self::SEO_MOD_PRODUCT_TAGS:
                $this->generateProductTags();
                break;
            case self::SEO_MOD_GENERAL_TAGS:
                SEO::opengraph()
                    ->setType(self::SEO_TYPE_WEBSITE);
                break;
            default:
                throw new Exception('seoMod should be set!');
        }
    }

    protected function generateCommonTags()
    {
        SEO::setTitle($this->title);
        SEO::setDescription($this->description);
        SEO::opengraph()
            ->setUrl($this->url);
        SEO::setCanonical($this->canonical);
        SEO::twitter()
            ->setSite($this->site);
        SEO::opengraph()
            ->addImage($this->imageUrl, [
                'height' => $this->imageHeight,
                'width' => $this->imageWidth,
            ]);
    }

    protected function generateVideoTags()
    {
        SEO::twitter()
            ->addValue('player', $this->playerUrl);
        SEO::twitter()
            ->addValue('player:width', $this->playerWidth);
        SEO::twitter()
            ->addValue('player:height', $this->playerHeight);
        // video.movie
        SEO::opengraph()
            ->setType(self::SEO_TYPE_VIDEO)
            ->setVideoMovie([
                'actor' => $this->videoActorName,
                'actor:role' => $this->videoActorRole,
                'director' => $this->videoDirector,
                'writer' => $this->videoWriter,
                'duration' => $this->videoDuration,
                'release_date' => $this->videoReleaseDate,
                'tag' => $this->tags->tags,
            ]);
        SEO::opengraph()
            ->addVideo($this->videoDirectUrl, [
                'secure_url' => $this->videoDirectUrl,
                'type' => $this->videoType,
                'width' => $this->videoWidth,
                'height' => $this->videoHeight,
            ]);
    }

    protected function generatePdfTags()
    {

        SEO::opengraph()
            ->setType(self::SEO_TYPE_WEBSITE);
    }

    protected function generateArticleTags()
    {
        SEO::opengraph()
            ->setType(self::SEO_TYPE_ARTICLE)
            ->setArticle([
                'published_time' => $this->articlePublishedTime,
                'modified_time' => $this->articleModifiedTime,
                'author' => $this->articleAuthor,
                'tag' => $this->tags->tags,
            ]);
    }

    protected function generateProductTags()
    {
        SEO::opengraph()
            ->setType(self::SEO_TYPE_WEBSITE);
    }
}
