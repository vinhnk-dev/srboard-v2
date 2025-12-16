@extends('layouts.IndexLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | ' .$pageName.' List')
@section('main')
<div class="nk-content px-0 px-md-3">
   <div class="container-fluid px-1">
      <div class="nk-content-inner">
         <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm ml-2">
               <div class="nk-block-head-content d-flex justify-content-between">
                  <h3 class="nk-block-title page-title">{{$pageName}}</h3>
                  <div class="d-flex flex-wrap ml-auto mr-2">
                     {!!$page_left_tools!!}
                  </div>
               </div>
            </div>
            @if($hasCardCategory) @include('Sys.CardCategory') @endif
            @include('Sys.TableLayout')
         </div>
      </div>
   </div>
</div>
@endsection