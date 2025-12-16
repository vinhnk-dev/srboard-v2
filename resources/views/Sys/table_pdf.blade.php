@extends('layouts.IndexLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | ' .$pageName.' List')
@section('main')
<div class="overflow-auto">
   <table class="nk-tb-list is-separate nk-tb-ulist">
      <thead>
         <tr class="nk-tb-item nk-tb-head">
            @foreach($tableHead as $k=>$v)
            <th class="nk-tb-col"><span class="sub-text text-nowrap">{{$v}}</span></th>
            @endforeach
         </tr>
      </thead>
      <tbody>
         @foreach($list as $modal)
         <tr class="nk-tb-item ">
            @foreach($tableHead as $k=>$v)
            <td class="nk-tb-col"><span class="sub-text text-nowrap">{!!$modal->getCellValue($k)!!}</span></td>
            @endforeach
         </tr>
         @endforeach
      </tbody>
   </table>
</div>
@endsection