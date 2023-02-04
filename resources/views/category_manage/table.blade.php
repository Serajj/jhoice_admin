@push('css_lib')
    @include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
    @include('layouts.datatables_categoryjs')
    {!! $dataTable->scripts() !!}
@endpush