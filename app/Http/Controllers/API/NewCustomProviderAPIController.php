<?php
/*
 * File name: EProviderController.php
 * Last modified: 2021.04.14 at 05:59:15
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Criteria\Addresses\AddressesOfUserCriteria;
use App\Criteria\EProviders\EProvidersOfUserCriteria;
use App\Criteria\Users\EProvidersCustomersCriteria;
use App\DataTables\EProviderDataTable;
use App\DataTables\RequestedEProviderDataTable;
use App\Events\EProviderChangedEvent;
use App\Http\Requests\CreateEProviderRequest;
use App\Http\Requests\UpdateEProviderRequest;
use App\Repositories\AddressRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EProviderRepository;
use App\Repositories\EProviderTypeRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class NewCustomProviderAPIController extends Controller
{
    /** @var  EProviderRepository */
    private $eProviderRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var EProviderTypeRepository
     */
    private $eProviderTypeRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var TaxRepository
     */
    private $taxRepository;

    public function __construct(EProviderRepository $eProviderRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo
        , EProviderTypeRepository $eProviderTypeRepo
        , UserRepository $userRepo
        , AddressRepository $addressRepo
        , TaxRepository $taxRepo)
    {
        parent::__construct();
        $this->eProviderRepository = $eProviderRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->eProviderTypeRepository = $eProviderTypeRepo;
        $this->userRepository = $userRepo;
        $this->addressRepository = $addressRepo;
        $this->taxRepository = $taxRepo;
    }

    /**
     * Display a listing of the EProvider.
     *
     * @param EProviderDataTable $eProviderDataTable
     * @return mixed
     */
    public function index(CreateEProviderRequest $request)
    {
        return $eProviderDataTable->render('e_providers.index');
    }

    /**
     * Display a listing of the EProvider.
     *
     * @param EProviderDataTable $eproviderDataTable
     * @return mixed
     */
    public function requestedEProviders(RequestedEProviderDataTable $requestedEProviderDataTable)
    {
        return $requestedEProviderDataTable->render('e_providers.requested');
    }

    /**
     * Show the form for creating a new EProvider.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $eProviderType = $this->eProviderTypeRepository->pluck('name', 'id');
        $user = $this->userRepository->getByCriteria(new EProvidersCustomersCriteria())->pluck('name', 'id');
        $address = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria(auth()->id()))->pluck('address', 'id');
        $tax = $this->taxRepository->pluck('name', 'id');
        $usersSelected = [];
        $addressesSelected = [];
        $taxesSelected = [];
        $hasCustomField = in_array($this->eProviderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eProviderRepository->model());
            $html = generateCustomField($customFields);
        }
        return response()->json(['message'=>'success', 'eProviderType' => $eProviderType, 'user' => $user, "usersSelected"=> $usersSelected, "address"=> $address, "addressesSelected"=> $addressesSelected, "tax"=> $tax, "taxesSelected"=> $taxesSelected], 200);
    }

    /**
     * Store a newly created EProvider in storage.
     *
     * @param CreateEProviderRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(Request $request)
    {
        
        $input = $request->all();

		/*echo "<pre>";
		print_r($input);
		exit;*/
        if(setting('auto_accept_provider') === "1"){
           $input['accepted'] = $input['accepted']??"1";
           $input['featured'] = $input['featured']??false;
           $input['available'] =  $input['available']??false;
           $input['taxes'] =  $input['taxes']??[];

        }
        if (auth()->user()->hasRole(['provider', 'customer'])) {
            $input['users'] = [auth()->id()];
        }

        try {
			$customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eProviderRepository->model());
            $input['accepted'] = 1;
            $input['e_provider_type_id'] = isset($input['e_provider_type_id']['id']) ? $input['e_provider_type_id']['id'] : $input['e_provider_type_id'];

            // $input['_token'] = $input['api_token']??"";
            // unset($input['api_token']);
            // unset($input['user_id']);
        // return response()->json(['message'=>$input], 200);
            
            $eProvider = $this->eProviderRepository->create($input);
            $eProvider->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($eProvider, 'image');
                }
            }
            
            event(new EProviderChangedEvent($eProvider, $eProvider));
        } catch (Exception $e) {
            return response()->json(['message'=>'error','stack'=>$e], 500);
        }

        return response()->json(['message'=>'success'], 200);
    }

    /**
     * Display the specified EProvider.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->eProviderRepository->pushCriteria(new EProvidersOfUserCriteria(auth()->id()));
        $eProvider = $this->eProviderRepository->findWithoutFail($id);

        if (empty($eProvider)) {
            return response()->json(['message'=>'error'], 404);
        }

        return view('e_providers.show')->with('eProvider', $eProvider);
    }

    /**
     * Show the form for editing the specified EProvider.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->eProviderRepository->pushCriteria(new EProvidersOfUserCriteria(auth()->id()));
        $eProvider = $this->eProviderRepository->findWithoutFail($id);
        if (empty($eProvider)) {
            return response()->json(['message'=>'error'], 404);
        }
        $eProviderType = $this->eProviderTypeRepository->pluck('name', 'id');
        $user = $this->userRepository->getByCriteria(new EProvidersCustomersCriteria())->pluck('name', 'id');
        $address = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria(auth()->id()))->pluck('address', 'id');
        $tax = $this->taxRepository->pluck('name', 'id');
        $usersSelected = $eProvider->users()->pluck('users.id')->toArray();
        $addressesSelected = $eProvider->addresses()->pluck('addresses.id')->toArray();
        $taxesSelected = $eProvider->taxes()->pluck('taxes.id')->toArray();

        $customFieldsValues = $eProvider->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eProviderRepository->model());
        $hasCustomField = in_array($this->eProviderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return response()->json(['message'=>'success', 'eProvider' => $eProvider, "eProviderType" => $eProviderType, "user"=> $user, "usersSelected"=> $usersSelected, "address" => $address, "addressesSelected"=> $addressesSelected, "tax"=> $tax, "taxesSelected"=> $taxesSelected], 200);
    }

    /**
     * Update the specified EProvider in storage.
     *
     * @param String $id
     * @param UpdateEProviderRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(String $id, UpdateEProviderRequest $request)
    {

        $this->eProviderRepository->pushCriteria(new EProvidersOfUserCriteria(auth()->id()));
        $oldEProvider = $this->eProviderRepository->findWithoutFail($id);
        if (empty($oldEProvider)) {
            return response()->json(['message'=>'error'], 404);
        }
        $input = $request->all();
        return response()->json($input, 200);

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eProviderRepository->model());
        try {
            // unset($input['user_id']);
            $input['users'] = isset($input['users']) ? $input['users'] : [];
            
            $input['addresses'] = isset($input['addresses']) ? $input['addresses'] : [];
            $input['taxes'] = isset($input['taxes']) ? $input['taxes'] : [];
            $input['e_provider_type_id'] = isset($input['e_provider_type_id']['id']) ? $input['e_provider_type_id']['id'] : '';
            

            
            $eProvider = $this->eProviderRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($eProvider, 'image');
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $eProvider->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
            event(new EProviderChangedEvent($eProvider, $oldEProvider));
        } catch (ValidatorException $e) {
            return response()->json(['message'=>'error'], 500);
        }

        return response()->json(['message'=>'success'], 200);
    }

    /**
     * Remove the specified EProvider from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function destroy(int $id)
    {
        if (config('installer.demo_app')) {
            return response()->json(['message'=>'error'], 403);
        }
        $this->eProviderRepository->pushCriteria(new EProvidersOfUserCriteria(auth()->id()));
        $eProvider = $this->eProviderRepository->findWithoutFail($id);

        if (empty($eProvider)) {
            return response()->json(['message'=>'error'], 404);
        }

        $this->eProviderRepository->delete($id);

        return response()->json(['message'=>'success'], 200);
    }

    /**
     * Remove Media of EProvider
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $eProvider = $this->eProviderRepository->findWithoutFail($input['id']);
        try {
            if ($eProvider->hasMedia($input['collection'])) {
                $eProvider->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function addUser(CreateEProviderRequest $request)
    {
        $input = $request->all();
        if(setting('auto_accept_provider') === "1"){
           $input['accepted'] = $input['accepted']??"1";
         }
        if (auth()->user()->hasRole(['provider', 'customer'])) {
            $input['users'] = [auth()->id()];
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eProviderRepository->model());
        try {
            $eProvider = $this->eProviderRepository->create($input);
            $eProvider->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($eProvider, 'image');
                }
            }
            event(new EProviderChangedEvent($eProvider, $eProvider));
        } catch (ValidatorException $e) {
            return response()->json(['message'=>'error'], 500);
        }

        return response()->json(['message'=>'success'], 200);
    }
}
