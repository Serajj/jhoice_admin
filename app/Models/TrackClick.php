<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;  
use App\Models\Booking; 

class TrackClick extends Model
{

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'service_id' => 'required',
        'type' => 'required|max:10',
    ];
    public $fillable = [
        'user_id',
        'service_id',
        'type'
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $table = 'track_clicks';  
    // protected $primaryKey = 'id'; 
    public function user()
    {
        return $this->belongsTo(User::class);
    } 
    public function service()
    {
        return $this->belongsTo(EService::class);
    }
    public function getTotalBookingByProvider(){
        return $this->select("track_clicks.*","s.name")->leftJoin('e_services as s','service_id','=','s.id')
        ->with('service')
        ->groupBy('s.e_provider_id');
    }
    public function getTotalBookingByProviderServices($providerid){
        return $this->select("track_clicks.*","s.name")->leftJoin('e_services as s','service_id','=','s.id')
        ->with('service')->where('s.e_provider_id',$providerid)->groupBy('s.id');;
    }
    public function getCountByType($type,$provider)
    {
        return $this->leftJoin('e_services as s','service_id','=','s.id')
        ->where('type',$type)->where('s.e_provider_id',$provider)->count();
    }
    public function getCountCompleted($provider)
    {
        return $this->leftJoin('e_services as s','service_id','=','s.id')->
        where('s.e_provider_id',$provider)->where('isBooked',true)->count();
    }


    public function getCountByTypeService($type,$service)
    {
        return $this->leftJoin('e_services as s','service_id','=','s.id')->where('type',$type)->where('s.id',$service)->count();
    }
    public function getCountCompletedService($service)
    {
        return $this->leftJoin('e_services as s','service_id','=','s.id')->where('s.id',$service)->where('isBooked',true)->count();
    }

    // public function getCustomFieldsAttribute()
    // {
    //     $hasCustomField = in_array(static::class, setting('custom_field_models', []));
    //     if (!$hasCustomField) {
    //         return [];
    //     }
    //     $array = $this->customFieldsValues()
    //         ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
    //         ->where('custom_fields.in_table', '=', true)
    //         ->get()->toArray();

    //     return convertToAssoc($array, 'name');
    // }

}
