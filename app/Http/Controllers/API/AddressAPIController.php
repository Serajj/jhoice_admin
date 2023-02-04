<?php
/*
 * File name: AddressAPIController.php
 * Last modified: 2021.02.18 at 12:08:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use App\Http\Requests\CreateAddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use App\Repositories\CustomFieldRepository;



/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */
class AddressAPIController extends Controller
{
    /** @var  AddressRepository */
    private $addressRepository;

    public function __construct(AddressRepository $addressRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo)
    {
        parent::__construct();
        $this->addressRepository = $addressRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the Address.
     * GET|HEAD /addresses
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->addressRepository->pushCriteria(new RequestCriteria($request));
            $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $addresses = $this->addressRepository->all();
        $this->filterCollection($request, $addresses);

        return $this->sendResponse($addresses->toArray(), __('lang.saved_successfully', ['operator' => __('lang.address')]));
    }
	
	public function store(CreateAddressRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = Auth::id();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->addressRepository->model());
        try {
            $address = $this->addressRepository->create($input);
            $address->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        // return redirect(route('addresses.edit', $address->id));
		//return $this->sendResponse(['temp' => 'address'], 'Address retrieved successfully');
		return $this->sendResponse( $address, 'Address retrieved successfully');
    }

    /**
     * Display the specified Address.
     * GET|HEAD /addresses/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var Address $address */
        if (!empty($this->addressRepository)) {
            $address = $this->addressRepository->findWithoutFail($id);
        }

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        return $this->sendResponse($address->toArray(), 'Address retrieved successfully');
    }
}
