@extends('layouts.InputLayout', ['title' => __($title ?? 'Issue')])
@section('page-title', 'Issue | ' .$issue->title)
@section('main')
<div class="nk-content">
   @if ($errors->any())
   <div class="alert alert-danger">
      <ul>
         @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
         @endforeach
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
                        <h4 class="title nk-block-title mb-4">ISSUE DETAILS</h4>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Title</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <div class="form-group">
                                 <div class="form-control-wrap">
                                    <input type="text" class="form-control" name="title" value="{{$issue->title}}" readonly>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Project ID</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <input type="text" class="form-control" name="title" value="{{$issue->project_code}}-{{$issue->id}}" readonly>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Status</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <input type="text" class="form-control" name="title" value="{{$issue->status_name}}" readonly>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Reporter</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <input type="text" class="form-control" name="title" value="{{$issue->reporters}}" readonly>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Assign</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <input type="text" class="form-control" name="title" value="{{$issue->users}}" readonly>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label text-nowrap">Completion Due Date</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <div class="form-group">
                                 <div class="form-control-wrap">
                                    <input type="text" class="form-control input-image hasDatepicker" name="url" value="{{$issue->due_date}}" readonly>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">URL</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <div class="form-group">
                                 <div class="form-control-wrap">
                                    <a href="{{$issue->url}}" class="form-control h-auto text-break" name="url" target="_blank" readonly>{{$issue->url}}</a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="row g-3 align-center">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">Describe</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <div class="form-group" id='description_editor'>
                                 <textarea class="form-control" id="description_textarea" rows="3" name="issue_description">{{$issue->issue_description}}</textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row g-3 align-start">
                           <div class="col-lg-2">
                              <div class="form-group">
                                 <label class="form-label">File</label>
                              </div>
                           </div>
                           <div class="col-lg-10">
                              <div class="image-container image-wrapper" id="img-list">
                                 @foreach($issue->pictures as $value)
                                 @if (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                 <a href="/{{$value->picture_url}}">
                                    <img class="img-thumbnail" src="/{{$value->picture_url}}">
                                 </a>
                                 @endif
                                 @endforeach
                              </div>
                              <div class="file-container">
                                 @foreach($issue->pictures as $value)
                                 @if (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                 <!--img-->
                                 @elseif (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.mp4'])))
                                 <div class="file-wrapper">
                                    <a class="icon ni ni-clip fileName" href="/{{$value->picture_url}}" style="font-size: large" data-toggle="modal" data-target="#modal_{{$value->id}}"></a>
                                 </div>
                                 @else
                                 <div class="file-wrapper">
                                    <a class="icon ni ni-clip fileName" href="/{{$value->picture_url}}" style="font-size: large" download></a>
                                 </div>
                                 @endif
                                 @endforeach
                              </div>
                           </div>
                        </div>
                        <div class="row g-3">
                           <div class="offset-lg-2">
                              <div class="form-group mt-2">
                                 <a href="/projects/{{$issue->project_id}}/issues?status_search={{request()->get('status_search')}}&search_text={{request()->get('search_text')}}&start_date_search={{request()->get('start_date_search')}}&end_date_search={{request()->get('end_date_search')}}" class="btn btn-lg btn-light">List</a>
                              </div>
                           </div>
                           <div>
                              <div class="form-group mt-2">
                                 <a href="{{route('issues.edit', ['parentid' => $issue->project_id, 'id' => $issue->id])}}" class="btn btn-lg btn-primary">Edit</a>
                              </div>
                           </div>
                           <div>
                              <div class="form-group mt-2">
                                 @if (auth()->user()->hasRole('Admin') || Auth::user()->id == $issue->user_id)
                                    <a class="btn btn-lg btn-danger" href="{{route('issues.deleteforce', ['parentid' => $issue->project_id, 'id' => $issue->id])}}">Delete</a>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <!-- Comment -->
         <div class="nk-reply-form-title ml-5 mt-5">
            <div class="title">Comment as:</div>
            <span class="lead-text">{{Auth::user()->name}}</span>
         </div>
         <form action="{{ route('issues.comment', ['id' => $issue->id, 'parentid' => $issue->project_id]) }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
            <input type="hidden" name="issue_id" value="{{$issue->id}}">
            @method('POST')
            @csrf
            <div class="tab-pane active" id="reply-form">
               <div class="nk-reply-form-editor">
                  <div class="nk-reply-form-field" id='description_editor'>
                     <textarea class="form-control form-control-simple no-resize" id="description_cmt" name="comment"></textarea>
                  </div>
                  <div class="nk-reply-form-tools ml-3">
                     <ul class="nk-reply-form-actions">
                        <li class="">
                           <button type="submit" class="btn btn-primary" type="submit">Comment</button>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </form>
         <div class="nk-msg-head"></div>
         <div class="nk-msg-reply nk-reply" data-simplebar>
            @foreach($issue->comments as $value)
            <div class="nk-reply-item">
               <div class="nk-reply-header">
                  <div class="user-card">
                     @if(!empty($value->avatar))
                     <div class="user-avatar">
                        <img src="/{{$value->avatar}}" class="user-avatar-sm avatar-sm">
                     </div>
                     @else
                     <div class="user-avatar sm bg-primary">
                        <span class="d-flex"><em class="icon ni ni-user-fill"></em></span>
                     </div>
                     @endif
                     <div class="user-name">{{$value->username}}</div>
                  </div>
                  <div class="date-time">
                     @if((Auth::user()->id) == $value->user_id)
                     <button class="btn btn-gray p-1" onclick="openEditCmt('comment_box_{{$value->id}}');"><em class="icon ni ni-edit"></em></button>
                     <button type="button" class="btn btn-danger p-1" onclick='delcomment({{$issue->project_id}}, {{$issue->id}}, {{$value->id}})'><em class="icon ni ni-trash"></em></button>
                     @endif
                  </div>
               </div>
               <div class="nk-reply-body">
                  <div class="info_comment_box_{{$value->id}} nk-reply-entry entry">
                     {!! $value->comment !!}
                  </div>
                  <div id="edt_comment_box_{{$value->id}}" class="d-none">
                     <form action="{{ route('issues.comment.edit', ['id' => $value->id , 'parentid' => $issue->project_id]) }}" class="gy-3" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <input type="hidden" name="issue_id" value="{{$issue->id}}">
                        @method('PATCH')
                        @csrf
                        <textarea class="form-control mt-2" name="comment" id="description_cmt">{{$value->comment}}</textarea>
                        <button type="submit" class="btn btn-primary mt-1">Update</button>
                        <button type="button" class="btn btn-danger mt-1" onclick="cancel('comment_box_{{$value->id}}')">Cancel</button>
                     </form>
                  </div>
                  <div class="info_comment_box_{{$value->id}} nk-reply-from"> Time: {{$value->updated_at}} </div>
               </div>
               <hr class="mb-0">
            </div>
            @endforeach
         </div>
      </div>
   </div>
</div>

<!-- PopUp comment delete-->
<div class="modal fade" tabindex="-1" id="modalAlert2">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-body modal-body-lg text-center">
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
<!-- PopUp comment delete -->

<!-- Popup video -->
@foreach($issue->pictures as $value)
   @if (isset($value->picture_url) && is_string($value->picture_url) && (Str::endsWith($value->picture_url, ['.mp4'])))
      <div class="modal fade" tabindex="-1" id="modal_{{$value->id}}">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-body text-center">
                  <div class="nk-modal">
                     <video width="100%" height="100%" controls>
                        <source src="/{{$value->picture_url}}" type="video/mp4">
                     </video>
                  </div>
               </div>
            </div>
         </div>
      </div>
   @endif
@endforeach

<script>
   tinymce.init({
      selector: '#description_textarea',
      readonly: true,
      toolbar: false,
      menubar: false,
      branding: false,
      height: 250
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

   //Edit Comment
   function openEditCmt(name) {
      $("#edt_" + name).removeClass('d-none');
      $(".info_" + name).addClass('d-none');
   }

   function cancel(name) {
      $("#edt_" + name).addClass('d-none');
      $(".info_" + name).removeClass('d-none');
   }

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

   //Popup-delete comment
   function delcomment(projectid, issueid, cmtid) {
      $("#modalAlert2").modal();
      $("#comment_del_confirm").attr('href', `/projects/${projectid}/issues/${issueid}/view/comment/${cmtid}/delete`);
   }

   //simple lightbox
   (function() {
      var $gallery = new SimpleLightbox('#img-list a', {});
   })();
</script>
@endsection
