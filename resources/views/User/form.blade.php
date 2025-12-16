@extends('layouts.InputLayout',[
'title' => __($title ?? 'Project')
])
@section('page-title', 'SR Board | ' .$modal->username. ' User')
@section('main')
<div class="nk-content ">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @if ($errors->has('username') && $errors->has('name') && $errors->has('email') && $errors->has('password'))
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
                        <div class="card">
                            <div class="card-inner">
                                @if ($modal->id)
                                    <h4 class="title nk-block-title mb-4">MODIFY <span class="text-blue">{{$modal->username}}</span> USER</h4>
                                @else
                                    <h4 class="title nk-block-title mb-4">CREATE NEW USER</h4>
                                @endif
                                <form action="{{route('admin.users.store')}}"
                                    class="gy-3" method="POST" enctype="multipart/form-data">
                                    @method('POST')
                                    @csrf
                                    <input type="hidden" name="id" value="{{$modal->id}}">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">PROFILE PICTURE</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                @if(!empty($modal->avatar))
                                                    <img src="/{{$modal->avatar}}" class="profile-avatar border border-light" alt="Avatar">
                                                @else
                                                    <img src="{{asset('./dashlite/images/Avt.jpeg')}}" class="profile-avatar border border-light">
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="check-active">
                                            <div class="form-group">
                                                <label class="form-label">Active</label>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="preview-block">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="customSwitch2" name="active" value="{{ $modal->active }}" @if($modal->active == 1) checked @endif>
                                                    <label class="custom-control-label" for="customSwitch2"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>USERNAME</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="username" value="{{ $modal->username }}" @if($modal->id) readonly @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>FULL NAME</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="name" value="{{ $modal->name }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>EMAIL</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="email" class="form-control" name="email" value="{{$modal->email}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                @if($modal->id)
                                                <label class="form-label">RESET PASSWORD</label>
                                                @else
                                                <label class="form-label">PASSWORD</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right is-hidden">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                    </a>
                                                    <input type="password" class="form-control" name="password" id="password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">ADD GROUP</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="form-select" multiple name="user_group_id[]">
                                                @foreach($group as $key => $value)
                                                <option value="{{$value->id}}" {{$value->active}}>{{$value->group_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Role</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <select class="form-select form-select-sm" name="role">
                                                <option value="Admin" @if ($modal && count($modal->getRoleNames()) > 0 && $modal->getRoleNames()[0] == 'Admin') selected @endif>Admin</option>
                                                <option value="User" @if ($modal && count($modal->getRoleNames()) > 0 && $modal->getRoleNames()[0] == 'User') selected @endif>User</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a href="{{route('admin.users.index')}}" class="btn btn-lg btn-light">Cancel</a>
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
<script>
    $("#customSwitch2").click(function() {
        $(this).val($(this).val() == 1 ? 0 : 1 );
    });

    //Open password
    const passwordInput = document.getElementById('password');
    const passcodeSwitch = document.querySelector('.form-icon');
    const icon = document.querySelector('.icon-show');

    passcodeSwitch.addEventListener('click', function() {
        event.preventDefault();
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('ni-eye');
            icon.classList.add('ni-eye-off');
        } else {
            passwordInput.type = 'password';
            icon.classList.add('ni-eye');
            icon.classList.remove('ni-eye-off');
        }
    });
</script>
@endsection
