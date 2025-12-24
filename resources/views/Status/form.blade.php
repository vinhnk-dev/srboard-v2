@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Edit Status')
@section('main')
<div class="nk-content">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @if ($errors->has('project_name') && $errors->has('url') && $errors->has('git_url') && $errors->has('description'))
                    <li>All fields are blank. Please fill in the required fields.</li>
                @else
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif
    <div class="container-fluid px-0 px-md-2">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="card p-3">
                            <div class="card-inner">
                                <h4 class="title nk-block-title mb-4">Status</h4>
                                    <form action="{{ $modal->id ? route('admin.status.update',$modal->id) : route('admin.status.store') }}"
                                        class="gy-3"
                                        method="POST"
                                        enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$modal->id}}">
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>STATUS NAME</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="status_name" placeholder="*Please enter project name" value="{{$modal->status_name}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Color</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="color" name="color" class="form-control-color" id="exampleColorInput" value="{{$modal->color}}" title="Choose your color" style="display: inline-block;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Check due</label>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="preview-block" id="is_check_due_container">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="is_check_due" value="1" id="is_check_due"
                                                            @if($modal->is_check_due==1) checked @endif>
                                                    <label class="custom-control-label" for="is_check_due"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a href="{{route('admin.status.index')}}" class="btn btn-lg btn-light">Cancel</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-lg btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
