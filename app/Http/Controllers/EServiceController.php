<?php
/*
 * File name: EServiceController.php
 * Last modified: 2021.03.21 at 15:11:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\Criteria\EProviders\EProvidersOfUserCriteria;
use App\Criteria\EServices\EServicesOfUserCriteria;
use App\DataTables\EServiceDataTable;
use App\DataTables\TrackDataTable;
use App\Http\Requests\CreateEServiceRequest;
use App\Http\Requests\UpdateEServiceRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EProviderRepository;
use App\Repositories\EServiceRepository;
use App\Repositories\UploadRepository;
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

class EServiceController extends Controller
{
    /** @var  EServiceRepository */
    private $eServiceRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var EProviderRepository
     */
    private $eProviderRepository;

    public function __construct(
        EServiceRepository $eServiceRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo,
        CategoryRepository $categoryRepo,
        EProviderRepository $eProviderRepo
    ) {
        parent::__construct();
        $this->eServiceRepository = $eServiceRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->categoryRepository = $categoryRepo;
        $this->eProviderRepository = $eProviderRepo;
    }

    /**
     * Display a listing of the EService.
     *
     * @param EServiceDataTable $eServiceDataTable
     * @return Response
     */
    public function index(EServiceDataTable $eServiceDataTable)
    {
        return $eServiceDataTable->render('e_services.index');
    }

    /**
     * Show the form for creating a new EService.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {
        $category = $this->categoryRepository->pluck('name', 'id');
        $eProvider = $this->eProviderRepository->getByCriteria(new EProvidersOfUserCriteria(auth()->id()))->pluck('name', 'id');
        $categoriesSelected = [];
        $hasCustomField = in_array($this->eServiceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eServiceRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('e_services.create')->with("customFields", isset($html) ? $html : false)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with('terms', [])->with("eProvider", $eProvider);
    }

    /**
     * Store a newly created EService in storage.
     *
     * @param CreateEServiceRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateEServiceRequest $request)
    {
        $input = $request->all();
        if ($input['price_unit'] == 'fixed') {

            // if(isset($input['durationHour'],$input['durationMonth'],$input['durationYear'])){
            $duration = "";
            $duration .= $input['durationHour'] != null ? $input['durationHour'] : '0';
            $duration .= $input['durationDay'] != null ? ' ' . $input['durationDay'] : ' 0';
            $duration .= $input['durationMonth'] != null ? ' ' . $input['durationMonth'] : ' 0';
            $duration .= $input['durationYear'] != null ? ' ' . $input['durationYear'] : ' 0';
            $input['duration'] = $duration;
            // }
        } else if ($input['price_unit'] == 'hourly' && isset($input['durationHourly']))
            $input['duration'] = $input['durationHourly'];

        unset($input['durationHour'], $input['durationDay'], $input['durationMonth'], $input['durationYear'], $input['durationHourly']);
        $input["terms"] = isset($input["terms"]) ? json_encode($input["terms"]) : null;
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eServiceRepository->model());
        try {
            $eService = $this->eServiceRepository->create($input);
            $eService->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($eService, 'image');
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.e_service')]));

        return redirect(route('eServices.index'));
    }

    /**
     * Display the specified EService.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->eServiceRepository->pushCriteria(new EServicesOfUserCriteria(auth()->id()));
        $eService = $this->eServiceRepository->findWithoutFail($id);

        if (empty($eService)) {
            Flash::error('E Service not found');

            return redirect(route('eServices.index'));
        }

        return view('e_services.show')->with('eService', $eService);
    }

    /**
     * Show the form for editing the specified EService.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->eServiceRepository->pushCriteria(new EServicesOfUserCriteria(auth()->id()));
        $eService = $this->eServiceRepository->findWithoutFail($id);
        if (empty($eService)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.e_service')]));

            return redirect(route('eServices.index'));
        }
        $terms = json_decode($eService->terms);
        $category = $this->categoryRepository->pluck('name', 'id');
        $eProvider = $this->eProviderRepository->getByCriteria(new EProvidersOfUserCriteria(auth()->id()))->pluck('name', 'id');
        $categoriesSelected = $eService->categories()->pluck('categories.id')->toArray();

        $customFieldsValues = $eService->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eServiceRepository->model());
        $hasCustomField = in_array($this->eServiceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        if ($eService->price_unit == 'fixed') {
            $durations = explode(" ", $eService->duration);
            if (isset($durations[0]))
                $eService->durationHour = $durations[0];
            if (isset($durations[1]))
                $eService->durationDay = $durations[1];
            if (isset($durations[2]))
                $eService->durationMonth = $durations[2];
            if (isset($durations[3]))
                $eService->durationYear = $durations[3];
        }else if ($eService->price_unit == 'hourly') {
            $eService->durationHourly = $eService->durationHourly;
        }
        return view('e_services.edit')->with('eService', $eService)->with("customFields", isset($html) ? $html : false)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with("terms", $terms)->with("eProvider", $eProvider);
    }

    /**
     * Update the specified EService in storage.
     *
     * @param int $id
     * @param UpdateEServiceRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateEServiceRequest $request)
    {
        $this->eServiceRepository->pushCriteria(new EServicesOfUserCriteria(auth()->id()));
        $eService = $this->eServiceRepository->findWithoutFail($id);

        if (empty($eService)) {
            Flash::error('E Service not found');
            return redirect(route('eServices.index'));
        }
        $input = $request->all();
        if ($input['price_unit'] == 'fixed') {

            // if(isset($input['durationHour'],$input['durationMonth'],$input['durationYear'])){
            $duration = "";
            $duration .= $input['durationHour'] != null ? $input['durationHour'] : '0';
            $duration .= $input['durationDay'] != null ? ' ' . $input['durationDay'] : ' 0';
            $duration .= $input['durationMonth'] != null ? ' ' . $input['durationMonth'] : ' 0';
            $duration .= $input['durationYear'] != null ? ' ' . $input['durationYear'] : ' 0';
            $input['duration'] = $duration;
            // }
        } else if ($input['price_unit'] == 'hourly' && isset($input['durationHourly']))
            $input['duration'] = $input['durationHourly'];

        unset($input['durationHour'], $input['durationDay'], $input['durationMonth'], $input['durationYear'], $input['durationHourly']);
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->eServiceRepository->model());
        try {
            $input['categories'] = isset($input['categories']) ? $input['categories'] : [];
            $eService = $this->eServiceRepository->update($input, $id);
            if (isset($input['image']) && $input['image'] && is_array($input['image'])) {
                foreach ($input['image'] as $fileUuid) {
                    $cacheUpload = $this->uploadRepository->getByUuid($fileUuid);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($eService, 'image');
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $eService->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.e_service')]));

        return redirect(route('eServices.index'));
    }

    /**
     * Remove the specified EService from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function destroy(int $id)
    {
        $this->eServiceRepository->pushCriteria(new EServicesOfUserCriteria(auth()->id()));
        $eService = $this->eServiceRepository->findWithoutFail($id);

        if (empty($eService)) {
            Flash::error('E Service not found');

            return redirect(route('eServices.index'));
        }

        $this->eServiceRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.e_service')]));

        return redirect(route('eServices.index'));
    }

    /**
     * Remove Media of EService
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $eService = $this->eServiceRepository->findWithoutFail($input['id']);
        try {
            if ($eService->hasMedia($input['collection'])) {
                $eService->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
    public function trackClick(TrackDataTable $trackDataTable)
    {
        return $trackDataTable->render('e_services.trackclick');
    }
    public function trackClickByService(TrackDataTable $trackDataTable, $id)
    {
        return $trackDataTable->with('serid', $id)->render('e_services.trackclick');
    }

    //     return view('e_services.trackclick');
    // }
}
