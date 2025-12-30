@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'Group | Edit ' .$modal->group_name)
@section('main')
<div class="nk-content ">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="card p-3">
                            <div class="card-inner">
                                <h4 class="title nk-block-title">GROUP</h4>
                               <form action="{{ $modal->id ? route('admin.group.update',$modal->id) : route('admin.group.store') }}"
                                        class="gy-3"
                                        method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    <input type="hidden" name="id" value="{{$modal->id}}">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>GROUP NAME</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="group_name" value="{{$modal->group_name}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Member</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="form-select form-select-sm" name="user_group_id[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($user as $key => $value)
                                                    <option value="{{$value->id}}" id="selected_{{$value->id}}" @if($value->assigned) selected @endif > {{$value -> name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Project</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 d-flex flex-column">
                                            <select class="form-select form-select-sm" name="group_assign_id[]" aria-label=".form-select-sm example" multiple>
                                                @foreach($project as $key => $value1)
                                                    <option value="{{$value1->id}}" @if($value1->assigned) selected @endif>{{$value1 ->project_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a href="{{route('admin.group.index')}}" class="btn btn-lg btn-light">Cancel</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-lg btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if($modal->id)
                     <div class="nk-block">
                        <div class="card card-stretch">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <ul class="nav nav-tabs mt-n3">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#tabItem5">
                                                <em class="icon ni ni-users"></em><span>Member</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tabItem6">
                                                <em class="icon ni ni-wallet-saving"></em><span>Project</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tabItem5">
                                            <div class="overflow-auto">
                                                <table class="nk-tb-list is-separate nk-tb-ulist">
                                                    <thead>
                                                        <tr class="nk-tb-item nk-tb-head">
                                                            <th class="nk-tb-col nk-tb-col-check">
                                                                <div class="custom-control custom-control-sm d-flex">
                                                                    <input type="checkbox" id="checkAll">
                                                                </div>
                                                            </th>
                                                            <th class="nk-tb-col"><span class="sub-text">FullName</span></th>
                                                            <th class="nk-tb-col"><span class="sub-text">UserName</span></th>
                                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                            <th class="nk-tb-col nk-tb-col-tools"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($user as $key => $value)
                                                            <tr class="nk-tb-item">
                                                                <td class="nk-tb-col">
                                                                    <div class="custom-control custom-control-sm d-flex">
                                                                        <input id="form-user_{{$value->id}}" type="checkbox"  @if($value->assigned) checked @endif  class="check" onclick="membercheck({{$value->id}})">
                                                                    </div>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <h6 class="title">{{$value -> name}}</h6>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span>{{$value -> username}}</span>
                                                                </td>
                                                                <td class="nk-tb-col">
                                                                    <span class="tb-status text-success">Active</span>
                                                                </td>
                                                                <td class="nk-tb-col d-flex flex-row-reverse">
                                                                    <a class="btn btn-lg btn-danger text-white p-1">
                                                                        <em class="d-sm-none icon ni ni-trash"></em><span class="d-none d-sm-inline" onclick="selected({{$value->id}})">Add</span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane overflow-auto" id="tabItem6">
                                            <table class="nk-tb-list is-separate nk-tb-ulist">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col nk-tb-col-check">
                                                            <div class="custom-control custom-control-sm d-flex">
                                                                <input type="checkbox" id="checkAllProject">
                                                            </div>
                                                        </th>
                                                        <th class="nk-tb-col"><span class="sub-text text-nowrap">Project Name</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Active</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">URL</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text text-nowrap">Created at</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools text-right"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($project as $key => $value1)
                                                        <tr class="nk-tb-item">
                                                            <td class="nk-tb-col">
                                                                <div class="custom-control custom-control-sm d-flex">
                                                                    <input id="form_project_{{$value1->id}}"type="checkbox" class="check-project"  @if($value1->assigned) checked @endif onclick="projectchecked({{$value1->id}})">
                                                                </div>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <a href="/projects/{{$value->id}}/issues" class="project-title">
                                                                    <div class="project-info">
                                                                        <h6 class="title">{{$value1->project_name}}</h6>
                                                                    </div>
                                                                </a>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                @if($value1 -> active == 0)
                                                                    <span class="tb-status text-danger">Not Activate</span>
                                                                @else
                                                                    <span class='tb-status text-success'>Activated</span>
                                                                @endif
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <div class="project-list-progress">
                                                                    <span>{{$value1 -> url}}</span>
                                                                </div>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <div class="project-list-progress">
                                                                    <span>{{$value1 -> created_at}}</span>
                                                                </div>
                                                            </td>
                                                            <td class="nk-tb-col d-flex flex-row-reverse">
                                                                <a class="btn btn-lg btn-danger text-white p-1">
                                                                    <em class="d-sm-none icon ni ni-trash"></em><span class="d-none d-sm-inline" onclick="prjselected({{$value1->id}})">Add</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //member
    function selected(id) {
        var member_arr = [];
        var mb_selected = $("select[name='user_group_id[]']").select2('data');

        for (let i = 0; i < mb_selected.length; ++i) {
            member_arr.push(mb_selected[i]['id']);
        }

        if (!member_arr.includes(id.toString())) {
            member_arr.push(id.toString());
        }

        $('#form-user_' + id).prop('checked', true);
        $("select[name='user_group_id[]']").val(member_arr).trigger('change');
    }

    function membercheck(id) {
        if($('#form-user_'+id).prop('checked')){ selected(id); }
        else{
            var mb_checked = $("select[name='user_group_id[]']").select2('data');
            var member_arr = [];
            for (let i = 0; i < mb_checked.length; ++i) {
                if(id != mb_checked[i]['id']){
                    member_arr.push(mb_checked[i]['id']);
                }
            }
            $("select[name='user_group_id[]']").val(member_arr).trigger('change');
        }
    }

    $(document).ready(function () {
        $("#checkAll").click(function () {
            if ($(this).prop("checked")) {
                $("select[name='user_group_id[]'] option").prop("selected", true);
            } else {
                $("select[name='user_group_id[]'] option").prop("selected", false);
            }
            $("select[name='user_group_id[]']").trigger("change");
        });
    });

    //project
    function prjselected(id){
        var project_arr = [];
        var prj_selected = $("select[name='group_assign_id[]']").select2('data');

        for (let i = 0; i < prj_selected.length; ++i) {
            project_arr.push(prj_selected[i]['id']);
        }

        if (!project_arr.includes(id.toString())) {
            project_arr.push(id.toString());
        }

        $('#form_project_' + id).prop('checked', true);
        $("select[name='group_assign_id[]']").val(project_arr).trigger('change');
    }

    function projectchecked(id){
        if($('#form_project_'+id).prop('checked')){
            prjselected(id);
        }else{
            var prj_checked = $("select[name='group_assign_id[]']").select2('data');
            var project_arr = [];
            for (let i = 0; i < prj_checked.length; ++i) {
                if(id != prj_checked[i]['id']){
                    project_arr.push(prj_checked[i]['id']);
                }
            }
            $("select[name='group_assign_id[]']").val(project_arr).trigger('change');
        }
    }

    $(document).ready(function () {
        $("#checkAllProject").click(function () {
            if ($(this).prop("checked")) {
                $("select[name='group_assign_id[]'] option").prop("selected", true);
            } else {
                $("select[name='group_assign_id[]'] option").prop("selected", false);
            }
            $("select[name='group_assign_id[]']").trigger("change");
        });
    });
</script>
@endsection
