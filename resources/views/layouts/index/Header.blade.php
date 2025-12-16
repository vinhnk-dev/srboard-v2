<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                    <em class="icon ni ni-menu"></em>
                </a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{route('index')}}" class="logo-link">
                    <img class="logo-light logo-img" src="{{asset('./dashlite/images/logo.png')}}" style="height: 30px;">
                    <img class="logo-dark logo-img" src="{{asset('./dashlite/images/logo.png')}}">
                </a>
            </div>
            <div class="p-dev-btn d-none d-sm-inline">
                @if(in_array(1, \App\Models\User::getGroupIds()))
                    <a href="{{config('srboard.headerButton.devR')}}" class="btn btn-outline-info" target="_blank">Dev R&R</a>
                @endif
                @if (auth()->user()->hasRole('Admin'))
                    <a href="#" class="btn btn-outline-info" data-toggle="modal" data-target="#Popup">Server Management</a>
                @endif
            </div>
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li>
                        <a href="{{config('srboard.headerButton.998')}}" target="_blank">
                            <img src="{{asset('./dashlite/images/remote_support_icon.png')}}" alt="remote_support_icon">
                        </a>
                    </li>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                            <div class="user-toggle">
                                @if (!empty(Auth::user()->avatar))
                                    <div class="user-avatar sm">
                                        <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @else
                                    <div class="user-avatar sm">
                                        <img src="{{asset('./dashlite/images/Avt.jpeg')}}">
                                    </div>
                                @endif
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-name dropdown-indicator">{{Auth::user()->name}}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-inner user-card-wrap bg-lighter">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        @if (!empty(Auth::user()->avatar))
                                            <div class="user-avatar-sm">
                                                <img src="{{ asset(Auth::user()->avatar) }}" alt="Avatar"  style="width:100%;height:100%;object-fit:cover;">
                                            </div>
                                        @else
                                            <div class="user-avatar-sm">
                                                <img src="{{asset('./dashlite/images/Avt.jpeg')}}">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{Auth::user()->name}}</span>
                                        <span class="sub-text">{{Auth::user()->username}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="{{route('user.profile')}}">
                                            <em class="icon ni ni-cc-alt2"></em>
                                            <span>View Profile</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <li>
                                            <a href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                <em class="icon ni ni-signout"></em>
                                                <span>Sign out</span>
                                            </a>
                                        </li>
                                    </form>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
