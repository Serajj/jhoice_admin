<?php
/*
 * File name: PermissionController.php
 * Last modified: 2021.03.18 at 16:44:59
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers;

use App\DataTables\CategoryMangeDataTable;
use App\DataTables\PermissionDataTable;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Flash;
use Illuminate\Support\Facades\Artisan;
use Request;
use Response;

class CategoryManageController extends Controller
{
    /** @var  CategoryRepository */
    private $permissionRepository;

    public function __construct(CategoryRepository $categoryRepo)
    {
        parent::__construct();
        $this->categoryManageRepository = $categoryRepo;
    }

    /**
     * Display a listing of the Permission.
     *
     * @param CategoryMangeDataTable $permissionDataTable
     * @return Response
     */
    public function index(CategoryMangeDataTable $categoryManageData)
    {
        return $categoryManageData->render('category_manage.index');
    }




    public function changeType(Request $request)
    {
        $input = Request::all();
        $category = Category::findOrfail($input['id']);
        $category->type = $input['type'];
        $category->save();
    }


}
