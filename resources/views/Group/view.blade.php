@extends('layouts.IndexLayout',['title' => __($title ?? 'Issues')])
@section('main')
<div class="nk-content px-0 px-md-3">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid px-1">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm ml-3">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{$group->group_name}}</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="{{route('admin.group.edit',['id' => $group->id, 'inGroup' => true])}}" class=" btn btn-primary">
                                    <em class="icon ni ni-setting"></em><span>Modify Group</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-stretch overflow-auto">
                        <div class="card-inner-group">
                            <div class="card-inner">
                                <ul class="nav nav-tabs mt-n3">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#tabMember">
                                            <em class="icon ni ni-users"></em><span>Member</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tabProject">
                                            <em class="icon ni ni-wallet-saving"></em><span>Project</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tabMember">
                                        <div class="overflow-auto">
                                            <table class="nk-tb-list is-separate nk-tb-ulist">
                                                <thead>
                                                    <tr class="nk-tb-item nk-tb-head">
                                                        <th class="nk-tb-col"><span class="sub-text">FullName</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">UserName</span></th>
                                                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                        <th class="nk-tb-col nk-tb-col-tools"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($users as $value)
                                                        <tr class="nk-tb-item">
                                                            <td class="nk-tb-col">
                                                                <h6 class="title text-nowrap">{{$value -> name}}</h6>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <span>{{$value -> username}}</span>
                                                            </td>
                                                            <td class="nk-tb-col">
                                                                <span class="tb-status text-success">Active</span>
                                                            </td>
                                                            <td class="nk-tb-col d-flex flex-row-reverse">
                                                                <a class="btn btn-lg btn-danger p-1 text-white" onclick="remove({{$value->group_id}},{{$value->user_id}})">
                                                                    <em class="d-sm-none icon ni ni-trash"></em>
                                                                    <span class="d-none d-sm-inline">Delete</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane overflow-auto" id="tabProject">
                                        <table class="nk-tb-list is-separate nk-tb-ulist">
                                            <thead>
                                                <tr class="nk-tb-item nk-tb-head">
                                                    <th class="nk-tb-col"><span class="sub-text text-nowrap">Project Name</span></th>
                                                    <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                                    <th class="nk-tb-col"><span class="sub-text">URL</span></th>
                                                    <th class="nk-tb-col"><span class="sub-text text-nowrap">Created at</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($project as $key => $value)
                                                <tr class="nk-tb-item">
                                                    <td class="nk-tb-col">
                                                        <a href="/projects/{{$value->id}}/issues" class="project-title">
                                                            <div class="project-info">
                                                                <h6 class="title text-nowrap">{{$value -> project_name}}</h6>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td class="nk-tb-col">                                                        
                                                        @if($value -> active == 0)
                                                            <span class="tb-status text-danger text-nowrap">Disable</span>
                                                        @else
                                                            <span class='tb-status text-success'>Active</span>
                                                        @endif
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <div class="project-list-progress">
                                                            <span>{{$value -> url}}</span>
                                                        </div>
                                                    </td>
                                                    <td class="nk-tb-col">
                                                        <div class="project-list-progress">
                                                            <span class="text-nowrap">{{$value -> created_at}}</span>
                                                        </div>
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
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modalAlert2">
    <div class="modal-dialog modal-sm" role="document">
       <div class="modal-content">
          <div class="modal-body text-center">
             <div class="nk-modal">
                <h5 class="nk-modal-title">Are you sure to delete this user from this Group?</h5>
                <div class="nk-modal-action">
                   <a id="delete_confirm" class="btn btn-sm btn-primary mr-3 text-white">Yes</a>
                   <a class="btn btn-sm btn-secondary text-white" data-dismiss="modal">Cancel</a>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
 <script>
    function remove(gid,uid) {
   $("#modalAlert2").modal();
   $("#delete_confirm").attr('href', `/admin/group/${gid}/member/delete/${uid}`);
}
 </script>
@endsection
