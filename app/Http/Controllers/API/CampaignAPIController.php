<?php
/*
 * File name: CategoryAPIController.php
 * Last modified: 2021.03.24 at 21:33:26
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Criteria\Categories\NearCriteria;
use App\Criteria\Categories\ParentCriteria;
use App\Http\Controllers\Controller;
use App\Models\Campaigns;
use App\Models\Category;
use App\Repositories\CampaignsRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Maatwebsite\Excel\Concerns\ToArray;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CampaignAPIController
 * @package App\Http\Controllers\API
 */
class CampaignAPIController extends Controller
{
    /** @var  CampaignsRepository */
    private $campaignRepository;

    public function __construct(CampaignsRepository $campaignRepo)
    {
        $this->campaignRepository = $campaignRepo;
    }

    /**
     * Display a listing of the Campaign.
     * GET|HEAD /campaign
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index()
    {
        $isCustomer=auth()->user()->hasRole([ 'customer']);
        $typeUser=array('both',($isCustomer?'customer':'provider'));
        $camapigns = $this->campaignRepository
        ->where(function($query)
        {
            $query->where([

                ['validityType', '=', 'h'],
        
                [DB::raw('NOW()'), '<=', DB::raw("date_ADD(created_at,interval validity HOUR)")]
        
            ])->orWhere([
    
                ['validityType', '=', 'd'],
        
                [DB::raw('NOW()'), '<=', DB::raw("date_ADD(created_at,interval validity DAY)")]
        
            ])
            ->orWhere([
    
                ['validityType', '=', 'w'],
        
                [DB::raw('NOW()'), '<=', DB::raw("date_ADD(created_at,interval validity WEEK)")]
        
            ])->orWhere([
    
                ['validityType', '=', 'm'],
        
                [DB::raw('NOW()'), '<=', DB::raw("date_ADD(created_at,interval validity MONTH)")]
        
            ])
            ->orWhere([
    
                ['validityType', '=', 'y'],
        
                [DB::raw('NOW()'), '<=', DB::raw("date_ADD(created_at,interval validity YEAR)")]
        
            ]);
        })
        ->whereIn('type',$typeUser)
        ->orderBy('created_at','desc')
        ->limit(4)->get();
        
        
        return $this->sendResponse($camapigns->toArray(), 'Campaign retrieved successfully');
    }

    /**
     * Display the specified Campaign.
     * GET|HEAD /Campaign/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var Campaigns $campaign */
        if (!empty($this->campaignRepository)) {
            $campaign = $this->campaignRepository->findWithoutFail($id);
        }

        if (empty($campaign)) {
            return $this->sendError('Campaign not found');
        }

        return $this->sendResponse($campaign->toArray(), 'Campaign retrieved successfully');
    }
   
}
