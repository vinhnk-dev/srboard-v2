@extends('layouts.InputLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Notice' )
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
                                <h4 class="title nk-block-title mb-4">FORM NOTICE</h4>
                                <form action="{{route('admin.notice.store',['id' => $board->id])}}" class="gy-3" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$board->id}}">
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
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
                                    <div class="row g-3 align-start">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label"><span class="text-danger">*</span>Description</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group" id='description_editor'>
                                                <textarea class="form-control" rows="3" name="board_content" placeholder="*Please enter description">{{$board->board_content}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-start">
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label">File</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="btn btn-secondary" for="fileInp">Select file</label>
                                            <input type="file" class="form-control-file img-Inp" id="fileInp" name="file_url[]" multiple>
                                            <div class="image-container" id="img-list">
                                                @foreach($board_file as $value)
                                                    @if (isset($value->file_url) && is_string($value->file_url) && (Str::endsWith($value->file_url, ['.jpg', '.jpeg', '.png', '.gif'])))
                                                        <div class="image-wrapper">
                                                            <input type="hidden" name="file_url[]" value="{{$value->file_url}}">
                                                            <img src="/{{$value->file_url}}" class="example-image img-thumbnail">
                                                            <button class="img-delete-btn">x</button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div class="file-container">
                                                @foreach($board_file as $value)
                                                    @if (isset($value->file_url) && is_string($value->file_url) && (Str::endsWith(($value->file_url), ['.jpg', '.jpeg', '.png', '.gif'])))
                                                    @else
                                                        <div class="file-block">
                                                            <input type="hidden" name="pic_url[]" value="{{$value->file_url}}">
                                                            <span class="file-delete-btn px-1">x</span>
                                                            <a class="fileName" href="/{{$value->file_url}}" style="font-size: large"></a>
                                                        </div>
                                                    @endif
                                                @endforeach
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
                  <a id="comment_del_confirm" href="{{route('admin.board.index')}}" class="btn btn-sm btn-primary mr-3">Yes</a>
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

    // Image/File
    const fileInp = document.getElementById('fileInp');
    const imageContainer = document.querySelector('.image-container');
    const fileContainer = document.querySelector('.file-container');
    imageContainer.addEventListener('click', imageDelete);
    fileContainer.addEventListener('click', fileDelete);
    const dt = new DataTransfer();

    fileInp.onchange = evt => {
        const files = fileInp.files;
        fileInp_files = files;
        for(let i = 0; i < files.length; i++){
            dt.items.add(files[i]);

            var deleteButton = document.createElement('button');
            deleteButton.innerText = 'x';
            deleteButton.classList.add('img-delete-btn');
            deleteButton.setAttribute('idx', dt.items.length-1);

            var btnDeleteFile = document.createElement('span');
            btnDeleteFile.setAttribute('class', 'file-delete-btn px-1');
            btnDeleteFile.textContent = 'x';
            btnDeleteFile.setAttribute('idx', dt.items.length-1);

            if (isImageFile(files[i].type)) {
                var imageWrapper = document.createElement('div');
                imageWrapper.classList.add('image-wrapper');

                var image = document.createElement('img');
                image.src = URL.createObjectURL(files[i]);
                image.setAttribute('class', 'example-image img-thumbnail');

                imageWrapper.appendChild(image);
                imageWrapper.appendChild(deleteButton);
                imageContainer.appendChild(imageWrapper);

            } else {
                var fileWrapper = document.createElement('div');
                fileWrapper.classList.add('file-block');

                var fileName = document.createElement('a');
                fileName.href = URL.createObjectURL(files[i]);
                fileName.setAttribute('style', 'font-size: large');
                fileName.textContent = files[i].name;
                fileName.target = '_blank';

                fileWrapper.appendChild(btnDeleteFile);
                fileWrapper.appendChild(fileName);
                fileContainer.appendChild(fileWrapper);
            }
        }
        fileInp.files = dt.files;
    }

    function isImageFile(fileType) {
        return fileType.startsWith('image/');
    }

    function imageDelete(event) {
        if (event.target.classList.contains('img-delete-btn')) {
            var idx = event.target.getAttribute("idx");
            dt.items.remove(idx);
            fileInp.files = dt.files;
            const imageWrapper = event.target.parentElement;
            imageWrapper.remove();     
        }
    }

    function fileDelete(event) {
        if (event.target.classList.contains('file-delete-btn')) {
            var idx = event.target.getAttribute("idx");
            dt.items.remove(idx);
            fileInp.files = dt.files;
            const fileWrapper = event.target.parentElement;
            fileWrapper.remove();     
        }
    }
</script>
@endsection
