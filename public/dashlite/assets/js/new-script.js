//script of Issue/Notice Form (Image/File)
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

//tinyMCE editor
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

tinymce.init({
    selector: '#description',
    readonly: true,
    toolbar: false,
    menubar: false,
    branding: false,
    height: 400
});

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
    $("#modalDelete").modal();
    $("#comment_del_confirm").attr('href', `/notice/${boardId}/view/comment/${cmtid}/delete`);
}