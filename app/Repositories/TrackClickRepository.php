<?php
/*
 * File name: AddressRepository.php
 * Last modified: 2021.02.16 at 10:54:15
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Repositories;

use App\Models\TrackClick as AppTrackClick;
use InfyOm\Generator\Common\BaseRepository;

class TrackClickRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'service_id',
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AppTrackClick::class;
    }
}
