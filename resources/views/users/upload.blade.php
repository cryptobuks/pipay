

@if (count($errors) > 0)
  @include('users.error')
@endif

{!! Form::open(
    array(
        'route' => 'user.upload.logo', 
        'class' => 'form', 
        'novalidate' => 'novalidate', 
        'files' => true)) !!}

<div class="form-group">
    {!! Form::label('Product Image') !!}
    {!! Form::file('logo', null) !!}
</div>

<div class="form-group">
    {!! Form::submit('Upload Logo!') !!}
</div>
{!! Form::close() !!}
</div>

