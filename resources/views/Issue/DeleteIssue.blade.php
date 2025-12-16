@extends('layouts.IndexLayout',[
'title' => __($title ?? 'Project')
])
@section('page-title', 'SR Board | Delete ' .$issue->title)
@section('main')
<div class="nk-content">
    <div class="container-fluid px-0 px-md-2">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md mx-auto">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h4 class="title nk-block-title">Delete <span class="text-blue">{{$issue->title}}</span> : </h4>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-inner">
                                <form method="POST" action="{{route ('issue.delete', ['id' => $issue->id, 'projectId' => $project->id])}}" class="gy-3">
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    @method('DELETE')
                                    @csrf
                                    <h5 class="text-center"> Are you sure about that? </h5>
                                    <div class="row g-3">
                                        <div class="col-lg-6 offset-lg-3 text-center">
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-lg btn-primary">Yes</button>
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
@endsection