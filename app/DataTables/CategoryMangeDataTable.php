<?php
/*
 * File name: PermissionDataTable.php
 * Last modified: 2021.01.23 at 11:14:43
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\Category;
use App\Models\Post;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class CategoryMangeDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable
            ->editColumn('name', function ($category) {
                return $category->name;
            })
            ->editColumn('VisibleType', function ($category) {
                return json_encode(['typeId' => $category]);
            });
            // ->addColumn('action', 'settings.permissions.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            // ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false ,'responsivePriority'=>'100'])
                ->parameters(array_merge(
                    config('datatables-buttons.parameters'), [
                        'language' => json_decode(
                            file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                            ), true),
                        'rowGroup' => [
                            'dataSrc' => 'class'
                        ],
                        'colReorder' => false,
                        'fixedColumns' => false,
                        "initComplete" => "function(settings){console.log('initComplete'); renderButtons( settings.sTableId); renderiCheck(settings.sTableId)}",
                       
                    ]
                )
            )
        ;
    }

    /**
     * Get columns.
     *
     * @return array
     */

    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'name',
                'title' => trans('lang.category_name'),
                'searchable' => true
            ],

        ];
        $roles = Category::visibleTypes();
        $options = "";
        foreach ($roles as $role) {
            $options = $options . '<option value=\'' . $role['name'] . '\'>' . $role['name'] . '</option>';
        }
        $newColumn['data'] = 'VisibleType';
        $newColumn['title'] = "Visible Type";
        $newColumn['searchable'] = 'false';
        $newColumn['exportable'] = 'false';
        $newColumn['render'] = 'function(){return "<div style=\"width:100px;\" class=\'icheck-default icheck-category\'><select  class=\'categories\' style=\"width:100px;\" name=\'category\'  data-typeid=\'"+data+"\'>' . $options . '</select></div>"}';

        $columns[] = $newColumn;

        // data-role-name=\''
        //          . $role['name'] . '\' data-role-id=\'' . $role['id'] . '\' 
        //          data-permission=\'"+data+"\'><label for=\'namehere\'></label>
        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'categoryManageDatatable' . time();
    }
}
