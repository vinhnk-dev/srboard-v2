@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'Notice | ' .$board->title)
@section('main')
<div class="nk-content">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @if ($errors->has('project_name') && $errors->has('url') && $errors->has('git_url') && $errors->has('description'))
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
                        <div class="card p-3">
                            <div class="card-inner">
                                <h4 class="title nk-block-title mb-5 pl-2">Notice</h4>
                                <div class="row g-3 align-center pl-2">
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <label class="form-label"><h5>{{$board->title}} <span class="text-gray">[{{$comment_cnt}}]</span></h5></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 align-center pl-2">
                                    <div>
                                        @if(!empty($writer->avatar))
                                            <div class="user-avatar sm"><img src="/{{$writer->avatar}}" class="avatar-sm"></div>
                                        @else
                                            <div class="user-avatar sm"><img src="{{asset('./dashlite/images/Avt.jpeg')}}"></div>
                                        @endif
                                    </div>
                                    <div class="d-flex">
                                        <div><h5>{{$writer->name}}</h5></div>
                                        <div class="notice-date text-gray">{{ $board->created_at->format('m/d/Y h:i A') }}</div>
                                    </div>
                                </div>
                                <div class="row g-3 align-start">
                                    <div class="col-lg-7">
                                        <div class="form-group" id='description_editor'>
                                            <textarea class="form-control" id="description_textarea" rows="3" name="board_content" placeholder="*Please enter description">{{$board->board_content}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 align-start">
                                    <div class="col-lg-6">
                                        <div class="image-container image-wrapper" id="img-list">
                                            @foreach($board_file as $value)
                                                @if (isset($value->file_url) && is_string($value->file_url) && (Str::endsWith($value->file_url, ['.jpg', '.jpeg', '.png', '.gif','.jfif'])))
                                                <a href="/{{$value->file_url}}">
                                                    <img class="img-thumbnail" src="/{{$value->file_url}}">
                                                </a>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="file-container">
                                            @foreach($board_file as $value)
                                                @if (isset($value->file_url) && is_string($value->file_url) && (Str::endsWith($value->file_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                                    <!--img-->
                                                @else
                                                <div class="file-wrapper">
                                                    <a class="icon ni ni-clip fileName" href="/{{$value->file_url}}" style="font-size: large" download></a>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @if (auth()->user()->hasRole('Admin'))
                                <div class="row g-3 pt-3">
                                    <div>
                                        <div class="form-group">
                                            <a href="{{route('admin.board.index')}}" class="btn btn-lg btn-light">List</a>
                                        </div>
                                    </div>
                                    <div>                                        
                                        <div class="form-group">
                                            <a href="{{route('admin.notice.edit',['id' => $board->id])}}" class="btn btn-lg btn-primary">Edit</a>
                                        </div>                                        
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Comment -->
            <div class="nk-reply-form-title ml-5 mt-5">
                <div class="title">Comment as:</div><span class="lead-text">{{Auth::user()->name}}</span>
            </div>
            <form action="{{route('notice.comment',['id' => $board->id])}}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                <input type="hidden" name="board_id" value="{{$board->id}}">
                @method('POST')
                @csrf
                <div class="tab-pane active" id="reply-form">
                    <div class="nk-reply-form-editor">
                        <div class="nk-reply-form-field" id='description_editor'>
                            <textarea class="form-control form-control-simple no-resize" id="description_cmt" name="comment"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary ml-5" type="submit">Comment</button>
                    </div>
                </div>
            </form><hr>
            @foreach ($comment as $value)
                <div class="nk-msg-reply nk-reply" data-simplebar>
                    <div class="nk-reply-item">
                        <div class="nk-reply-header">
                            <div class="user-card">
                                @if(!empty($value->avatar))
                                    <div class="user-avatar"><img src="/{{$value->avatar}}" class="user-avatar-sm avatar-sm"></div>
                                @else
                                    <div class="user-avatar"><img src="{{asset('./dashlite/images/Avt.jpeg')}}" class="user-avatar-sm avatar-sm"></div>
                                @endif
                                <div class="user-name">{{$value->username}}</div>
                            </div>
                            <div class="date-time">
                                <button class="btn btn-gray p-1" onclick="openEditCmt('comment_box_{{$value->id}}');"><em class="icon ni ni-edit"></em></button>
                                <button type="button" class="btn btn-danger p-1" onclick='delcomment({{$board->id}},{{$value->id}})'><em class="icon ni ni-trash"></em></button>
                            </div>
                        </div>
                        <div class="nk-reply-body">
                            <div class="info_comment_box_{{$value->id}} nk-reply-entry entry">{!! $value->comment !!}</div>
                            <div id="edt_comment_box_{{$value->id}}" class="d-none">
                                <form action="{{route('notice.edit.comment',[ 'id' => $value->id ,'boardId' => $board->id])}}" class="gy-3" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="comment_id" value="{{$value->id}}">
                                    @method('POST')
                                    @csrf
                                    <textarea class="form-control mt-2" name="comment" id="description_cmt">{{ $value->comment }}</textarea>
                                    <button type="submit" class="btn btn-primary mt-1">Submit</button>
                                    <button type="button" class="btn btn-danger mt-1" onclick="cancel('comment_box_{{$value->id}}')">Cancel</button>
                                </form>
                            </div>
                            <div class="info_comment_box_{{$value->id}} nk-reply-from">Time: {{\Carbon\Carbon::parse($value->created_at)->format('m/d/Y h:i A')}}</div>
                        </div><hr>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- PopUp delete -->
<div class="modal fade" tabindex="-1" id="modalAlert2">
    <div class="modal-dialog" role="document">
       <div class="modal-content">
          <div class="modal-body text-center">
             <div class="nk-modal">
                <h5 class="nk-modal-title">Are you sure you want to delete this comment?</h5>
                <div class="nk-modal-action">
                   <a id="comment_del_confirm" class="btn btn-lg btn-danger">Yes</a>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
 <!-- PopUp delete -->

<script>
    tinymce.init({
        selector: '#description_textarea',
        readonly: true,
        toolbar: false,
        menubar: false,
        branding: false,
        height: 400
    });

    tinymce.init({
        selector: '#description_cmt',
        toolbar_mode: 'scrolling',
        menubar: false,
        branding: false,
        height: 250,
        toolbar: "fontfamily fontsize forecolor bold italic align indent outdent checklist bullist numlist link image codesample code",
        plugins: "lists link image codesample code",
        font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
        content_style: "@import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap'); body { font-family: Verdana; font-size: 10pt;}",
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
    });

    //Lay ten file
    var fileLinks = document.querySelectorAll('.fileName');

    fileLinks.forEach(function(link) {
        var url = link.getAttribute('href');
        var fileName = getFileNameFromURL(url);
        link.innerText = fileName;
    });

    function getFileNameFromURL(url) {
        var pathArray = url.split('/');
        var fileNameWithParams = pathArray[pathArray.length - 1];
        var fileName = fileNameWithParams.split('?')[0]; // Remove any query parameters if present
        return decodeURIComponent(fileName);
    }

    //simple lightbox
    (function() {
        var $gallery = new SimpleLightbox('#img-list a', {});
    })();

    //Edit Comment
    function openEditCmt(name) {
        $("#edt_" + name).removeClass('d-none');
        $(".info_" + name).addClass('d-none');
    }

    function cancel(name){
        $("#edt_" + name).addClass('d-none');
        $(".info_" + name).removeClass('d-none');
    }
    
    function delcomment(boardId, cmtid) {
        $("#modalAlert2").modal();
        $("#comment_del_confirm").attr('href', '/notice/'+boardId+'/view/comment/'+cmtid+'/delete');
    }
</script>
@endsection
