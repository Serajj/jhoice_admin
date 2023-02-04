@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Name Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('name', trans("lang.campaigns_name"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('name', null,  ['class' => 'form-control','placeholder'=>  trans("lang.campaigns_name_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.campaigns_name_help") }}
            </div>
        </div>
    </div>

    <!-- Type Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('type', trans("lang.campaigns_type"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('type', ['both'=>'Both','customer'=>'Customer','provider'=>'provider'], null, ['data-empty'=>trans("lang.campaigns_type_placeholder"), 'class' => 'select2 not-required form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.campaigns_type_help") }}</div>
        </div>
    </div>

    <!-- Validity Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('validity', trans("lang.campaigns_validity"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::number('validity', null,  ['class' => 'form-control','placeholder'=>  trans("lang.campaigns_validity_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.campaigns_validity_help") }}
            </div>
        </div>
    </div>
    <!-- Select ValidityType Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('validityType', trans("lang.campaigns_type"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('validityType', 
            ['h'=>'Hour','d'=>'Day','w'=>'Week','m'=>'Month','y'=>'Year'], null, ['data-empty'=>trans("lang.campaigns_validitytype_placeholder"), 'class' => 'select2 not-required form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.campaigns_validitytype_help") }}</div>
        </div>
    </div>
    <!-- Select ValidityType Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('condition', trans("lang.campaigns_condition"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('condition', 
            ['login'=>'After Login','onetime'=>'One time in a day',
            'firstserv'=>'Until first service create'], null, ['data-empty'=>trans("lang.campaigns_condition_placeholder"), 'class' => 'select2 not-required form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.campaigns_condition_placeholder") }}</div>
        </div>
    </div>
   

</div>
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Image Field -->
    <div class="form-group align-items-start d-flex flex-column flex-md-row">
        {!! Form::label('image', trans("lang.category_image"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal" class="btn btn-outline-{{setting('theme_color','primary')}} btn-sm float-right mt-1">{{ trans('lang.media_select')}}</a>
            <div class="form-text text-muted w-50">
                {{ trans("lang.category_image_help") }}
            </div>
        </div>
    </div>
    
    @prepend('scripts')
        <script type="text/javascript">
            var var16110650672130312723ble = '';
            @if(isset($campaigns) && $campaigns->hasMedia('image'))
                var16110650672130312723ble = {
                name: "{!! $campaigns->getFirstMedia('image')->name !!}",
                size: "{!! $campaigns->getFirstMedia('image')->size !!}",
                type: "{!! $campaigns->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $campaigns->getFirstMedia('image')->collection_name !!}"
            };
            @endif
            var dz_var16110650672130312723ble = $(".dropzone.image").dropzone({
                url: "{!!url('uploads/store')!!}",
                addRemoveLinks: true,
                maxFiles: 1,
                init: function () {
                    @if(isset($campaigns) && $campaigns->hasMedia('image'))
                    dzInit(this, var16110650672130312723ble, '{!! url($campaigns->getFirstMediaUrl('image','thumb')) !!}')
                    @endif
                },
                accept: function (file, done) {
                    dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                },
                sending: function (file, xhr, formData) {
                    dzSending(this, file, formData, '{!! csrf_token() !!}');
                },
                maxfilesexceeded: function (file) {
                    dz_var16110650672130312723ble[0].mockFile = '';
                    dzMaxfile(this, file);
                },
                complete: function (file) {
                    dzComplete(this, file, var16110650672130312723ble, dz_var16110650672130312723ble[0].mockFile);
                    dz_var16110650672130312723ble[0].mockFile = file;
                },
                removedfile: function (file) {
                    dzRemoveFile(
                        file, var16110650672130312723ble, '{!! url("campaigns/remove-media") !!}',
                        'image', '{!! isset($campaigns) ? $campaigns->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                    );
                }
            });
            dz_var16110650672130312723ble[0].mockFile = var16110650672130312723ble;
            dropzoneFields['image'] = dz_var16110650672130312723ble;


        </script>
@endprepend
 <!-- RedirectUrl Field -->
 <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
    {!! Form::label('redirectUrl', trans("lang.campaigns_url"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        {!! Form::text('redirectUrl', null,  ['class' => 'form-control','step'=>'1','min'=>'0', 'placeholder'=>  trans("lang.campaigns_url_placeholder")]) !!}
        <div class="form-text text-muted">
            {{ trans("lang.campaigns_url_help") }}
        </div>
    </div>
</div>

</div>
@if($customFields)
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
    <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2">
        <i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.campaigns')}}
    </button>
    <a href="{!! route('campaigns.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
