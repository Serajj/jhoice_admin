<?php
/*
 * File name: BookingsOfUserCriteria.php
 * Last modified: 2021.05.07 at 19:12:31
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Criteria\Bookings;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BookingsOfUserCriteria.
 *
 * @package namespace App\Criteria\Bookings;
 */
class BookingsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $userId;

    /**
     * BookingsOfUserCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model;
        } else if (auth()->user()->hasRole('provider')) {
			
            $eProviderId = DB::raw("json_extract(e_provider, '$.id')");
			$providerBooking = DB::table('e_provider_users')->get()->where('user_id',"=",$this->userId );
			$i = 0;
			$provider_array = array();
			foreach($providerBooking as $providers)
			{
				$provider_array[] = $providers->e_provider_id ;
			}
			
			if(!empty($provider_array))
			{
				return $model->join("e_provider_users", "e_provider_users.e_provider_id", "=", $eProviderId)
							->where('e_provider_users.user_id', $this->userId)
							->orwhere('bookings.user_id', $this->userId)
							//->andwhere('bookings.user_id', "!=", 'e_provider_users.user_id')
							->groupBy('bookings.id')
							->select('bookings.*');
			}
			else
			{
				return $model->WhereIn('bookings.provider_id', $provider_array)
						 ->groupBy('bookings.id')
						 ->select('bookings.*');
			}
			
        } else if (auth()->user()->hasRole('customer')) {
            return $model->where('bookings.user_id', $this->userId)
                ->select('bookings.*')
                ->groupBy('bookings.id');
        } else {
            return $model;
        }
    }
}
