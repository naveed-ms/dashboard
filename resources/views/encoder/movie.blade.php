@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add Movie</div>
                <div class="panel-body">
                  @if (isset($message))
                  <div class="alert alert-info">{{$message}}</div>
                  @endif
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/encoder/movie') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('movie_name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Movie Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="movie_name" value="{{ old('movie_name') }}">

                                @if ($errors->has('movie_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('movie_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fa fa-btn fa-plus"></i>Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
