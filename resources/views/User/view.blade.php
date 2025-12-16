@extends('layouts.IndexLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Your profile')
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
                <div class="nk-block">
                    <div class="card">
                        <div class="card-aside-wrap">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head nk-block-head-lg">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h4 class="nk-block-title">Personal Information</h4>
                                            <div class="nk-block-des">
                                                <p><span class="text-danger">*</span>Please change your Password at first login!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(isset($mode) && $mode=='edit')
                                    <!-----EDIT----->
                                    <form action="{{route('user.edit_submit')}}" class="gy-3" method="POST" id='form1' enctype="multipart/form-data">
                                        @csrf
                                        @Method('POST')
                                        <input type="hidden" name="id" value="{{Auth::user()->id}}">
                                        <input type="hidden" name="isUser" value="true">
                                        <div class="nk-block">
                                            <div class="nk-data data-list">
                                                <div class="data-head">
                                                    <h6 class="overline-title">Basics</h6>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Profile picture</span>
                                                        <div class="col-lg-6">
                                                            <div class="image-container" id="filesList">
                                                                @if(!empty(Auth::user()->avatar))
                                                                    <input type="hidden" name="avatar" value="{{Auth::user()->avatar}}">
                                                                    <img src="/{{Auth::user()->avatar}}" class="profile-avatar border border-light">
                                                                @else
                                                                    <img src="{{asset('./dashlite/images/Avt.jpeg')}}" class="profile-avatar border border-light">
                                                                @endif
                                                                <label class="overlay" for="fileInp">
                                                                    <span class="overlay-text"></span>
                                                                </label>
                                                            </div>
                                                            <input type="file" class="form-control-file img-Inp" id="fileInp" name="avatar">
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="data-value" name="id" value="{{Auth::user()->id}}">
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Full Name</span>
                                                        <input class="data-value form-control form-control-lg" name="name" value="{{Auth::user()->name}}">
                                                    </div>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Username</span>
                                                        <input class="data-value form-control form-control-lg" value="{{Auth::user()->username}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Email</span>
                                                        <input class="data-value form-control form-control-lg" name="email" value="{{Auth::user()->email}}">
                                                    </div>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Group name</span>
                                                        <input class="data-value form-control form-control-lg" value="{{$group}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Password</span>
                                                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg is-hidden" data-target="password">
                                                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                        </a>
                                                        <input autocomplete="new-password" type="password" class="form-control form-control-lg" id="password" name="password">
                                                    </div>
                                                </div>
                                                <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                    <div class="data-col form-control-wrap">
                                                        <span class="data-label" style="width:200px;">Confirm password</span>
                                                        <input type="password" class="data-value form-control form-control-lg" name="password_confirmation" id="password_confirmation">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 justify-content-end">
                                            <div>
                                                <div class="form-group mt-2">
                                                    <button type="submit" class="btn btn-lg btn-primary" id='button1'>Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <!-- --ORIGINAL-- -->
                                    <div class="nk-block">
                                        <div class="nk-data data-list">
                                            <div class="data-head">
                                                <h6 class="overline-title">Basics</h6>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">Profile picture</span>
                                                    @if(!empty(Auth::user()->avatar))
                                                    <img src="/{{Auth::user()->avatar}}" class="profile-avatar border border-light">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">Full Name</span>
                                                    <span class="data-value">{{Auth::user()->name}}</span>
                                                </div>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">Username</span>
                                                    <span class="data-value">{{Auth::user()->username}}</span>
                                                </div>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">Group</span>
                                                    <span class="data-value">{{$group}}</span>
                                                </div>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">Email</span>
                                                    <span class="data-value">{{Auth::user()->email}}</span>
                                                </div>
                                            </div>
                                            <div class="data-item" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label" style="width:200px;">password</span>
                                                    <span class="data-value">************</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 justify-content-end">
                                        <div>
                                            <div class="form-group mt-2">
                                                <a href="{{route('user.profile_edit',['mode' => 'edit'])}}" class="btn btn-lg btn-primary">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //avatar
    const fileInp = document.getElementById('fileInp');
    const imageContainer = document.querySelector('.image-container');

    fileInp.onchange = evt => {
        const existingImage = imageContainer.querySelector('img');
        if (existingImage) {
            existingImage.remove();
        }

        const files = fileInp.files;
        fileInp_files = files;
        for(let i = 0; i < files.length; i++){
            var image = document.createElement('img');
                image.src = URL.createObjectURL(files[i]);
                image.setAttribute('class', 'profile-avatar border border-light');
                image.id = 'avatarImage';

            imageContainer.appendChild(image);
        }
    }

    //Open password
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const passcodeSwitch = document.querySelector('.passcode-switch');

    passcodeSwitch.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordConfirmationInput.type = 'text';
        } else {
            passwordInput.type = 'password';
            passwordConfirmationInput.type = 'password';
        }
    });
</script>
@endsection
