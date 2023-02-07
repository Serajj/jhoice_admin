<!-- Id Field -->
<div class="form-group row col-6">
    {!! Form::label('id', 'Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->id !!}</p>
    </div>
</div>

<!-- Question Field -->
<div class="form-group row col-6">
    {!! Form::label('question', 'Question:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->question !!}</p>
    </div>
</div>

<!-- Answer Field -->
<div class="form-group row col-6">
    {!! Form::label('answer', 'Answer:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->answer !!}</p>
    </div>
</div>

<!-- post Category Id Field -->
<div class="form-group row col-6">
    {!! Form::label('faq_category_id', 'Faq Category Id:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->post_category_id !!}</p>
    </div>
</div>

<!-- Created At Field -->
<div class="form-group row col-6">
    {!! Form::label('created_at', 'Created At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->created_at !!}</p>
    </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
    {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
    <div class="col-md-9">
        <p>{!! $post->updated_at !!}</p>
    </div>
</div>

