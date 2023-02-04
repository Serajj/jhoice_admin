<?php
/*
 * File name: BookingDataTable.php
 * Last modified: 2021.06.10 at 20:38:02
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\Booking;
use App\Models\CustomField;
use App\Models\TrackClick;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class TrackDataTable extends DataTable
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
            ->editColumn('id', function ($track) {
                return $track->service->eProvider->id;
            })
            ->editColumn('service.id', function ($track) {
                return $track->service->id;
            })
            ->editColumn('service.eProvider', function ($track) {
                return $track->service->eProvider->name;
            })
            ->editColumn('service.name', function ($track) {
                return json_decode($track->name)->en;
            })
            ->editColumn('getCountByCall', function ($track) {
                return $track->getCountByType('call', $track->service->eProvider->id);
            })
            ->editColumn('getCountByChat', function ($track) {
                return $track->getCountByType('chat', $track->service->eProvider->id);
            })
            ->editColumn('getCountByBook', function ($track) {
                return $track->getCountByType('book', $track->service->eProvider->id);
            }) 
            ->editColumn('getCountCompleted', function ($track) {
                return $track->getCountCompleted($track->service->eProvider->id);
            })
            ->editColumn('getCountByCallService', function ($track) {
                return $track->getCountByTypeService('call', $track->service->id);
            })
            ->editColumn('getCountByChatService', function ($track) {
                return $track->getCountByTypeService('chat', $track->service->id);
            })
            ->editColumn('getCountByBookService', function ($track) {
                return $track->getCountByTypeService('book', $track->service->id);
            })
            ->editColumn('getCountCompletedService', function ($track) {
                return $track->getCountCompletedService($track->service->id);
            })
            // ->editColumn('.', function ($eService) {
            //     return getDateColumn($eService, 'updated_at');
            // })
            ->addColumn('action', function ($track) {
                return is_null($this->serid)?"<a data-toggle=\"tooltip\" data-placement=\"left\" title=\"\" href=\"/jhoice/jhoicenew/public/track/" . $track->service->eProvider->id . "\" class=\"btn btn-link\" data-original-title=\"View Provider Services\">
View Services </a>":"";
            })
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
            // print('<script>console.log(\'id iis'.isset($this->serid)?'1':'2'.' \')</script>');

        $columns = [
            (is_null($this->serid))?[
                'data' => 'id',
                'title' => trans('lang.custom_field_id'),
            ]:[
                'data' => 'service.id',
                'title' => trans('lang.custom_field_id'),
            ],
            (is_null($this->serid))?[
                'data' => 'service.eProvider',
                'title' => trans('lang.e_provider'),
            ]:
            [
                'data' => 'service.name',
                'title' => trans('lang.e_service'),
            ]
            ,
            (is_null($this->serid))?[
                'data' => 'getCountByCall',
                'name' => 'getCountByCall',
                'title' => trans('lang.track_call_button'),
            ]:[
                'data' => 'getCountByCallService',
                'name' => 'getCountByCallService',
                'title' => trans('lang.track_call_button'),
            ],
            (is_null($this->serid))?[
                'data' => 'getCountByChat',
                'name' => 'getCountByChat',
                'title' => trans('lang.track_chat_button'),
            ]:[
                'data' => 'getCountByChatService',
                'name' => 'getCountByChatService',
                'title' => trans('lang.track_chat_button'),
                'orderable'=> false,
            ],
            (is_null($this->serid))?[
                'data' => 'getCountByBook',
                'name' => 'getCountByBook',
                'title' => trans('lang.track_book_button'),
            ]:[
                'data' => 'getCountByBookService',
                'name' => 'getCountByBookService',
                'title' => trans('lang.track_book_button'),
            ],
            (is_null($this->serid))?[
                'data' => 'getCountCompleted',
                'name' => 'getCountCompleted',
                'title' => trans('lang.track_completed'),
            ]:[
                'data' => 'getCountCompletedService',
                'name' => 'getCountCompletedService',
                'title' => trans('lang.track_completed'),
            ],

        ];

        $hasCustomField = in_array(Booking::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Booking::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.booking_' . $field->name),
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
     * @param Booking $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TrackClick $model)
    {
        if (auth()->user()->hasRole('admin')) {
            $providerid = $this->serid;
            // print('<script>console.log(\'id iis'.json_encode().' \')</script>');

            if (isset($providerid))
                return  $model->getTotalBookingByProviderServices($providerid);
            else
                return $model->getTotalBookingByProvider();
        }
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
                config('datatables-buttons.parameters'),
                [
                    'language' => json_decode(
                        file_get_contents(
                            base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ),
                        true
                    )
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
        return 'trackdatatable' . time();
    }
}
