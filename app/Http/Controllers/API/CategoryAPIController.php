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
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Maatwebsite\Excel\Concerns\ToArray;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CategoryController
 * @package App\Http\Controllers\API
 */
class CategoryAPIController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepository = $categoryRepo;
    }

    /**
     * Display a listing of the Category.
     * GET|HEAD /categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->categoryRepository->pushCriteria(new RequestCriteria($request));
            $this->categoryRepository->pushCriteria(new ParentCriteria($request));
            $this->categoryRepository->pushCriteria(new NearCriteria($request));
            $this->categoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $type = array('customer', 'provider');
        if (isset($request->all()['type']))
            $type = array($request->all()['type']);
        $typeUser = array('both');
        array_push($typeUser,...$type);
        // print(json_encode($typeUser));
        $categories = $this->categoryRepository->whereIn('type', $typeUser)->get();
        foreach ($categories as $key => $cat) {

            if ($cat->order != null && count($categories) > $cat->order)
                $this->moveElement($categories, $key, $cat->order - 1);
        }

        return $this->sendResponse($categories->toArray(), 'Categories retrieved successfully');
    }

    /**
     * Display the specified Category.
     * GET|HEAD /categories/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var Category $category */
        if (!empty($this->categoryRepository)) {
            $category = $this->categoryRepository->findWithoutFail($id);
        }

        if (empty($category)) {
            return $this->sendError('Category not found');
        }

        return $this->sendResponse($category->toArray(), 'Category retrieved successfully');
    }
    function moveElement($a, $i, $j)
    {
        $tmp =  $a[$i];
        if ($i > $j) {
            for ($k = $i; $k > $j; $k--) {
                $a[$k] = $a[$k - 1];
            }
        } else {
            for ($k = $i; $k < $j; $k++) {
                $a[$k] = $a[$k + 1];
            }
        }
        $a[$j] = $tmp;
        return $a;
    }
}
