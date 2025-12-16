<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
@include('layouts.index.Head',['title'=>__($title ??'Intube')])
</head>

<body class="nk-body ui-rounder npc-default has-sidebar ">
    <div class="nk-app-root">
        <!-- side-bar -->
        @include('layouts.index.Sidebar')
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                @include('layouts.index.Header')
                <!-- main header @e -->
                <!-- content @s -->
                @yield('main')
                <!-- content @e -->
            </div>                  
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    @include('layouts.index.Footer')
    <!-- JavaScript -->
    @include('layouts.index.scripts')
</body>
</html>
