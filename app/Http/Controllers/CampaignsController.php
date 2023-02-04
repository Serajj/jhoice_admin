<?php

namespace App\Http\Controllers;

use App\DataTables\CampaignsDataTable;
use App\Http\Requests\CreateCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Repositories\CampaignsRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Validator\Exceptions\ValidatorException;

class CampaignsController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(CampaignsRepository $campaignRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->campaignRepository = $campaignRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Category.
     *
     * @param CampaignsDataTable $categoryDataTable
     * @return Response
     */
    public function index(CampaignsDataTable $campaignsDataTable)
    {
        return $campaignsDataTable->render('campaigns.index');
    }
    public function create()
    {

        $hasCustomField = in_array($this->campaignRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->campaignRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('campaigns.create')->with("customFields", isset($html) ? $html : false);
    }
    /**
     * Store a newly created Category in storage.
     *
     * @param CreateCampaignRequest $request
     *
     * @return Response
     */
    public function store(CreateCampaignRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->campaignRepository->model());
        try {
            $campaign = $this->campaignRepository->create($input);
            $campaign->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($campaign, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.campaign')]));

        return redirect(route('campaigns.index'));
    }

    /**
     * Show the form for editing the specified Campaign.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $camapign = $this->campaignRepository->findWithoutFail($id);

        if (empty($camapign)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.campaign')]));

            return redirect(route('campaigns.index'));
        }
        $customFieldsValues = $camapign->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->campaignRepository->model());
        $hasCustomField = in_array($this->campaignRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('campaigns.edit')->with('campaigns', $camapign)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCampaignRequest $request)
    {
        $campaign = $this->campaignRepository->findWithoutFail($id);

        if (empty($campaign)) {
            Flash::error('campaign not found');
            return redirect(route('campaigns.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->campaignRepository->model());
        try {
            $campaign = $this->campaignRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($campaign, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $campaign->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.campaign')]));

        return redirect(route('campaigns.index'));
    }

     /**
     * Remove the specified Campaign from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $campaign = $this->campaignRepository->findWithoutFail($id);

        if (empty($campaign)) {
            Flash::error('Campaign not found');

            return redirect(route('campaigns.index'));
        }

        $this->campaignRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.campaigns')]));

        return redirect(route('campaigns.index'));
    }
}
