@extends('layouts.IndexLayout',['title' => __($title ?? 'Intube')])
@section('page-title', 'Intube | SR - Board')
@section('main')
<div class="nk-content">
   <div class="container-fluid">
      <div class="nk-content-inner">
         <div class="nk-content-body">
            @include('Board.BoardLayout')
            <div class="nk-block">
               <div class="nk-block-head nk-block-head-sm">
                  <div class="nk-block-between">
                     <div class="nk-block-head-content d-flex flex-nowrap">
                        <h3 class="nk-block-title page-title">My task</h3>
                        <h4 class="mx-2">|</h4>
                        <a href="{{route('index')}}"><h3 class="nk-block-title page-title not-active">Project</h3></a>
                     </div>
                  </div>
               </div>
               <div class="nk-block-project">
               @include('Sys.TableLayout')
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
