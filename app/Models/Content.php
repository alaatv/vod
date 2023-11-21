<?php

namespace App\Models;

use App\Classes\FavorableInterface;
use App\Classes\LinkGenerator;
use App\Classes\Search\RecommendedProductSearch;
use App\Classes\Search\RelatedProductSearch;
use App\Classes\SEO\SeoInterface;
use App\Classes\SEO\SeoMetaTagsGenerator;
use App\Classes\Taggable;
use App\Collection\ContentCollection;
use App\Collection\ProductCollection;
use App\Repositories\SubscriptionRepo;
use App\Repositories\VastRepo;
use App\Services\ForrestService;
use App\Traits\APIRequestCommon;
use App\Traits\CommentTrait;
use App\Traits\Content\TaggableContentTrait;
use App\Traits\DateTrait;
use App\Traits\favorableTraits;
use App\Traits\logger;
use App\Traits\ModelTrackerTrait;
use App\Traits\WatchHistoryTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Artisan, Cache};
use Stevebauman\Purify\Facades\Purify;

class Content extends BaseModel implements Taggable, SeoInterface, FavorableInterface
{
    /*
    |--------------------------------------------------------------------------
    | Traits
    |--------------------------------------------------------------------------
    */
//    use Searchable;
    use APIRequestCommon;
    use favorableTraits;
    use ModelTrackerTrait;
    use TaggableContentTrait;
    use DateTrait;
    use CommentTrait;
    use WatchHistoryTrait;

    use logger;

    protected const LOG_ATTRIBUTES = ['name', 'description'];
    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    public const CONTENT_TYPE_PAMPHLET = 1;
    public const CONTENT_TYPE_EXAM = 2;
    public const CONTENT_TYPE_GHALAMCHI = 3;
    public const CONTENT_TYPE_GOZINE2 = 4;
    public const CONTENT_TYPE_SANJESH = 5;
    public const CONTENT_TYPE_KONKOOR = 6;
    public const CONTENT_TYPE_BOOK = 7;
    public const CONTENT_TYPE_VIDEO = 8;
    public const CONTENT_TYPE_ARTICLE = 9;
    public const CONTENT_TYPE_VOICE = 10;
    public const CONTENT_TEMPLATE_VIDEO = 1;
    public const CONTENT_TEMPLATE_PAMPHLET = 2;
    public const CONTENT_TEMPLATE_ARTICLE = 3;
    public const CONTENT_TEMPLATE_EXAM = 4;
    public const QUALITY_MAP = [
        '240p' => '240p',
        '480p' => 'hq',
        '720p' => 'HD_720p',
    ];

    protected const PURIFY_NULL_CONFIG = ['HTML.Allowed' => ''];
    public const INDEX_PAGE_NAME = 'contentPage';
    /**      * The attributes that should be mutated to dates.        */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'validSince' => 'datetime',
        'forrest_tree_grid' => 'array',
        'forrest_tree_tags' => 'array',
    ];
    protected $table = 'educationalcontents';
    protected $fillable = [
        'redirectUrl',
        'name',
        'description',
        'context',
        'file',
        'order',
        'validSince',
        'metaTitle',
        'metaDescription',
        'metaKeywords',
        //        'tags',
        'author_id',
        'template_id',
        'contenttype_id',
        'contentset_id',
        'isFree',
        'enable',
        'display',
        'section_id',
        'tmp_description',
        'copied_from',
        'hls',
        'forrest_tree_grid',
        'forrest_tree_tags',
        'content_status_id',
        'short_description',
    ];
    protected $hidden = [
        'user',
        'deleted_at',
//        'validSince',
//        'enable',
        'metaKeywords',
        'metaDescription',
        'metaTitle',
        'author_id',
        'template_id',
        'slug',
        'contentsets',
        'contentset_id',
        'template',
//        'contenttype',
    ];
    protected $with = [
        'user',
        'set',
        'template',
        'contenttype'
    ];
    protected $timepoints_cache;
    protected $cachedMethods = [
        'getTimesAttribute',
        'getSourcesAttribute',
        'getDisplayNameAttribute',
    ];
    protected $is_favored_cache;
    protected $sources_cache;
    protected $display_name_cache;
    protected $file_cache;
    /**
     * Get the content's author .
     *
     * @return User
     */
    protected $content_author_cache;

    /**
     * @return array
     */
    public static function videoFileCaptionTable(): array
    {
        return [
            '240p' => 'کیفیت متوسط',
            '480p' => 'کیفیت بالا',
            '720p' => 'کیفیت عالی',
        ];
    }

    public static function pamphletFileCaption(): string
    {
        return 'جزوه';
    }

    public static function voiceFileCaption(): string
    {
        return 'صوت';
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'contents_index';
    }

    public function shouldBeSearchable()
    {
        return $this->isPublished();
    }

    private function isPublished()
    {
        return $this->isActive();
    }

    /**
     * Checks whether the content is active or not .
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return ($this->isEnable() && $this->isValid() ? true : false);
    }

    /**
     * Checks whether the content is enable or not .
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        if ($this->enable) {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Private methods
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Checks whether the content is valid or not .
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->validSince === null || $this->validSince < Carbon::createFromFormat('Y-m-d H:i:s',
                Carbon::now('Asia/Tehran'))) {
            return true;
        }

        return false;
    }

    public function hasThumbnail(): bool
    {
        return !is_null($this->thumbnail_for_admin) && isFileExists($this->thumbnail_for_admin);
    }

    public function allFilesExist(): bool
    {
        $files = $this->file_for_admin;
        $files = Arr::get($files, 'video', []);
        foreach ($files as $file) {
            if (!isFileExists($file->link)) {
                return false;
            }
        }

        $files = Arr::get($files, 'pamphlet', []);
        foreach ($files as $file) {
            if (!isFileExists($file->link)) {
                return false;
            }
        }

        return true;
    }

    public function hasDuration(): bool
    {
        return !is_null($this->duration);
    }

    public function isVideo(): bool
    {
        return $this->contenttype_id === self::CONTENT_TYPE_VIDEO;
    }

    public function isArticle(): bool
    {
        return $this->contenttype_id === self::CONTENT_TYPE_ARTICLE;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $unSetArray = [
            'basePrice',
            'user',
            'deleted_at',
            'validSince',
            'enable',
            'metaKeywords',
            'metaDescription',
            'metaTitle',
            'author_id',
            'template_id',
            'slug',
            'contentset_id',
            'template',
            'contenttype',
            'url',
            'apiUrl',
            'nextUrl',
            'nextApiUrl',
            'previousUrl',
            'previousApiUrl',
            'author',
            'file',
            'order',
            'validSince',
            'metaTitle',
            'metaDescription',
            'metaKeywords',
            'tags',
            'author_id',
            'template_id',
            'contenttype_id',
            'contentset_id',
            'isFree',
            'enable',
            'created_at',
            'updated_at',
            'deleted_at',
            'validSince',
            'page_view',
            'thumbnail',
            'redirectUrl',
            'tmp_description',
        ];
        foreach ($unSetArray as $key) {
            unset($array[$key]);
        }
        if (!(!$this->isActive() || isset($this->redirectUrl))) {
            return $array;
        }
        foreach ($array as $key => $value) {
            $array[$key] = null;
        }
        return $array;
    }

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->id;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new ContentCollection($models);
    }

    /**
     * Scope a query to only include enable(or disable) Contents.
     *
     * @param  Builder  $query
     * @param  int  $enable
     *
     * @return Builder
     */
    public function scopeEnable($query, $enable = 1)
    {
        return $query->where('enable', $enable);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeVideo($query)
    {
        return $query->where('contenttype_id', self::CONTENT_TYPE_VIDEO);
    }

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopePamphlet($query)
    {
        return $query->where('contenttype_id', self::CONTENT_TYPE_PAMPHLET);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeArticle($query)
    {
        return $query->where('contenttype_id', self::CONTENT_TYPE_ARTICLE);
    }

    /**
     * Scope a query to only include Valid Contents.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->where('validSince', '<', Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran')))
                ->orWhereNull('validSince');
        });
    }

    public function scopeDisplay($query, $display = 1)
    {
        return $query->where('display', $display);
    }

    /**
     * Scope a query to only include active Contents.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->enable()
            ->valid();
    }

    public function scopeSearch($query, $keywords)
    {
        $keywords = explode(' ', $keywords);
        foreach ($keywords as $keyword) {
            $query->where('name', 'LIKE', '%'.$keyword.'%');
        }
        return $query;
    }

    public function scopeRedirected($query)
    {
        return $query->whereNotNull('redirectUrl');
    }

    public function scopeNotRedirected($query)
    {
        return $query->whereNull('redirectUrl');
    }

    public function scopeFree($query)
    {
        return $query->where('isFree', 1);
    }

    /**
     * Scope a query to only include Contents that will come soon.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeSoon($query)
    {
        return $query->where('validSince', '>', Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())
            ->timezone('Asia/Tehran'));
    }

    public function scopeType($query, $type)
    {
        return $query->where('contenttype_id', $type);
    }

    public function getUrlAttribute($value): string
    {
        if (isset($this->id)) {
            return appUrlRoute('c.show', $this);
        }
        return '';
    }

    public function getForrestTreeAttribute()
    {
        return $this->forrestGetter('forrest_tree_grid');
    }

    private function forrestGetter($attribute)
    {
        $treesCollection = collect();
        $forrestTreeGrid = $this->attributes[$attribute];
        $key = "content:$attribute:".$this->cacheKey().$forrestTreeGrid;
        $forrestTreeGrid = json_decode($forrestTreeGrid);
        return Cache::tags(['content', 'content_'.$this->id])
            ->remember($key, config('constants.CACHE_600'), function () use ($treesCollection, $forrestTreeGrid) {
                if (isset($forrestTreeGrid) && is_array($forrestTreeGrid)) {
                    foreach ($forrestTreeGrid as $grid) {
                        $tree = resolve(ForrestService::class)->getTreeByGrid($grid);
                        if ($tree) {
                            $treesCollection->push(array_values(json_decode(json_encode($tree), associative: true))[0]);
                        }
                    }
                }
                return $treesCollection;
            });
    }

    public function getForrestTagsAttribute()
    {
        return $this->forrestGetter('forrest_tree_tags');
    }

    public function getPreviousUrlAttribute($value)
    {
        return ($this->getPreviousContent() ?: new Content())->url;
    }

    public function getPreviousContent()
    {
        $key = 'content:previousContent:'.$this->cacheKey();

        if (!isset($this->contentset_id)) {
            return null;
        }

        $set = $this->set;
        return Cache::tags(['content', 'previousContent', 'content_'.$this->id, 'content_previousContent_'.$this->id])
            ->remember($key, config('constants.CACHE_600'), function () use ($set) {
                if (!isset($set)) {
                    return null;
                }
                $previousContent = $set->activeContents([])
                    ->where('contenttype_id', $this->contenttype_id)
                    ->where('order', '<=', $this->order - 1)
                    ->orderByDesc('order')
                    ->first();


                return $previousContent ?? null;
            });
    }

    public function getPreviousContentForAPIV2()
    {
        $key = 'content:getPreviousContentForAPIV2:'.$this->cacheKey();

        return Cache::tags(['content', 'previousContent', 'content_'.$this->id, 'content_previousContent_'.$this->id])
            ->remember($key, config('constants.CACHE_600'), function () {
                $set = $this->set;
                if (!isset($set)) {

                    return isset($previousContent) ? $previousContent : null;
                }
                $previousContent = $set->activeContentsForApiV2()
                    ->where('contenttype_id', $this->contenttype_id)
                    ->where('order', '<=', $this->order - 1)
                    ->orderByDesc('order')
                    ->first();


                return isset($previousContent) ? $previousContent : null;
            });
    }

    public function getPreviousContentAttribute()
    {
        return $this->getPreviousContent();
    }

    public function getNextUrlAttribute($value)
    {
        return ($this->getNextContent() ?: new Content())->url;
    }

    public function getNextContent()
    {
        $key = 'content:nextContent'.$this->cacheKey();

        return Cache::tags(['content', 'nextContent', 'content_'.$this->id, 'content_'.$this->id.'_nextContent'])
            ->remember($key, config('constants.CACHE_600'), function () {
                $set = $this->set;
                $nextContent = $set?->activeContents([])
                    ->where('contenttype_id', $this->contenttype_id)
                    ->where('order', '>=', $this->order + 1)
                    ->orderBy('order')
                    ->first();
                return ($nextContent) ?? null;
            });
    }

    public function getNextContentForAPIV2()
    {
        $key = 'content:getNextContentForAPIV2'.$this->cacheKey();

        return Cache::tags(['content', 'nextContent', 'content_'.$this->id, 'content_'.$this->id.'_nextContent'])
            ->remember($key, config('constants.CACHE_600'), function () {
                $set = $this->set;
                $nextContent = $set?->activeContentsForApiV2()
                    ->where('contenttype_id', $this->contenttype_id)
                    ->where('order', '>=', $this->order + 1)
                    ->orderBy('order')
                    ->first();


                return ($nextContent) ?? null;
            });
    }

    public function getNextContentAttribute()
    {
        return $this->getNextContent();
    }

    public function getApiUrlAttribute($value): array
    {
        if (isset($this->id)) {
            return [
                'v1' => route('api.v1.content.show', $this),
//                'v2' => route('api.v2.content.show' , $this)
            ];

        }

        return [
            'v1' => '',
        ];
    }

    public function getApiUrlV1Attribute()
    {
        return route('api.v1.content.show', $this);
    }

    public function getApiUrlV2Attribute($value)
    {
        return appUrlRoute('api.v2.content.show', $this->id);
    }

    public function getPreviousApiUrlAttribute($value)
    {
        return ($this->getPreviousContent() ?: new Content())->api_url;
    }

    public function getNextApiUrlAttribute($value)
    {
        return ($this->getNextContent() ?: new Content())->api_url;
    }

    /**
     * Get the content's title .
     *
     * @param $value
     *
     * @return string
     */
    public function getTitleAttribute($value): string
    {
        return Purify::clean($value, self::PURIFY_NULL_CONFIG);
    }

    /**
     * Get the content's description .
     *
     * @param $value
     *
     * @return string
     */
    public function getDescriptionAttribute($value): string
    {
        return Purify::clean($value);
    }

    /**
     * Get the content's name .
     *
     * @param $value
     *
     * @return string
     */
    public function getNameAttribute($value): string
    {
        return Purify::clean($value, self::PURIFY_NULL_CONFIG);
    }

    /**
     * Get the content's meta title .
     *
     * @param $value
     *
     * @return string
     */
    public function getMetaTitleAttribute($value): string
    {
        if (isset($value[0])) {
            return $this->getCleanTextForMetaTags($value);
        }

        return mb_substr($this->getCleanTextForMetaTags($this->display_name), 0, config('constants.META_TITLE_LIMIT'),
            'utf-8');
    }

    /**
     * @param  string  $text
     *
     * @return mixed
     */
    private function getCleanTextForMetaTags(string $text)
    {
        return Purify::clean($text, self::PURIFY_NULL_CONFIG);
    }

    /**
     * Get the content's meta description .
     *
     * @param $value
     *
     * @return string
     */
    public function getMetaDescriptionAttribute($value): string
    {
        if (isset($value[0])) {
            return $this->getCleanTextForMetaTags($value);
        }
        $value =
            mb_substr($this->getCleanTextForMetaTags($this->description.' '.$this->getSetName().' '.$this->displayName),
                0, config('constants.META_TITLE_LIMIT'), 'utf-8');
        return str_replace(["\n", "\r"], '', $value);
    }

    public function getSetName()
    {
        $set = $this->set;
        return $set?->name;
    }

    /**
     * Get the content's files .
     *
     * @param $value
     *
     * @return Collection
     */
    public function getFileAttribute($value): ?Collection
    {

        if (!is_null($this->file_cache)) {
            return $this->file_cache;
        }

        $user = auth()->user();
        $disk = null;
        if ($user?->hasHalfPriceService) {
            $disk = $this->getHalfPriceDisk();
        }

        $key = "content:disk:$disk:file:".$this->cacheKey();
        $this->file_cache = Cache::tags(['content', 'file', 'content_'.$this->id, 'content_'.$this->id.'_file'])
            ->remember($key, config('constants.CACHE_600'), function () use ($value, $disk) {
                $content = $this->getFileResource();
                $value = $content->getRawOriginal('file');

                return $this->getFileCollection($content, 'getLinks', $value, $disk ?? null, unsetFromItem: [
                    'fileName', 'disk', 'url'
                ]);
            });

        return $this->file_cache;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id', 'id')
            ->withDefault();
    }

    private function getHalfPriceDisk()
    {
        if ($this->isFree) {
            return config('disks.HALF_PRICE_FREE_DISK');
        }
        return config('disks.HALF_PRICE_PAID_DISK');
    }

    protected function getFileResource()
    {
        return $this->copied_from !== null ? self::find($this->copied_from) : $this;
    }

    private function getFileCollection(
        Content $content,
        string $getLinksMethod,
        $value,
        ?string $customDisk = null,
        $turnOffEncryption = false,
        array $unsetFromItem = []
    ) {
        $fileCollection = collect(json_decode($value));
        $fileCollection->transform(function ($item, $key) use (
            $content,
            $getLinksMethod,
            $customDisk,
            $turnOffEncryption,
            $unsetFromItem
        ) {
            $item->disk = $customDisk ?? $item->disk;
            $l = new LinkGenerator($item);
            $item->link = $l->$getLinksMethod($content, function (string $fileName, ?string $quality) {
                if (!$this->isFree) {
                    return $this->makePaidContentFileName($fileName);
                }

                if ($this->isPamphlet()) {
                    return $fileName;
                }

                return $this->makeFreeContentFileName($this->contentset_id, $fileName, $quality);
            }, !($turnOffEncryption) && !$this->isFree);

            foreach ($unsetFromItem as $key) {
                unset($item->$key);
            }
            if (in_array($item->type, ['pamphlet', 'voice'])) {
                unset($item->res);
            }
            return $item;
        });

        return $fileCollection->count() > 0 ? $fileCollection->groupBy('type') : null;
    }

    private function makePaidContentFileName(string $fileName)
    {
        return str_ireplace('/paid/', '', $fileName);
    }

    public function isPamphlet(): bool
    {
        return (int) $this->contenttype_id === self::CONTENT_TYPE_PAMPHLET;
    }

    private function makeFreeContentFileName(?string $contentSetId, string $fileName, ?string $quality)
    {
        $qualitySubFolder = $quality ? $quality.'/' : '';
        return $contentSetId.'/'.$qualitySubFolder.$fileName;
    }

    /**
     * Get the content's files .
     *
     * @param $value
     *
     * @return Collection
     */
    public function getFileForAppAttribute(): ?Collection
    {
        return $this->fileGetter('file');
    }

    private function fileGetter($column)
    {
        $value = $this->getRawOriginal($column);
        $key = 'content:'.$column.'ForApp'.$this->cacheKey();

        return Cache::tags([
            'content', 'file', $column.'ForApp', 'content_'.$this->id, 'content_'.$this->id.'_file',
            'content_'.$this->id.'_'.$column.'ForApp'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($value, $column) {
                $content = $this->getFileResource();
                $value = $content->getRawOriginal($column);

                return $this->getFileCollection($content, 'getLinksForApp', $value, unsetFromItem: [
                    'fileName', 'disk', 'url'
                ]);
            });
    }

    public function getStreamForAppAttribute()
    {
        return $this->fileGetter('stream');
    }

    /**
     * Get the content's files for admin.
     *
     * @param $value
     *
     * @return Collection
     */

    public function getFileForAdminAttribute(): ?Collection
    {
        $content = $this->getFileResource();
        $value = $content->getRawOriginal('file');

        return $this->getFileCollection($content, 'getLinks', $value, turnOffEncryption: true);
    }

    public function getTimesAttribute()
    {
        if (!is_null($this->timepoints_cache)) {
            return $this->timepoints_cache;
        }
        $key = 'content:timepoints:'.$this->cacheKey();

        $this->timepoints_cache =
            Cache::tags(['content', 'content_'.$this->id, 'content_'.$this->id.'_timepoints'])
                ->remember($key, config('constants.CACHE_600'), function () {
                    return $this->timepoints()->orderBy('time')->get();
                });
        return $this->timepoints_cache;
    }

    public function timepoints()
    {
        return $this->hasMany(Timepoint::Class);
    }

    public function getFavoredTimesAttribute()
    {
        $user = null;
        if (auth()->check()) {
            $user = auth()->user();
        }

        $key = 'content:favoredTimepoints:';
        if (isset($user)) {
            $key = $key.'user-'.$user->id.':';
        }

        $key = $key.$this->cacheKey();

        return Cache::tags([
            'content', 'content_'.$this->id, 'content_'.$this->id.'_timepoints',
            'content_'.$this->id.'_favoredTimepoints'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($user) {
                $timepoints = $this->times;
                foreach ($timepoints as $key => $timepoint) {
                    $isFavored = (isset($user)) ? $user->hasFavoredTimepoint($timepoint) : false;

                    if (!$isFavored) {
                        $timepoints->pull($key);
                    }
                }

                return $timepoints->values();
            });
    }

    public function getOldTimesAttribute()
    {
        if (is_null($this->time_points)) {
            return null;
        }
        $value = $this->time_points;
        $value = json_decode($value);
        return $value->points;
    }

    /**
     * Get the content's thumbnail .
     *
     * @param $value
     *
     * @return array|null|string
     *
     * @throws Exception
     */
    public function getThumbnailAttribute($value)
    {
        $t = json_decode($value);
        $link = null;
        if (isset($t)) {
            $link = new LinkGenerator($t);
        }

        $thumbnailUrl = $link?->getLinks($this, function (string $fileName) {
            return $fileName ? "{$this->contentset_id}/$fileName" : 'Alaa_Narenj.jpg';
        }, false);

        if (is_null($thumbnailUrl)) {
            if ($this->contenttype_id != self::CONTENT_TYPE_VIDEO) {
                return null;
            }

            $setPhoto = $this->set?->photo;
            if (isset($setPhoto)) {
                return $setPhoto;
            }

            return 'https://nodes.alaatv.com/media/thumbnails/Alaa_Narenj.jpg';
        }

        return $thumbnailUrl;

    }

    public function getThumbnailForAdminAttribute()
    {
        $value = $this->getRawOriginal('thumbnail');

        $t = json_decode($value);
        $link = null;
        if (!isset($t)) {
            return $link;
        }

        $link = new LinkGenerator($t);

        return $link->getLinks($this, function (string $fileName) {
            return $fileName ? "{$this->contentset_id}/$fileName" : null;
        }, false);
    }

    public function getAuthorAttribute(): User
    {
        if (!is_null($this->content_author_cache)) {
            return $this->content_author_cache;
        }
        $content = $this;
        $key = 'content:author'.$content->cacheKey();

        $this->content_author_cache = Cache::tags([
            'content', 'author', 'content_'.$content->id, 'content_'.$content->id.'_author'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($content) {

                $visibleArray = [
                    'id',
                    'firstName',
                    'lastName',
                    'photo',
                    'full_name',
                ];
                $user = $this->user ?: User::getNullInstant($visibleArray);
                return $user->setVisible($visibleArray);
            });
        return $this->content_author_cache;
    }

    public function getAuthorNameAttribute(): ?string
    {
        $author = $this->author;
        return isset($author) ? $author->full_name : '';
    }

    /**
     * Get the content's tags .
     *
     * @param $value
     *
     * @return mixed
     */
    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Mutator
    |--------------------------------------------------------------------------
    */

    /**
     * Get the content's session .
     *
     * @return int|null
     */
    public function getSessionAttribute()
    {
        return $this->order;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Gets content's pamphlets
     *
     * @return Collection
     */
    public function getPamphlets(): Collection
    {
        $file = $this->file;
        if ($file === null) {

            return collect();
        }
        $pamphlet = $file->get('pamphlet');

        return isset($pamphlet) ? $pamphlet : collect();
    }

    /**
     * Gets content's voices
     *
     * @return Collection
     */
    public function getVoices(): Collection
    {
        $file = $this->file;
        if ($file === null) {

            return collect();
        }
        $voice = $file->get('voice');

        return isset($voice) ? $voice : collect();
    }

    /**
     * Gets content's videos
     *
     * @return Collection
     */
    public function getVideos(): Collection
    {
        $file = $this->file;
        if ($file === null) {
            return collect();
        }
        $video = $file->get('video');

        return isset($video) ? $video : collect();
    }

    /**
     * Gets content's set mates (contents which has same content set as this content
     *
     * @return mixed
     */
    public function getSetMates()
    {
        $contentSet = $this->set;
        $contentSetName = $this->getSetName();
        if (isset($contentSet)) {
            $sameContents = $contentSet->getActiveContents()
                ->sortBy('order')
                ->load('contenttype');
        } else {
            $sameContents = new ContentCollection([]);
        }
        return [
            $sameContents,
            $contentSetName,
        ];
    }

    public function getIsFavoredAttribute()
    {
        if (!is_null($this->is_favored_cache)) {
            return $this->is_favored_cache;
        }
        $authUser = auth()->user();
        if (!isset($authUser)) {
            return false;
        }
        $this->is_favored_cache = $authUser->hasFavoredContent($this);
        return $this->is_favored_cache;
    }

    /**
     * Gets content's display name
     *
     * @return string
     * @throws Exception
     */
    public function getDisplayNameAttribute(): string
    {
        if (!is_null($this->display_name_cache)) {
            return $this->display_name_cache;
        }
        $c = $this;
        $displayName = '';
        $sessionNumber = $c->order;
        if (isset($c->contenttype)) {
            $displayName .= $c->contenttype->displayName.' ';
        }
        $displayName .= (isset($sessionNumber) && $sessionNumber > -1 && $c->contenttype_id !==
            self::CONTENT_TYPE_ARTICLE ?
                'جلسه '
                .$sessionNumber.' - ' : '')
            .' '.($c->name ?? $c->user->name);

        $this->display_name_cache = $displayName;
        return $this->display_name_cache;
    }

    /**
     * Gets content's meta tags array
     *
     * @return array
     */
    public function getMetaTags(): array
    {
        $file = $this->file ?: collect();
        $videoDirectUrl = $file->where('res', '480p') ?: collect();
        $videoDirectUrl = $videoDirectUrl->first();
        $videoDirectUrl = isset($videoDirectUrl) ? $videoDirectUrl->link : null;

        $seoModLookupTable = [
            self::CONTENT_TYPE_VIDEO => SeoMetaTagsGenerator::SEO_MOD_VIDEO_TAGS,
            self::CONTENT_TYPE_PAMPHLET => SeoMetaTagsGenerator::SEO_MOD_PDF_TAGS,
            self::CONTENT_TYPE_EXAM => SeoMetaTagsGenerator::SEO_MOD_GENERAL_TAGS,
            self::CONTENT_TYPE_BOOK => SeoMetaTagsGenerator::SEO_MOD_GENERAL_TAGS,
            self::CONTENT_TYPE_ARTICLE => SeoMetaTagsGenerator::SEO_MOD_ARTICLE_TAGS,
        ];
        return [
            'title' => $this->metaTitle,
            'description' => $this->metaDescription,
            'url' => action('Web\ContentController@show', $this),
            'canonical' => action('Web\ContentController@show', $this),
            'site' => 'آلاء',
            'imageUrl' => $this->thumbnail,
            'imageWidth' => '1280',
            'imageHeight' => '720',
            'seoMod' => $seoModLookupTable[$this->contenttype_id],
            'playerUrl' => action('Web\ContentController@embed', $this),
            'playerWidth' => '854',
            'playerHeight' => '480',
            'videoDirectUrl' => $videoDirectUrl,
            'videoActorName' => $this->authorName,
            'videoActorRole' => 'دبیر',
            'videoDirector' => 'آلاء',
            'videoWriter' => 'آلاء',
            'videoDuration' => $this->duration,
            'videoReleaseDate' => $this->validSince,
            'tags' => $this->tags,
            'videoWidth' => '854',
            'videoHeight' => '480',
            'videoType' => 'video/mp4',
            'articleAuthor' => $this->authorName,
            'articleModifiedTime' => $this->updated_at,
            'articlePublishedTime' => $this->validSince,
        ];
    }

    /**
     * Set the content's thumbnail.
     *
     * @param $input
     *
     * @return void
     */
    public function setThumbnailAttribute($input)
    {
        if (is_null($input)) {
            $this->attributes['thumbnail'] = null;
        } else {
            $this->attributes['thumbnail'] = json_encode($input, JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Set the content's file.
     *
     * @param  Collection  $input
     *
     * @return void
     */
    public function setFileAttribute(Collection $input = null)
    {
        $this->attributes['file'] = optional($input)->toJson(JSON_UNESCAPED_UNICODE);
    }

    /**
     * Set the content's tag.
     *
     * @param  array  $value
     *
     * @return void
     */
    public function setTagsAttribute(array $value = null)
    {
        $tags = null;
        if (!empty($value)) {
            $tags = json_encode([
                'bucket' => 'content',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }

    /**
     * every products that have this content.
     *
     * @return ProductCollection
     */
    public function activeProducts(): ProductCollection
    {
        $key = 'content:activeProducts:'.$this->cacheKey();
        return Cache::tags(['content', 'product', 'content_'.$this->id, 'content_'.$this->id.'_activeProducts'])
            ->remember($key, config('constants.CACHE_60'), function () {
                return $this->set->getProducts()
                    ->makeHidden([
                        'shortDescription',
                        'longDescription',
                        'tags',
                        'introVideo',
                        'order',
                        'page_view',
                        'gift',
                        'type',
                        'attributes',
                        'samplePhotos',
                        'sets',
                        'product_set',
                        'children',
                        'updated_at',
                        'amount',

                    ]);
            });

    }

    public function grades()
    {
        //ToDo : deprecated
        return $this->belongsToMany(Grade::class);
    }

    public function majors()
    {
        //ToDo : deprecated
        return $this->belongsToMany(Major::class);
    }

    public function thumbnails()
    {
        return $this->files()
            ->where('label', '=', 'thumbnail');
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'educationalcontent_file', 'content_id', 'file_id')
            ->withPivot('caption', 'label');
    }

    public function template()
    {
        return $this->belongsTo(Template::class)
            ->withDefault();
    }

    /*
    |--------------------------------------------------------------------------
    |  Checkers (boolean)
    |--------------------------------------------------------------------------
    */

    public function getSourcesAttribute()
    {
        if (!is_null($this->sources_cache)) {
            return $this->sources_cache;
        }
        $this->sources_cache = $this->sources()->get();
        return $this->sources_cache;
    }

    /**
     * Get all of the tags for the post.
     */
    public function sources()
    {
        return $this->morphToMany(Source::Class, 'sourceable')->withTimestamps();
    }

    public function contenttype()
    {
        return $this->belongsTo(Contenttype::class)
            ->withDefault();
    }

    public function section()
    {
        return $this->belongsTo(Section::Class);
    }

    /**
     * Get the content's contentset .
     *
     * @return Contentset|BelongsTo
     */
    public function set()
    {
        return $this->belongsTo(Contentset::class, 'contentset_id', 'id')
            ->withDefault([
                'id' => 0,
                'url' => null,
                'apiUrl' => [
                    'v1' => null,
                ],
                'shortName' => null,
                'author' => [
                    'full_name' => null,
                ],
                'contentUrl' => null,
            ]);
    }

    public function mapDetails()
    {
        return $this->morphMany(MapDetail::class, 'entity');
    }

    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            'educationalcontent_plan',
            'content_id',
            'plan_id',
            'id',
            'id'
        )->withTimestamps()->withPivot('type_id');
    }

    public function status()
    {
        return $this->belongsTo(ContentsStatus::class, 'content_status_id');
    }

    /**
     * Fixes contents files (used in
     * /database/migrations/2018_08_21_143144_alter_table_educationalcontents_add_columns.php)
     *
     * @retuen void
     */
    public function fixFiles(): void
    {
        $content = $this;
        $files = collect();
        switch ($content->template->name) {
            case 'video1':
                $file = $content->files->where('pivot.label', 'hd')
                    ->first();
                if (isset($file)) {
                    $url = $file->name;
                    $size = null;
                    $caption = $file->pivot->caption;
                    $res = '720p';
                    $type = 'video';

                    $files->push([
                        'uuid' => $file->uuid,
                        'disk' => config('disks.ALAA_CDN_SFTP'),
                        'url' => $url,
                        'fileName' => parse_url($url)['path'],
                        'size' => $size,
                        'caption' => $caption,
                        'res' => $res,
                        'type' => $type,
                        'ext' => pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION),
                    ]);
                }

                $file = $content->files->where('pivot.label', 'hq')
                    ->first();
                if (isset($file)) {
                    $url = $file->name;
                    $size = null;
                    $caption = $file->pivot->caption;
                    $res = '480p';
                    $type = 'video';

                    $files->push([
                        'uuid' => $file->uuid,
                        'disk' => config('disks.ALAA_CDN_SFTP'),
                        'url' => $url,
                        'fileName' => parse_url($url)['path'],
                        'size' => $size,
                        'caption' => $caption,
                        'res' => $res,
                        'type' => $type,
                        'ext' => pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION),
                    ]);
                }

                $file = $content->files->where('pivot.label', '240p')
                    ->first();
                if (isset($file)) {
                    $url = $file->name;
                    $size = null;
                    $caption = $file->pivot->caption;
                    $res = '240p';
                    $type = 'video';

                    $files->push([
                        'uuid' => $file->uuid,
                        'disk' => config('disks.ALAA_CDN_SFTP'),
                        'url' => $url,
                        'fileName' => parse_url($url)['path'],
                        'size' => $size,
                        'caption' => $caption,
                        'res' => $res,
                        'type' => $type,
                        'ext' => pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION),
                    ]);
                }

                $file = optional($content->files->where('pivot.label', 'thumbnail')
                    ->first());

                $url = $file->name;
                if (isset($url)) {
                    $size = null;
                    $type = 'thumbnail';

                    $this->thumbnail = [
                        'uuid' => $file->uuid,
                        'disk' => config('disks.ALAA_CDN_SFTP'),
                        'url' => $url,
                        'fileName' => parse_url($url)['path'],
                        'size' => $size,
                        'caption' => null,
                        'res' => null,
                        'type' => $type,
                        'ext' => pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION),
                    ];
                }
                break;

            case  'pamphlet1':
                $pFiles = $content->files;
                foreach ($pFiles as $file) {
                    $type = 'pamphlet';
                    $res = null;
                    $caption = 'فایل'.' '.$file->pivot->caption;

                    if ($file->disks->isNotEmpty()) {
                        $disk = $file->disks->first();
                        $diskName = $disk->name;
                    }

                    $files->push([
                        'uuid' => $file->uuid,
                        'disk' => (isset($diskName) ? $diskName : null),
                        'url' => null,
                        'fileName' => $file->name,
                        'size' => null,
                        'caption' => $caption,
                        'res' => $res,
                        'type' => $type,
                        'ext' => pathinfo($file->name, PATHINFO_EXTENSION),
                    ]);
                }
                break;
            case 'article' :
                break;
            default:
                break;
        }

        //        dd($files);
        $this->file = $files;
        $this->updateWithoutTimestamp();

        Artisan::call('cache:clear');
    }

    /**
     * @return string
     */
    public function getEditLinkAttribute(): string
    {
        return route('c.edit', $this->id);
    }

    public function getRemoveLinkAttribute()
    {
        return action('Web\ContentController@destroy', $this->id);
    }

    /**
     * @return ProductCollection
     */
    public function getRelatedProductsAttribute(): ProductCollection
    {
        $content = $this;
        $key = 'content:relatedProduct:'.$content->cacheKey();
        $relatedProductSearch = new RelatedProductSearch();
        return Cache::tags([
            'content', 'relatedProduct', 'content_'.$content->id, 'content_'.$content->id.'_relatedProduct'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($content, $relatedProductSearch) {
                $filters = [
                    'tags' => ['Content-'.$content->id],
                ];
                $result = $relatedProductSearch->get($filters);
                $products = new ProductCollection();
                foreach ($result as $product) {
                    $products->push($product);
                }
                return $products;
            });
    }

    /**
     * @return ProductCollection
     */
    public function getRecommendedProductsAttribute(): ProductCollection
    {
        $content = $this;
        $key = 'content:recommendedProduct:'.$content->cacheKey();
        $recommendedProductSearch = new RecommendedProductSearch ();
        return Cache::tags([
            'content', 'recommendedProduct', 'content_'.$content->id, 'content_'.$content->id.'_recommendedProduct'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($content, $recommendedProductSearch) {
                $filters = [
                    'tags' => [$content->id],
                ];

                $result = $recommendedProductSearch->get($filters);
                $products = new ProductCollection();
                foreach ($result as $product) {
                    $products->push($product);
                }

                return $products;
            });
    }

    public function getRedirectUrlAttribute($value)
    {
        if (!isset($value)) {
            return null;
        }

        $value = json_decode($value);

        $url = isset($value->url) ? parse_url($value->url) : '';

        return [
            'url' => $url ? url($url['path']) : '',
            'code' => isset($value->code) ? $value->code : '',
        ];
    }

    public function getVastUrlAttribute()
    {
        return $this->validVast()?->url;
    }

    private function validVast()
    {
        $now = now('Asia/Tehran');
        $vast = $this->vasts()?->where(function ($query) use ($now) {
            $query->where(function ($q) use ($now) {
                $q->where('valid_since', '<', $now)->orWhereNull('valid_since');
            })->where(function ($q) use ($now) {
                $q->where('valid_until', '>', $now)->orWhereNull('valid_until');
            });
        })->first();

        if (!$vast) {
            $vast = $this->set
                ->first()
                ?->vasts()
                ?->where(function ($query) use ($now) {
                    $query->where(function ($q) use ($now) {
                        $q->where('valid_since', '<', $now)->orWhereNull('valid_since');
                    })->where(function ($q) use ($now) {
                        $q->where('valid_until', '>', $now)->orWhereNull('valid_until');
                    });
                })->first();
        }

        if (!$vast) {
            $vast = VastRepo::randomDefault()?->first();
        }

        if (!$vast) {
            return null;
        }

        return $vast;
    }

    public function vasts()
    {
        return $this->belongsToMany(Vast::class, 'content_vast', 'content_id', 'vast_id')
            ->withPivot(['created_at', 'valid_since', 'valid_until'])
            ->orderBy('created_at', 'desc');
    }

    public function getVastAttribute()
    {
        return $this->validVast() ?? null;
    }

    public function getCanSeeContent(?User $user): int
    {
        $roles = $user?->roles()->whereIn('name', [
            config('constants.ROLE_ADMIN'),
            config('constants.ROLE_PRODUCT_MANAGEMENT'),
            config('constants.ROLE_AD_MANAGER'),
            config('constants.ROLE_PROJECT_CONTROLLER'),
            config('constants.ROLE_PUBLIC_RELATION_EMPLOYEE'),
            config('constants.ROLE_PUBLIC_RELATION_MANAGER'),
            config('constants.ROLE_BONYAD_EHSAN_MANAGER'),

        ])->get();
        if (isset($roles) && $roles->isNotEmpty()) {
            return 1;
        }
        /**
         *   Outputs:
         *   0 => can't see content
         *   2 => it's not determine whether can see content or not
         *   1 => can see content
         */
        if ($this->isFree) {
            return 1;
        }

        if (is_null($user)) {
            return 2;
        }

        return $user->canSeeContent($this) ? 1 : 0;
    }

    public function setRedirectUrlAttribute($value)
    {
        $this->attributes['redirectUrl'] = !isset($value) ? null : json_encode($value);
    }

    public function attachSource($sourceId): bool
    {
        if ($this->sources->where('id', $sourceId)->isEmpty()) {
            $this->sources()->attach($sourceId);
            return true;
        }

        return false;
    }

    public function getShortDescription()
    {
        return mb_substr($this->getCleanTextForMetaTags($this->description), 0,
            config('constants.UI_META_DESCRIPTION_LIMIT'), 'utf-8');
    }

    public function contentInCome()
    {
        return $this->hasMany(ContentIncome::class, 'content_id');
    }

    /**
     * @return string|null
     */
    public function abrishamProductShortTitle(int $major = null, bool $attachLessonName = false): ?string
    {
        $contentSet = $this->set;
        $lessonName = optional($contentSet)->abrishamProductLessonName($major);
        return 'جلسه '."{$this->order} {$contentSet->small_name} ".($attachLessonName ? $lessonName : '');
    }

    public function productsIdArray(): array
    {
        return $this->set->products->flatten()->pluck('id')->toArray();
    }

    public function getCanUserUseTimepointAttribute()
    {
        $user = auth()->check() ? auth()->user() : null;
        $canUseTimePoint = false;
        if (!$this->isFree) {
            $canUseTimePoint = true;
        } else {
            if (isset($user)) {
                $userTimepointSubscription =
                    SubscriptionRepo::validProductSubscriptionOfUser($user->id,
                        Product::TIMEPOINT_SUBSCRIPTON_PRODUCTS);
                $canUseTimePoint = isset($userTimepointSubscription);
            }
        }
        return $canUseTimePoint;
    }

    public function danaContents()
    {
        return $this->hasMany(DanaProductContentTransfer::class, 'educationalcontent_id');
    }

    public function urlExist()
    {
        $files = $this->file_for_admin?->first();
        if (!isset($files)) {
            return false;
        }
        foreach ($files as $file) {

            if (url_exists($file->link)) {
                return true;
            }
        }
        return false;
    }

    public function getContentDuration(): ?int
    {
        $length = null;
        $basePath = config('constants.DOWNLOAD_SERVER_ROOT');

        $files = $this->file_for_app;
        if ($this->contenttype_id == Content::CONTENT_TYPE_VIDEO) {
            $videos = $files?->get('video');
            if (is_null($videos)) {
                return null;
            }

            foreach ($videos as $file) {
                $link = $file?->link;
                if (is_null($link)) {
                    continue;
                }

                $link = strtok($link, '?');
                $parseUrl = parse_url($link);
                $path = $parseUrl['path'];
                $url = 'http://127.0.0.1:8000'.$parseUrl['path'];
                $pathInfo = pathinfo($path);
                $extension = Arr::get($pathInfo, 'extension');
                if (!in_array($extension, ['mkv', 'mp4'])) {
                    continue;
                }
                $length = getVideoLength($url);
                if (!is_null($length)) {
                    break;
                }
            }

            return $length;
        }

        if ($this->contenttype_id == Content::CONTENT_TYPE_PAMPHLET) {
            $pamphlets = $files?->get('pamphlet');
            if (is_null($pamphlets)) {
                return null;
            }

            $pamphlet = $pamphlets[0];
            if (is_null($pamphlet)) {
                return null;
            }

            $link = $pamphlet?->link;
            if (is_null($link)) {
                return null;
            }

            $link = strtok($link, '?');
            $parseUrl = parse_url($link);
            $path = '/minio'.$parseUrl['path'];
            return countPDFPages($path);
        }

        return $length;
    }

    private function isRaheAbrishamContent(): bool
    {
        return Cache::tags([
            'content', 'content_'.$this->id
        ])->remember('content:isRaheAbrishamContent:'.$this->cacheKey(), config('constants.CACHE_600'), function () {
            return !$this->isFree && !empty(array_intersect($this->allProducts()->pluck('id')->toArray(),
                    array_keys(Product::ALL_ABRISHAM_PRODUCTS)));
        });
    }

    /**
     * every products that have this content.
     *
     * @return ProductCollection
     */
    public function allProducts(): ProductCollection
    {
        $key = 'content:products:'.$this->cacheKey();
        return Cache::tags(['content', 'product', 'content_'.$this->id, 'content_'.$this->id.'_products'])
            ->remember($key, config('constants.CACHE_60'), function () {
                return $this->set->getProducts(false)
                    ->makeHidden([
                        'shortDescription',
                        'longDescription',
                        'tags',
                        'introVideo',
                        'order',
                        'page_view',
                        'gift',
                        'type',
                        'attributes',
                        'samplePhotos',
                        'sets',
                        'product_set',
                        'children',
                        'updated_at',
                        'amount',
                    ]);
            });

    }
}
