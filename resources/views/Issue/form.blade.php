@extends('layouts.InputLayout', [
'title' => __($title ?? 'Issue')
])
@if ($modal->id)
    @section('page-title', 'SR Board | Modify Issue')
@else
    @section('page-title', 'SR Board | Create New Issue')
@endif
@section('main')
<div class="nk-content">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @if ($errors->has('title') && $errors->has('url') && $errors->has('content') && $errors->has('due_date'))
                    <li>All fields are blank. Please fill in the required fields.</li>
                @else
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif
    <!-- Main content -->
    <div class="container-fluid px-0 px-md-2">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="card">
                            <div class="card-inner">
                                @if ($modal->id)
                                    <h4 class="title nk-block-title mb-4">MODIFY ISSUE</h4>
                                @else
                                    <h4 class="title nk-block-title mb-4">CREATE NEW ISSUE</h4>
                                @endif
                                <form action="{{$form_action}}"
                                    class="gy-3" method="POST" enctype="multipart/form-data">
                                    @method('POST')
                                    @csrf
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Title</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="title" value="{{$modal->title}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if (auth()->user()->hasRole('Admin'))
                                        <div class="row g-3 align-center">
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label class="form-label"><span class="text-danger">*</span>Project</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <div class="preview-block">
                                                    <select class="form-control form-control-lg select-selected" name="project_id" id="project_change">
                                                        @foreach($project as $value)
                                                            <option value="{{$value->id}}" @if($value->id == $parentid) selected @endif>{{$value->project_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="dropdown-icon"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" name="project_id" value="{{$modal->project_id}}">
                                    @endif
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Status</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="preview-block">
                                                <select class="form-control form-control-lg select-selected" name="status" id="status_change">
                                                    @foreach($status_name as $value)
                                                        <option value="{{$value->status_id}}" @if($modal->status == $value->status_id) selected @endif>{{$value->status_name}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="dropdown-icon"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Reporter</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 d-flex flex-column">
                                            <select class="form-select form-select-sm" name="report_assign[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($reporters as $key => $value1)
                                                    <option value="{{$value1->id}}" {{$value1->active}} > {{$value1->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Assign</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 d-flex flex-column">
                                            <select class="form-select form-select-sm" name="user_assign[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($users as $key => $value)
                                                    <option value="{{$value->id}}" {{$value->active}} > {{$value->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label text-nowrap"><span class="text-danger">*</span>Completion Due Date</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control input-image hasDatepicker date-picker" name="due_date" value="{{$modal->due_date}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>URL</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="url" value="{{$modal->url}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Describe</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-group" id='description_editor'>
                                                <textarea class="form-control" rows="3" name="issue_description">{{$modal->issue_description}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-start">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">File</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <label class="btn btn-secondary" for="fileInp">Select file</label>
                                            <input type="file" class="form-control-file img-Inp" id="fileInp" name="picture_url[]" multiple>
                                            <div class="image-container" id="img-list">
                                                @foreach($issue_picture as $value)
                                                    @if (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                                        <div class="image-wrapper">
                                                            <input type="hidden" name="pic_url[]" value="{{$value->picture_url}}">
                                                            <img src="/{{$value->picture_url}}" class="example-image img-thumbnail">
                                                            <button class="img-delete-btn">x</button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="file-container">
                                                @foreach($issue_picture as $value)
                                                    @if (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                                    <!--img-->
                                                    @else
                                                        <div class="file-block">
                                                            <input type="hidden" name="pic_url[]" value="{{$value->picture_url}}">
                                                            <span class="file-delete-btn px-1">x</span>
                                                            <a class="fileName" href="/{{$value->picture_url}}" style="font-size: large"></a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <!------------->
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a href="/projects/{{$parentid}}/issues/@if($modal->id > 0){{$modal->id}}/view @endif" class="btn btn-lg btn-light">Cancel</a>
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
<script src="{{asset('./dashlite/assets/js/new-script.js')}}"></script>
<script>
    $('#project_change').change(function(){
        var selectedValue = $(this).val();
        $.ajax({
            url: '/change-status-with-project',
            type: 'GET',
            data: { project_id: selectedValue },
            success: function (response) {
                $('#status_change').empty();
                $.each(response.status, function(index, option){
                    $('#status_change').append($('<option>',{
                        value: option.status_id,
                        text: option.status_name
                    }));
                });
            }
        });
    });
</script>
@endsection
