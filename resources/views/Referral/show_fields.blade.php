<!-- Id Field -->
<div class="form-group row col-6">
    {!! Form::label('id', 'Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->id !!}</p>
    </div>
</div>

<!-- Question Field -->
<div class="form-group row col-6">
    {!! Form::label('question', 'Question:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->question !!}</p>
    </div>
</div>

<!-- Answer Field -->
<div class="form-group row col-6">
    {!! Form::label('answer', 'Answer:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->answer !!}</p>
    </div>
</div>

<!-- Referral Category Id Field -->
<div class="form-group row col-6">
    {!! Form::label('Referral_category_id', 'Referral Category Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->Referral_category_id !!}</p>
    </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
    {!! Form::label('created_at', 'Created At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->created_at !!}</p>
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $Referral->updated_at !!}</p>
    </div>
</div>

