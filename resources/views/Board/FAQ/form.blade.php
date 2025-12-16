@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Edit FAQ' )
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
                        <div class="card p-3">
                            <div class="card-inner">
                                <h4 class="title nk-block-title mb-4">EDIT FAQ</h4>
                                <form action="{{route('admin.faq.store')}}" class="gy-3" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$board->id}}">
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    <input type="hidden" name="board_type_id" value="{{$board_type_id}}">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Title</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" name="title" placeholder="*Please enter project's name" value="{{$board->title}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">Category</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <select class="form-select form-select-sm" name="board_category_id">
                                                @foreach ($board_categories as $bc)
                                                    @if($bc->category != 'itnononnone')
                                                        <option value="{{ $bc->id }}" {{ $bc->id == $board->board_category_id ? 'selected' : '' }}>{{ $bc->category }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-start">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Content</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group" id='description_editor'>
                                                <textarea class="form-control" rows="3" name="board_content" placeholder="*Please enter description">{{$board->board_content}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="offset-lg-2">
                                            <div class="form-group mt-2">
                                                <a onclick="delcomment()" class="btn btn-lg btn-light">Cancel</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-lg btn-primary">Submit</button>
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

<!-- PopUp-cancel button-->
<div class="modal fade" tabindex="-1" id="modalAlert2">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-body text-center">
            <div class="nk-modal">
               <h5 class="nk-modal-title">Are you sure to Cancel?</h5>
               <div class="nk-modal-action">
                  <a id="comment_del_confirm" href="{{route('admin.board.index')}}?faq" class="btn btn-sm btn-primary mr-3">Yes</a>
                  <button class="btn btn-sm btn-secondary text-white" data-dismiss="modal">Cancel</button>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- PopUp-cancel button -->

<script>
    function delcomment() {
        $("#modalAlert2").modal();
    }

    tinymce.init({
        selector: 'textarea',
        toolbar_mode: 'scrolling',
        menubar: false,
        branding: false,
        height: 400,
        toolbar: "fontfamily fontsize forecolor bold italic align indent outdent checklist bullist numlist link image codesample code",
        plugins: "lists link image codesample code",
        font_formats: "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",
        content_style: "@import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap'); body { font-family: Verdana; font-size: 10pt;}",
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt"
    });
</script>
@endsection
