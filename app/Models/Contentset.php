<?php

namespace App\Models;

use App\Classes\FavorableInterface;
use App\Classes\LastWatch;
use App\Classes\SEO\SeoInterface;
use App\Classes\SEO\SeoMetaTagsGenerator;
use App\Classes\Taggable;
use App\Classes\Uploader\Uploader;
use App\Collection\ContentCollection;
use App\Collection\ProductCollection;
use App\Collection\SetCollection;
use App\Repositories\VastRepo;
use App\Services\ForrestService;
use App\Traits\CommentTrait;
use App\Traits\favorableTraits;
use App\Traits\MinioPhotoHandler;
use App\Traits\Set\TaggableSetTrait;
use App\Traits\WatchHistoryTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stevebauman\Purify\Facades\Purify;

class Contentset extends BaseModel implements Taggable, SeoInterface, FavorableInterface
{

    use MinioPhotoHandler;

    public const PHOTO_FIELD = 'photo';
    protected const PURIFY_NULL_CONFIG = ['HTML.Allowed' => ''];

    use favorableTraits;

//    use Searchable;
    use TaggableSetTrait;
    use CommentTrait;
    use WatchHistoryTrait;
    use LogsActivity;

    public const NEXT_WATCH_CONTENT_NOT_CONTAIN_CONTENT_SET_STRINGS = [
        'زین بی قرار',
        'پیش آزمون',
        'پس آزمون',
        'مشاوره',
    ];
    public const PARACHUTE_SET_ID = 2294;
    public const ABRISHAM_MOSHAVERE_SE_ID = 1213;
    public const NAHAYI_1402_MOSHAVERE_SET_ID = 2820;
    public const INDEX_PAGE_NAME = 'setPage';
    public const SET_MOSHAVERE_STRATEGY_100 = 1210;
    public const SET_MOSHAVERE_MIND_BUILDER_CLUB = 1211;
    protected static $recordEvents = ['updated', 'deleted', 'created'];
    protected static $console_description = ' from console';
    public int $plan_major_id;
    /**
     * @var array
     */
    protected $fillable = [
        'redirectUrl',
        'author_id',
        'name',
        'small_name',
        'description',
        'photo',
        'enable',
        'display',
        'forrest_tree_grid',
        'forrest_tree_tags',
    ];

    // TODO: Check the comments below, if their presence in the "protected $hidden" array is important. Commented Those by Emad Naeimifar
    protected $casts = [
        'forrest_tree_grid' => 'array',
        'forrest_tree_tags' => 'array',
    ];
    protected $withCount = [
        'contents',
        'activeContents',
    ];
    protected $appends = [
        'url',
        'apiUrl',
        'shortName',
        'author',
        'contentUrl',
        'setUrl',
    ];
    protected $hidden = [
        'deleted_at',
        'small_name',
        'pivot',
        'productSet',
    ];
    /**
     * @var array|mixed
     */
    protected $author_cache;

    protected $cachedMethods = [
        'getAuthorAttribute'
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return SetCollection
     */
    public function newCollection(array $models = [])
    {
        return new SetCollection($models);
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

    public function isActive()
    {
        return $this->isEnable();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function isEnable(): bool
    {
        if ($this->enable) {
            return true;
        }

        return false;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        $unSetArrayItems = [
            'tags',
            'photo',
            'url',
            'apiUrl',
            'shortName',
            'author',
            'contentUrl',
            'deleted_at',
            'small_name',
            'pivot',
            'enable',
            'display',
            'updated_at',
            'created_at',
        ];
        foreach ($unSetArrayItems as $item) {
            unset($array[$item]);
        }
        return $array;
    }

    /**
     * Scope a query to only include active Contentsets.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('enable', 1);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('small_name', 'regexp', "-\s*$keyword\s*-");
    }

    public function scopeDisplay($query)
    {
        return $query->where('display', 1);
    }

    public function scopeIds($query, array $ids)
    {
        return $query->whereIn('id', $ids);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function scopeRedirected($query)
    {
        return $query->whereNotNull('redirectUrl');
    }

    public function scopeNotRedirected($query)
    {
        return $query->whereNull('redirectUrl');
    }

    public function scopeNameDoesNotContain($query, array $strings)
    {
        foreach ($strings as $string) {
            $query->where('name', 'NOT LIKE', "%{$string}%");
        }
    }

    public function scopeSmallNameDoesNotContain($query, array $strings)
    {
        foreach ($strings as $string) {
            $query->where('small_name', 'NOT LIKE', "%{$string}%");
        }
    }

    public function getContentUrlAttribute($value)
    {
        return action('Web\ContentController@index', [
            'set' => $this->id,
            'contentOnly' => true,
            'free' => [0, 1],
        ]);
    }

    public function getActiveContentsBySectionAttribute()
    {
        return $this->getActiveContents2()->groupBy('section.name');
    }

    public function getActiveContents2(int $type = null)
    {
        $key = 'set:getActiveContents2:type_'.$type.':'.$this->cacheKey();
        return Cache::tags([
            'set', 'activeContent', 'set_'.$this->id, 'set_'.$this->id.'_contents', 'set_'.$this->id.'_activeContents'
        ])
            ->remember($key, config('constants.CACHE_300'), function () use ($type) {
                $contents = $this->activeContents();
                if (isset($type)) {
                    $contents->type($type);
                }
                $contents = $contents->get()
                    ->sortBy('order');
                /** @var Content $content */
                foreach ($contents as $content) {
                    $content->attacheCachedMethodResult();
                }
                return $contents;
            });
    }

    public function activeContents($with = ['section'])
    {
        return $this->contents()->with($with)->active();
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */

    public function getActiveContents2ForAPIV2(int $type = null)
    {
        $key = 'set:getActiveContents2ForAPIV2:type_'.$type.':'.$this->cacheKey();
        return Cache::tags([
            'set', 'activeContent', 'set_'.$this->id, 'set_'.$this->id.'_contents', 'set_'.$this->id.'_activeContents'
        ])
            ->remember($key, config('constants.CACHE_300'), function () use ($type) {
                $contents = $this->activeContentsForApiV2();

                if (isset($type)) {
                    $contents->type($type);
                }

                $contents = $contents->get()
                    ->sortBy('order');
                /** @var Content $content */
                foreach ($contents as $content) {
                    $content->attacheCachedMethodResult();
                }
                return $contents;
            });
    }

    public function activeContentsForApiV2()
    {
        return $this->contents()->with('section')->active()->where('file', 'NOT LIKE', '%.mp3%');
    }

    /**
     * Get all of the tags for the post.
     */
    public function sources()
    {
        return $this->morphToMany(Source::Class, 'sourceable')->withTimestamps()
            ->withPivot(['order']);
    }


    //new way ( after migrate )

    public function getLastContentUserWatched()
    {
        $lastWatch = new LastWatch(auth()->user(), 'set', $this->id);
        return $lastWatch->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function sections()
    {
        $sections = collect();

        $this->contents
            ->each(function (Content $content) use ($sections) {
                $section = $content->section;
                $sections->when($section)->push($section);
            });

        return $sections;
    }

    public function getVastUrlAttribute()
    {
        return $this->validVast()?->url;
    }

    private function validVast()
    {
        $now = now('Asia/Tehran');
        $vast = $this?->vasts()
            ?->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->where('valid_since', '<', $now)->orWhereNull('valid_since');
                })->where(function ($q) use ($now) {
                    $q->where('valid_until', '>', $now)->orWhereNull('valid_until');
                });
            })->first();

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
        return $this->belongsToMany(Vast::class, 'set_vast', 'set_id', 'vast_id')
            ->withPivot(['created_at', 'valid_since', 'valid_until'])
            ->orderBy('created_at', 'desc');
    }

    public function getVastAttribute()
    {
        return $this->validVast() ?? null;
    }

    public function getProducts($onlyActiveProduct = true): ProductCollection
    {
        $onlyActiveString = ($onlyActiveProduct) ? '1' : '0';
        $key = 'set:getProducts:onlyActive_'.$onlyActiveString.':'.$this->cacheKey();
        return Cache::tags(['set', 'product', 'set_'.$this->id, 'set_'.$this->id.'_products'])
            ->remember($key, config('constants.CACHE_60'), function () use ($onlyActiveProduct) {
                return self::getProductOfSet($onlyActiveProduct, $this);
            });
    }

    /**
     * @param  bool  $onlyActiveProduct
     * @param  Contentset  $set
     *
     * @return ProductCollection
     */
    public static function getProductOfSet(bool $onlyActiveProduct, Contentset $set): ProductCollection
    {
        return ($onlyActiveProduct ? $set->products()
            ->active()
            ->get() : $set->products()
            ->get()) ?: new
        ProductCollection();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot([
                'order',
            ])
            ->withTimestamps()
            ->orderBy('order');
    }

    public function mapDetails()
    {
        return $this->morphMany(MapDetail::class, 'entity');
    }

    public function getShortNameAttribute($value)
    {
        return $this->small_name;
    }

    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Set the set's tag.
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
                'bucket' => 'contentset',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }


    public function getLastActiveContent(): Content
    {
        $key = 'set:getLastActiveContent:'.$this->cacheKey();

        return Cache::tags([
            'set', 'activeContent', 'lastActiveContent', 'set_'.$this->id, 'set_'.$this->id.'_contents',
            'set_'.$this->id.'_activeContents', 'set_'.$this->id.'_lastActiveContent'
        ])
            ->remember($key, config('constants.CACHE_300'), function () {

                $r = $this->getActiveContents();
                $lastContent = $r->sortByDesc('order')->first();

                return $lastContent ?: new Content();
            });
    }

    public function getActiveContents(): ContentCollection
    {
        $key = 'set:getActiveContents:'.$this->cacheKey();

        return Cache::tags([
            'set', 'activeContent', 'set_'.$this->id, 'set_'.$this->id.'_contents', 'set_'.$this->id.'_activeContents'
        ])
            ->remember($key, config('constants.CACHE_300'), function () {

                $contents = $this->contents()->without([
                    'user',
                    'set',
                    'template',
                ])
                    ->active()
                    ->orderBy('order')
                    ->get() ?: new ContentCollection();
                /** @var Content $content */
                foreach ($contents as $content) {
                    $content->attacheCachedMethodResult();
                }

                return $contents;
            });
    }


    public function getLastContent(): Content
    {
        $key = 'set:getLastContent:'.$this->cacheKey();

        return Cache::tags([
            'set', 'content', 'lastContent', 'set_'.$this->id, 'set_'.$this->id.'_contents',
            'set_'.$this->id.'_lastContent'
        ])
            ->remember($key, config('constants.CACHE_300'), function () {

                $r = $this->getContents();

                return $r->sortByDesc('order')->first()?->append(['author',]) ?: new Content();
            });
    }

    public function getContents(): ContentCollection
    {
        $key = 'set:getContents:'.$this->cacheKey();

        return Cache::tags(['set', 'content', 'set_'.$this->id, 'set_'.$this->id.'_contents'])
            ->remember($key, config('constants.CACHE_300'), function () {

                return $this->contents()
                    ->get() ?: new ContentCollection();
            });
    }


    public function getApiUrlV2Attribute($value)
    {
        return appUrlRoute('api.v2.set.show', $this->id);
    }

    public function getRedirectUrlAttribute($value)
    {
        if (!isset($value)) {
            return null;
        }

        $value = json_decode($value);


        $url = isset($value->url) ? parse_url($value->url) : null;

        return [
            'url' => $url ? url($url['path']) : null,
            'code' => isset($value->code) ? $value->code : null,
        ];
    }

    public function setRedirectUrlAttribute($value)
    {
        $this->attributes['redirectUrl'] = !isset($value) ? null : json_encode($value);
    }

    /**
     * @param $value
     *
     * @return User|null
     */
    public function getAuthorAttribute(): ?User
    {
        $set = $this;
        $key = 'set:author-'.$set->cacheKey();

        if (!is_null($this->author_cache)) {
            return $this->author_cache;
        }
        $this->author_cache = Cache::tags(['set', 'author', 'set_'.$set->id, 'set_'.$set->id.'_author'])
            ->remember($key, config('constants.CACHE_600'), function () use ($set) {

                if (is_null($set->author_id)) {
                    return null;
                }

                $visibleArray = [
                    'id',
                    'firstName',
                    'lastName',
                    'photo',
                    'full_name',
                ];

                $author = $set->user;

                return $author?->setVisible($visibleArray);
            });
        return $this->author_cache;

    }

    public function getRemoveLinkAttribute()
    {
//        if (hasAuthenticatedUserPermission(config('constants.REMOVE_BLOCK_ACCESS')))
//            return action('Web\BlockController@destroy', $this->id);

        return null;
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

        return mb_substr('فیلم و جزوه های '.$this->getCleanTextForMetaTags($this->name).' | آلاء', 0,
            config('constants.META_TITLE_LIMIT'),
            'utf-8');
    }

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
        return mb_substr($this->getCleanTextForMetaTags($this->description.' '.$this->metaTitle),
            0, config('constants.META_TITLE_LIMIT'), 'utf-8');
    }

    public function getMetaTags(): array
    {
        return [
            'seoMod' => SeoMetaTagsGenerator::SEO_MOD_GENERAL_TAGS,
            'title' => $this->getCustomizedMetaTitle(),
            'description' => $this->getCustomizedMetaDescription(),
            'url' => action([SetController::class, 'show'], $this),
            'canonical' => action([SetController::class, 'show'], $this),
            'site' => 'آلاء',
            'imageUrl' => $this->photo,
            'imageWidth' => '1280',
            'imageHeight' => '720',
        ];
    }

    public function getCustomizedMetaTitle()
    {
        switch ($this->id) {
            case 1226 :
                return 'مشاوره رایگان انتخاب رشته کنکور 1400';
            case 1241 :
                return 'ویدئوی مشاوره انتخاب رشته کنکور';
            default:
                return $this->metaTitle;
        }
    }

    public function getCustomizedMetaDescription()
    {
        switch ($this->id) {
            case 1226 :
                return 'مشاوره ویدیویی رایگان انتخاب رشته صحیح کنکور1400
تحلیل کارنامۀ کنکور،نکات طلایی کارنامه و دفترچه کنکور،دانشگاه ها و دوره ها، اشتباهات رایج انتخاب رشته';
            case 1241 :
                return 'مشاوره ویدیویی رایگان انتخاب رشته کنکور
بررسی تخصصی رشته های تجربی و ریاضی با بهره گیری از اساتید دانشگاه';
            default:
                return $this->metaDescription;
        }
    }

    // todo: uncomment this after running MoveImagesFromMariadbToMinio migration

    public function getIsFavoredAttribute()
    {
        $authUser = auth()->user();
        if (!isset($authUser)) {
            return false;
        }

        return Cache::tags([
            'favorite', 'user', 'user_'.$authUser->id, 'user_'.$authUser->id.'_favorites',
            'user_'.$authUser->id.'_favoriteSets'
        ])
            ->remember('user:'.$authUser->id.':hasFavored:set:'.$this->cacheKey(), config('constants.CACHE_10'),
                function () use ($authUser) {
                    return $authUser->hasFavoredSet($this);
                });
    }

    public function getContentsLinkAttribute()
    {
        return route('web.set.list.contents', $this->id);
    }

    public function getPhotoAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Uploader::url(config('disks.SET_IMAGE_MINIO'), $value);
    }

    public function abrishamProductLessonName(?int $major): ?string
    {
        $abrishamProductCategory =
            nestedArraySearchWithKey(Product::ABRISHAM_PRODUCTS_CATEGORY, $major ?? -1, 'user_major_category');

        $product = $this->products()
            ->whereIn('id', Arr::get($abrishamProductCategory, 'products'))
            ->belongsToAbrishamProducts()
            ->first();

        if (!is_null(optional($product)->id)) {
            $lessonInfo = Arr::get(Product::ALL_ABRISHAM_PRODUCTS, $product->id);
            return Arr::get($lessonInfo, 'lesson_name');
        }

        return null;
    }

    public function getImageAttribute()
    {
        return $this->getRawOriginal('photo');
    }

    public function saveWithoutEvents(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        $model = explode('\\', self::class)[1];
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName
            ) => (auth()->check()) ? $eventName : $eventName.self::$console_description)
            ->useLogName("{$model}");
    }

    public function getActiveContentsDurationAttribute()
    {
        return $this->activeContents()->pluck('duration')->sum();
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
}
