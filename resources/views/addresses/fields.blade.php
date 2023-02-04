@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Description Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('description', trans("lang.address_description"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('description', null,  ['class' => 'form-control','placeholder'=>  trans("lang.address_description_placeholder"),'value' => {{$currentUserInfo->countryName }}]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.address_description_help") }}
            </div>
        </div>
    </div>

    <!-- Address Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('address', trans("lang.address_address"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('address', null,  ['class' => 'form-control','placeholder'=>  trans("lang.address_address_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.address_address_help") }}
            </div>
        </div>
    </div>

</div>
<div class="d-flex flex-column col-sm-12 col-md-6">

    <!-- Latitude Field -->
    {{--<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('latitude', trans("lang.address_latitude"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::number('latitude', null,  ['class' => 'form-control','step'=>'any', 'placeholder'=>  trans("lang.address_latitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.address_latitude_help") }}
            </div>
        </div>
    </div>--}}

    <!-- Longitude Field -->
    {{--<div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('longitude', trans("lang.address_longitude"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::number('longitude', null,  ['class' => 'form-control','step'=>'any', 'placeholder'=>  trans("lang.address_longitude_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.address_longitude_help") }}
            </div>
        </div>
    </div>--}}

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
     integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
     crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
     integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
     crossorigin=""></script>

    <input id="loc_lat_input" type="hidden" name="latitude" required>
    <input id="loc_long_input" type="hidden" name="longitude" required>

    <div class="form-group justify-content-center d-flex flex-column ml-2 ml-md-5">
      {!! Form::label('longitude', "Add Current Location", ['class' => 'control-label mr-1 ml-2 ml-md-3']) !!}
      <!-- <button onclick="setCurrentLocation()" type="button" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2" name="button">
        <i class="fa fa-map-marker-alt"></i>
        Get Current Location
      </button> -->

      <div id="map" style="height: 20rem; width: 100%;"></div>

      <div id="location-notification" class="form-text text-muted ml-2 ml-md-4"></div>
    </div>

    <script>

      $(document).ready(init);

      function init() {
        var mymap = L.map('map').setView([51.505, -0.09], 13),
          lonSpan = $('#lon'),
          latSpan = $('#lat');

        L.tileLayer( 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            subdomains: ['a','b','c']
        }).addTo( mymap );

        var marker = L.marker([51.5, -0.09]).addTo(mymap);

        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition((position) => {
            document.getElementById('loc_lat_input').value = position.coords.latitude
            document.getElementById('loc_long_input').value = position.coords.longitude
            mymap.setView([position.coords.latitude, position.coords.longitude], 13)
            marker.setLatLng({lat: position.coords.latitude, lng: position.coords.longitude})
          });
        }

        mymap.on('click', function(e) {
          document.getElementById('loc_lat_input').value = e.latlng.lat
          document.getElementById('loc_long_input').value = e.latlng.lng
          marker.setLatLng(e.latlng)
        });
      }
    </script>

    <!-- 'Boolean Default Field' -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('default', trans("lang.address_default"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        {!! Form::hidden('default', 0, ['id'=>"hidden_default"]) !!}
        <div class="col-9 icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('default', 1, null) !!}
            <label for="default"></label>
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
        <i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.address')}}</button>
    <a href="{!! route('addresses.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
