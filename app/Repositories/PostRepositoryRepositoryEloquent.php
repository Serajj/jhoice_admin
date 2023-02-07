<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PostRepositoryRepository;
use App\Entities\PostRepository;
use App\Validators\PostRepositoryValidator;

/**
 * Class PostRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PostRepositoryRepositoryEloquent extends BaseRepository implements PostRepositoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PostRepository::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
