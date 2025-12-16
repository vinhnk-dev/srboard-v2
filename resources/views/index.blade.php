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
                        <div class="nk-block-between">
                           <div class="nk-block-head-content d-flex flex-nowrap">
                              <div class="nk-block-between">
                                 <div class="nk-block-head-content d-flex flex-nowrap">
                                    <a href="{{route('mytask')}}"><h3 class="nk-block-title page-title not-active">My task</h3></a>
                                    <h4 class="mx-2">|</h4>
                                    <h3 class="nk-block-title page-title">Project</h3>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="nk-block">
                  <div class="row g-gs">
                     @foreach($projects as $project)
                        <div class="col-lg-3 col-md-6">
                           <a href="/projects/{{$project->id}}/issues">
                              <div class="card card-bordered pricing">
                                 <div class="pricing-head">
                                    <div class="pricing-title">
                                       <h5 class="card-title title">{{$project->project_name}}</h5>
                                    </div>
                                 </div>
                                 <div class="pricing-body">
                                    <ul class="pricing-features">
                                       @foreach ( $project->Status as $st )
                                       <li>
                                          <a href="/projects/{{$project->id}}/issues?status_search={{$st->status_id}}">
                                             <span class="text-reset stt-dashboard"><em class="mr-1 icon ni ni-piority-fill"></em>{{$st->status_name}}</span>
                                          </a>
                                          <span class="ml-auto stt-dashboard">{{$st->stCount}}</span>
                                       </li>
                                       @endforeach
                                       <li>
                                          <span class="text-danger stt-dashboard"><em class="mr-1 icon ni ni-alert-circle"></em>Over Due</span>
                                          <span class="ml-auto stt-dashboard">{{$project->overdue}}</span>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                           </a>
                        </div>
                     @endforeach
                  </div>
               </div>
               <div class="nk-block-head nk-block-head-sm">
                  <div class="nk-block-between">
                     <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Maintenance</h3>
                     </div>
                  </div>
               </div>
               <div class="nk-block">
                  <div class="row g-gs">
                     @foreach($maintenances as $project )
                        <div class="col-lg-3 col-md-6">
                           <a href="/projects/{{$project->id}}/issues">
                              <div class="card card-bordered pricing">
                                 <div class="pricing-head">
                                    <div class="pricing-title">
                                       <h5 class="card-title title">{{$project->project_name}}</h5>
                                    </div>
                                 </div>
                                 <div class="pricing-body">
                                 <ul class="pricing-features">
                                       @foreach ( $project->Status as $st )
                                          <li>
                                             <a href="/projects/{{$project->id}}/issues?status_search={{$st->status_id}}">
                                                <span class="text-reset stt-dashboard"><em class="mr-1 icon ni ni-piority-fill"></em>{{$st->status_name}}</span>
                                             </a>
                                             <span class="ml-auto stt-dashboard">{{$st->stCount}}</span>
                                          </li>
                                       @endforeach
                                       <li>
                                          <span class="text-danger stt-dashboard"><em class="mr-1 icon ni ni-alert-circle"></em>Over Due</span>
                                          <span class="ml-auto stt-dashboard">{{$project->overdue}}</span>
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                           </a>
                        </div>
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
