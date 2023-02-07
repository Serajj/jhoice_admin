<?php
/*
 * File name: FaqCategoryAPIController.php
 * Last modified: 2021.02.11 at 09:26:34
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Repositories\PostCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class FaqCategoryController
 * @package App\Http\Controllers\API
 */
class FaqCategoryAPIController extends Controller
{
    /** @var  PostCategoryRepository */
    private $postCategoryRepository;

    public function __construct(PostCategoryRepository $postCategoryRepo)
    {
        $this->postCategoryRepository = $postCategoryRepo;
    }

    /**
     * Display a listing of the FaqCategory.
     * GET|HEAD /faqCategories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->postCategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->postCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $postCategories = $this->postCategoryRepository->all();
        $this->filterCollection($request, $faqCategories);

        return $this->sendResponse($postCategories->toArray(), 'post Categories retrieved successfully');
    }

    /**
     * Display the specified FaqCategory.
     * GET|HEAD /faqCategories/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var postCategory $faqCategory */
        if (!empty($this->postCategoryRepository)) {
            $postCategory = $this->postCategoryRepository->findWithoutFail($id);
        }

        if (empty($postCategory)) {
            return $this->sendError('post Category not found');
        }

        return $this->sendResponse($postCategory->toArray(), 'post Category retrieved successfully');
    }
}
