<div class="nk-sidebar nk-sidebar-fixed is-light" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand mt-2">
            <a href="{{route('mytask')}}" class="logo-link nk-sidebar-logo">
                <img class="logo-dark logo-img" src="{{asset('./dashlite/images/logoEDITED.png')}}">
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar><br>
                <ul class="nk-menu">
                    <li class="nk-menu-item has-sub active current-page" id="ProjectMenu">
                        <a href="#" class="nk-menu-toggle pl-2 pb-1">
                            <span class="overline-title text-primary-alt">Projects</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @foreach($myProjects as $project)
                                <li class="nk-menu-item border-bottom">
                                    <a href="/projects/{{$project->id}}/issues" class="nk-menu-link nk-menu-link-border">
                                        <span class="nk-menu-text text-sidebar">{{ $project->project_name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <br>
                    <li class="nk-menu-item has-sub" id="MaintenanceMenu">
                        <a href="#" class="nk-menu-toggle pl-2 pb-1">
                            <span class="overline-title text-primary-alt">Maintenance</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @foreach($myMaintenances as $project)
                                <li class="nk-menu-item border-bottom">
                                    <a href="/projects/{{$project->id}}/issues" class="nk-menu-link nk-menu-link-border replace">
                                        <span class="nk-menu-text text-sidebar">{{ $project->project_name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    @if (auth()->user()->hasRole('Admin'))
                    <li class="nk-menu-heading">
                        <h6 class="overline-title text-primary-alt">Management</h6>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{route('admin.projects.index')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-tile-thumb-fill"></em></span>
                            <span class="nk-menu-text">Projects</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{route('admin.status.index')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-swap-alt-fill"></em></span>
                            <span class="nk-menu-text">Status</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{route('admin.users.index')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">User</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{route('admin.group.index')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                            <span class="nk-menu-text">Group</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="{{route('admin.board.index')}}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-calender-date-fill"></em></span>
                            <span class="nk-menu-text">Board</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
