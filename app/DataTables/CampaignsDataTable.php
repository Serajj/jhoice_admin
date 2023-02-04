<?php
/*
 * File name: CamapignsDataTable.php
 * Last modified: 2021.04.12 at 09:17:55
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\Campaigns;
use App\Models\CustomField;
use App\Models\Post;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class CampaignsDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('image', function ($campaign) {
                return getMediaColumn($campaign, 'image');
            })
            ->editColumn('name', function ($campaign) {
                return $campaign->name;
            })
            ->editColumn('type', function ($campaign) {
                return $campaign->type;
            })
            ->editColumn('validity', function ($campaign) {
                return $campaign->validity;
            })
            ->editColumn('validityType', function ($campaign) {
                return $campaign->validityType;
            })
            ->addColumn('action', 'campaigns.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
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
                'data' => 'image',
                'title' => trans('lang.category_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'name',
                'title' => trans('lang.campaigns_name'),

            ],
            [
                'data' => 'type',
                'title' => trans('lang.campaigns_type'),

            ],
            [
                'data' => 'validity',
                'title' => trans('lang.campaigns_validity'),

            ],
            [
                'data' => 'validityType',
                'title' => trans('lang.campaigns_type'),

            ]
            ,
            [
                'data' => 'redirectUrl',
                'title' => trans('lang.campaigns_url'),

            ]
        ];

        $hasCustomField = in_array(Campaigns::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Campaigns::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.campaign_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param Campaigns $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Campaigns $model)
    {
        return $model->newQuery()->select("campaigns.*");
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
            ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'campaigndatatable' . time();
    }
}
