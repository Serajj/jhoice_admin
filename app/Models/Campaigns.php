<?php

namespace App\Models;

use Eloquent as Model;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Campaigns extends Model implements HasMedia
{
    /**
 * Class Category
 * @package App\Models
 * @version January 19, 2021, 2:04 pm UTC
 *
 * @property string name
 * @property string typevalidity
 * @property integer description
 * @property string validityType
 * @property string redirectUrl
 */
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:250',
        'type' => 'required|max:10',
        'validity' => 'required|max:10',
        'validityType' => 'required|max:10',
        'redirectUrl' => 'nullable',
    ];
    public $table = 'campaigns';
    public $fillable = [
        'name',
        'type',
        'validity',
        'validityType',
        'redirectUrl'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'=>'string',
        'type' => 'string',
        'validity' => 'integer',
        'validityType' => 'string',
        'redirectUrl' => 'string'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        // 'custom_fields',
        'has_media'
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "validityType",
        "validity"
    ];

    /**
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb');
           
        $this->addMediaConversion('icon');
    }

    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($collectionName = 'default', $conversion = '')
    {
        $url = $this->getFirstMediaUrlTrait($collectionName);
        $array = explode('.', $url);
        $extension = strtolower(end($array));
        if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
            return asset($this->getFirstMediaUrlTrait($collectionName, $conversion));
        } else {
            return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
        }
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute()
    {
        return $this->hasMedia('image') ? true : false;
    }
}
