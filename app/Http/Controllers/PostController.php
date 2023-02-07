<?php

namespace App\Http\Controllers;

use App\DataTables\PostDataTable;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\PostCategoryRepository;
use App\Repositories\PostRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class PostController extends Controller
{
 public function index()
    {
        return view('post.index');
    
    }
}
    