<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./dashlite/images/favicon.png">
    <!-- Page Title  -->
    <title>Intube | SR - Board Login</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./dashlite/assets/css/dashlite.css?ver=2.5.0">
    <link id="skin-default" rel="stylesheet" href="./dashlite/assets/css/theme.css?ver=2.5.0">
    <link rel="stylesheet" href="./dashlite/assets/css/stylelogin.css">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                                <a href="#" class="toggle btn-white btn btn-icon btn-light" data-target="athPromo">
                                    <em class="icon ni ni-info"></em>
                                </a>
                            </div>
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo text-center pb-5">
                                    <a href="#" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg"
                                            src="{{asset('dashlite/images/logo.png')}}" alt="logo">
                                        <img class="logo-dark logo-img logo-img-lg"
                                            src="{{asset('dashlite/images/logo.png')}}" alt="logo-dark">
                                    </a>
                                </div>
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        <span>Incorrect username or password.</span>
                                    </ul>
                                </div>
                                @endif
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h5 class="d-flex justify-content-center">ID/PWD를 입력하신 후 로그인 해주세요</h5>                                        
                                    </div>
                                </div>
                                <form method="POST" action="{{route ('user.login_submit')}}" class="form-validate is-alter" autocomplete="off">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="email-address"> Username</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input autocomplete="off" type="text" class="form-control form-control-lg"
                                                required id="email-address" placeholder="Enter your username" name="username">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password">Password</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input autocomplete="new-password" type="password" class="form-control form-control-lg" 
                                                required id="password" name="password" placeholder="Enter your password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-lg btn-primary btn-block">Sign in</button>
                                    </div>
                                </form>
                            </div>
                            <div class="nk-block nk-auth-footer">
                                <div class="nk-block-between">
                                </div>
                                <div class="mt-3">
                                    <!-- <p>&reg; Intube.</p> -->
                                </div>
                            </div>
                        </div>
            
                        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right"
                            data-content="athPromo" data-toggle-screen="lg">                            
                            <div class="slider-wrap w-100 w-max-550px h-max-580px p-3 p-sm-5 m-auto overflow-y-hidden">
                                <div class="fake-description">
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class='loading'>
                                                <div class="nk-feature-img">
                                                    <img class="round" src="{{ asset('dashlite/images/slides/img-1.png') }}">
                                                </div>
                                            </div>
                                            <div class="nk-content py-4 loading my-4">
                                                <h4 class="nk-feature-img">e-Learning Plaftorm</h4>
                                                <div class="nk-feature-img">
                                                    Moodle 기반의 사용자 위주 학습모형 설계 및
                                                    <br>
                                                    자기 주도 학습이 가능한 On-Demand, SaaSe-Learning 서비스를 제공합니다.
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="slider-init hidden main-description" data-slick='{"dots":true, "arrows":false}'>
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{ asset('dashlite/images/slides/img-1.png') }}">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-4">
                                                <h4>e-Learning Plaftorm</h4>
                                                <div>
                                                    Moodle 기반의 사용자 위주 학습모형 설계 및
                                                    <br>
                                                    자기 주도 학습이 가능한 On-Demand, SaaSe-Learning 서비스를 제공합니다.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{asset('dashlite/images/slides/img-2.png')}}">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-4">
                                                <h4>Edutech Consulting</h4>
                                                <div>
                                                    On-Off 통합 교육 시스템 구축 컨설팅/ 학습분석 시스템 구축,
                                                    <br>
                                                    컨설팅ㆍ운영 시스템 성능 테스트 및 다양한 에듀테크 컨설팅 서비스를 제공 합니다.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slider-item">
                                        <div class="nk-feature nk-feature-center">
                                            <div class="nk-feature-img">
                                                <img class="round" src="{{asset('dashlite/images/slides/img-3.png')}}">
                                            </div>
                                            <div class="nk-feature-content py-4 p-sm-4">
                                                <h4>Education Integration</h4>
                                                <div>
                                                    학습분석 시스템, 학습데이터 레이크, 실시간 강의 저작도구,
                                                    <br>
                                                    웹 기반의 마인드맵 등 교육에 필요한 모든 서비스를 제공 합니다.
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
        </div>
    </div>
    <script src="../../../dashlite/assets/js/bundle.js?ver=2.5.0"></script>
    <script src="../../../dashlite/assets/js/scripts.js?ver=2.5.0"></script>
</html>