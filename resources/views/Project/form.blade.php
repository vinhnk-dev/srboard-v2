@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Project')
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
                                <h4 class="title nk-block-title mb-5">PROJECT</h4>
                                <form id="edit_project_form" class="gy-3" method="POST" enctype="multipart/form-data"
                                        action="{{route ('admin.projects.store')}}">
                                    {{-- <input type="hidden" name="groupId" value="{{$groupId}}" /> --}}
                                    <input type="hidden" name="id" value="{{$modal->id}}">
                                    @csrf
                                    <div class="row g-3 align-center">
                                        <div class="check-active">
                                            <div class="form-group">
                                                <label class="form-label">Active</label>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="preview-block" id="active_container">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" name="active" value="1" id="customSwitch2"
                                                            @if($modal->active==1) checked @endif>
                                                    <label class="custom-control-label" for="customSwitch2"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="check-active">
                                            <div class="form-group">
                                                <label class="form-label">Project type</label>
                                            </div>
                                        </div>
                                        <div class="mb-auto">
                                            <div class="form-check d-inline mr-4">
                                                <input class="form-check-input" type="radio" name="project_type" id="flexRadioDefault2" value="Project" @if($modal->project_type == 'Project') checked @endif>
                                                <label class="form-check-label h6">Project</label>
                                            </div>
                                            <div class="form-check d-inline">
                                                <input class="form-check-input" type="radio" name="project_type" id="flexRadioDefault1" value="Maintenance" @if($modal->project_type == 'Maintenance') checked @endif>
                                                <label class="form-check-label h6">Maintenance</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>PROJECT ID</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="project_code" name="project_code"  @if ($modal->project_code) value="{{$modal->project_code}}" @else value="{{ strtoupper(Str::random(2)) }}" @endif data-container="body" data-placement="top">
                                                    <div id="project-code-error" style="color: red;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>PROJECT NAME</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="project_name" value="{{$modal->project_name}}">
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
                                        <div class="col-lg-6">
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
                                                <label class="form-label">Add group</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 d-flex flex-column">
                                            <select class="form-select form-select-sm" name="group_assignment_id[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($group as $key => $value)
                                                    <option value="{{$value->id}}" {{$value -> active}}>{{$value -> group_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-start">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="form-select form-select-sm" name="status_id[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($status as $key => $value)
                                                    <option value="{{$value->id}}" {{$value->active}}>{{$value->status_name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="status-block">
                                                @foreach($status as $key => $status2)
                                                    @if($status2->active)
                                                    <div class="status-item border border-dark rounded w-25 p-1 mt-2 d-inline-block row mx-0">
                                                        <div class="d-flex justify-content-between flex-nowrap align-items-center">
                                                            <div class="text-truncate mw-150px">{{$status2->status_name}}</div>
                                                            <div class="d-flex">
                                                                <div class="custom-control custom-control-sm custom-radio">
                                                                    <input name="show[]" id="{{$status2->id}}" class="custom-control-input" type="checkbox" onclick="check_limited()" value="{{$status2->id}}" {{$status2->check}}>
                                                                    <label class="custom-control-label" for="{{$status2->id}}"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Git URL</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" name="git_url" class="form-control" name="git_url" value="{{$modal->git_url}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Description</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="description" value="{{$modal->description}}">{{$modal->description}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a href="{{route('admin.projects.index')}}" class="btn btn-lg btn-light">Cancel</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group mt-2">
                                                <input type="submit" value="Update" class="btn btn-lg btn-primary">
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

<script>
    $(document).ready(function () {
        $("#project_code").on("keyup", function () {
            checkProjectCode();
        });

        function checkProjectCode() {
            var projectCode = $("#project_code").val();
            $.ajax({
                type: "GET",
                url: "/admin/check-project-code",
                data: { project_code: projectCode },
                success: function (response) {
                    if (response.exists) {
                        $("#project-code-error").text("the code already exits.");
                    } else {
                        $("#project-code-error").text("");
                    }
                }
            });
        }
    });
</script>

@endsection
