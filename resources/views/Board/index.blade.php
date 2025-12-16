@extends('layouts.IndexLayout',['title' => __($title ?? 'Project')])
@section('page-title', 'SR Board | Board Management')
@section('main')
@php
   $countCategory = 1;
   $boardNotice = $list->sortByDesc('updated_at');
   $boardFAQ = $list->sortByDesc('updated_at')->sortByDesc(function ($board) { return $board->board_type_id == '2';});
@endphp
<div class="nk-content px-0 px-md-3">
   <div class="container-fluid px-1">
      <div class="nk-content-inner">
         <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm ml-2">
               <div class="nk-block-between">
                  <div class="nk-block-head-content">
                     <h3 class="nk-block-title page-title">Board</h3>
                  </div>
                  <div class="nk-block-head-content @if(!request()->has('faq')) d-none @endif" id="FAQSection">
                     <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="" class="btn btn-primary mr-3 rounded-pill" data-toggle="modal" data-target="#PopupCategory">
                           <em class="icon ni ni-setting-fill"></em><span>Category</span>
                        </a>
                        <a href="{{route('admin.faq.create')}}" class="btn btn-primary rounded-pill">
                           <em class="icon ni ni-plus"></em><span>Add FAQ</span>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            <!-- ----------- -->
            <div class="nk-block">
               <div class="card card-stretch">
                  <div class="card-inner-group">
                     <div class="card-inner">
                        <ul class="nav nav-tabs mt-n3">
                           <li class="nav-item">
                              <a class="nav-link @if(!request()->has('faq')) active @endif" data-toggle="tab" href="#tabItem1" onclick="ChangeTab()">
                                 <em class="icon ni ni-bell"></em><span>Notice</span>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link @if(request()->has('faq')) active @endif" data-toggle="tab" href="#tabItem2" onclick="ChangeTab()">
                                 <em class="icon ni ni-question"></em><span>FAQ</span>
                              </a>
                           </li>
                           <div class="align-items-center ml-auto my-1">
                              <form action="{{route('admin.board.index')}}" method="GET" class="d-flex border round px-2">
                                 <input type="text" name="search_text" class="form-control border-transparent form-focus-none w-auto"
                                    placeholder="Search..." value="{{request()->get('search_text')}}" style="margin-left:-12px">
                                 <button type="submit" class="btn btn-search">
                                    <em class="icon ni ni-search" style="font-size: 1.8em; color: aliceblue;"></em>
                                 </button>
                              </form>
                           </div>
                        </ul>
                        <div class="tab-content">
                           <div class="nk-header-search pb-3 @if(request()->has('faq')) d-none @endif" id="NoticeSection">
                              <div class="nk-block-head-content">
                                 <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="{{route('admin.notice.create')}}" class="btn btn-primary rounded-pill">
                                       <em class="icon ni ni-plus"></em><span>Add</span>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <div class="nk-header-search mb-2 FAQSection @if(!request()->has('faq')) d-none @endif" id="FAQCategory">
                              <div class="nk-block-head-content">
                                 <div class="toggle-wrap nk-block-tools-toggle d-flex flex-wrap align-center">
                                    <b>Category:</b>
                                    <div class="cate ml-2 mt-1" data-class="all">
                                       <a class="btn btn-primary text-light filter-faq">All</a>
                                    </div>
                                    @foreach($cate as $bc)
                                       @if($bc->category != 'itnononnone')
                                          <div class="cate ml-2 mt-1" data-class="{{$bc->category}}">
                                             <a class="btn btn-light text-dark filter-faq" id="category-list_{{$bc->id}}">{{$bc->category}}</a>
                                          </div>                                          
                                       @endif
                                    @endforeach
                                    <div class="cate mt-1" id="list-cate"></div>
                                </div>
                              </div>
                           </div>
                           <div class="tab-pane @if(!request()->has('faq')) active @endif" id="tabItem1">
                              <div class="overflow-auto">
                                 <table class="nk-tb-list is-separate nk-tb-ulist">
                                    <thead>
                                       <tr class="nk-tb-item nk-tb-head">
                                          <th class="nk-tb-col" style="min-width: 200px;"><span class="sub-textp">Title</span></th>
                                          <th class="nk-tb-col"><span class="sub-text">Writer</span></th>
                                          <th class="nk-tb-col"><span class="sub-text">Date</span></th>
                                          <th class="nk-tb-col nk-tb-col-tools"></th>
                                       </tr>
                                    </thead>
                                    <tbody>                                       
                                       @foreach($boardNotice as $board)
                                          @if($board->board_category_id == 0)
                                             <tr class="nk-tb-item">
                                                <td class="nk-tb-col">
                                                   <a href="{{route('notice.view',['id' => $board->id])}}" class="project-title">
                                                      <div class="project-info"><h6 class="title">{{$board->title}}</h6></div>
                                                   </a>
                                                </td>
                                                <td class="nk-tb-col">
                                                   <span>{{$board->name}}</span>
                                                </td>
                                                <td class="nk-tb-col">
                                                   <div class="project-list-progress d-flex">
                                                      <span>{{ \Carbon\Carbon::parse($board->created_at)->format('m/d/Y h:i A') }}</span>
                                                   </div>
                                                </td>
                                                <td class="nk-tb-col nk-tb-col-tools">
                                                   <ul class="nk-tb-actions gx-1">
                                                      <li>
                                                         <div class="drodown">
                                                            <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                               <ul class="link-list-opt no-bdr">
                                                                  <li><a href="{{route('admin.notice.edit', ['id' => $board->id])}}"><em class="icon ni ni-edit"></em><span>Modify</span></a></li>
                                                                  <li><a type="button" data-toggle="modal" data-target="#PopupDelete_{{$board->id}}"><em class="icon ni ni-trash"></em>Delete</a></li>
                                                               </ul>
                                                            </div>
                                                         </div>
                                                      </li>
                                                   </ul>
                                                </td>
                                             </tr>
                                          @endif
                                       @endforeach
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                           <div class="tab-pane @if(request()->has('faq')) active @endif" id="tabItem2">
                              <div class="overflow-auto">
                                 <table class="nk-tb-list is-separate nk-tb-ulist">
                                    <thead><tr class="nk-tb-item nk-tb-head"></tr></thead>
                                    <tbody>
                                    @foreach($boardFAQ as $board)
                                       @if($board->board_category_id != 0)
                                          <tr class="nk-tb-item" id="FAQ-block" data-class="{{$board->category}}">
                                             <td class="nk-tb-col">
                                                <div id="faqs" class="accordion">
                                                   <div class="d-flex board-item">
                                                      <a data-toggle="modal" data-id="{{$board->id}}" data-target="#PopupFAQ" class="project-title title">
                                                         <b class="mr-4">Q</b>
                                                         <b class="text-blue mr-4 w-min-70px">{{$board->category}}</b>
                                                         <h6 class="title">{{$board->title}}</h6>
                                                      </a>
                                                      <a href="#" class="accordion-head collapsed ml-auto" data-toggle="collapse" data-target="#faq-q{{$board->id}}">
                                                         <span class="accordion-icon"></span>
                                                      </a>
                                                   </div>
                                                   <div class="accordion-body collapse" id="faq-q{{$board->id}}" data-parent="#faqs"><hr>
                                                      <div class="accordion-inner d-flex">
                                                         <b class="text-dark mr-4">A</b>{!! $board->board_content !!}
                                                      </div>
                                                   </div>
                                                </div>
                                             </td>
                                          </tr>
                                       @endif
                                    @endforeach
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- PopUp FAQ -->
<div class="modal fade" tabindex="-1" id="PopupFAQ">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content px-4">
         <div class="modal-header">
            <h5 class="modal-title">FAQ</h5>
         </div>
         <div class="modal-body bg-light d-flex">
            <div class="d-flex mr-auto">
               <h6 class="text-dark mr-2">Q</h6>
               <h6 class="title" id="board_title"></h6>
            </div>
            <h6 class="text-dark mr-4" id="created_at"></h6>
            <h6 class="text-dark" id="user_name"></h6>
         </div>
         <div class="modal-body">
            <p id="board_content"></p>
         </div>
         <div class="modal-body ml-auto">
            <a class="btn btn-md btn-light py-1 mr-1" data-dismiss="modal">List</a>
            <a class="btn btn-md btn-primary py-1 mr-1 text-white edit-button" id="edit-btn">Edit</a>
            <a class="btn btn-md btn-danger py-1 text-white" id="del_FAQ">Delete</a>
         </div>
      </div>
   </div>
</div>
<!-- PopUp FAQ -->

<!-- PopUp delete -->
@foreach($boardNotice as $board)
   @if($board->board_category_id == 0)
      <div class="modal fade" tabindex="-1" id="PopupDelete_{{$board->id}}">
         <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
               <div class="modal-body text-center">
                  <div class="nk-modal">
                     <h5 class="nk-modal-title">Are you sure to Delete?</h5>
                     <div class="nk-modal-action">
                        <a href="{{route('admin.board.delete', ['id' => $board->id])}}" class="btn btn-sm btn-primary mr-3">Yes</a>
                        <a class="btn btn-sm btn-secondary text-white" data-dismiss="modal">Cancel</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   @endif
@endforeach
<!-- PopUp delete -->

<!-- PopUp Category -->
<div class="modal fade" tabindex="-1" id="PopupCategory">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h6 class="modal-title">Category management</h6>
            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
               <em class="icon ni ni-cross"></em>
            </a>
         </div>
         <div class="modal-body">
            <form action="#" class="form-validate is-alter" id="add-category-form">
               <div class="form-group">
                  <div class="form-control-wrap d-flex">
                     <input type="text" class="form-control round-xl ml-3" id="category-input">
                     <button type="submit"  id="ajax-button" class="btn btn-sm btn-primary round-xl ml-2"><em class="icon ni ni-plus-sm"></em>Add</button>
                  </div>
               </div>
            </form>
            <div class="categorie-item py-2">
               @foreach($cate as $bc)
                  @if($bc->category != 'itnononnone')
                  <div class="form-group" id="categroup_id_{{$bc->id}}">
                     <div class="form-control-wrap d-flex">
                        <b class="align-center" style="min-width:10px;" id="countCate">{{ $countCategory }}</b>
                        <input type="text" class="form-control round-xl ml-1 mr-1" id="category_{{$bc->id}}"  name="category" value="{{$bc->category}}" readonly>
                        <a onclick="cateEdit({{$bc->id}})" class="btn btn-lg p-1" id="edit-cate_{{$bc->id}}"><em class="icon ni ni-edit-alt"></em></a>
                        <a onclick="EditCate({{$bc->id}})" class="btn btn-sm btn-primary round-xl text-white ml-1 px-4 d-none" id="save-cate_{{$bc->id}}"><em class="icon ni ni-save-fill"></em></a>
                        <a url="{{route('admin.category.delete',['id' => $bc->id])}}" class="btn btn-lg text-danger p-1 del-cate" id="delete-cate_{{$bc->id}}"><em class="icon ni ni-trash"></em></a>
                     </div>
                  </div>
                  @php $countCategory++; @endphp
                  @endif
               @endforeach
            </div>
         </div>
      </div>
   </div>
</div>
<!-- PopUp Category -->
<script src="asset{{('../../../dashlite/assets/js/dateformat.js')}}"></script>
<script>
   function deleteNotice(id) {
      $("#PopupDelete").modal();
      $("#comment_del_confirm").attr('href', `/admin/boards/${id}/delete/`);
   }

   document.addEventListener("DOMContentLoaded", function() {
      const NoticeTab = document.querySelector('[data-toggle="tab"][href="#tabItem1"]');
      const NoticeSection = document.getElementById("NoticeSection");

      const FAQTab = document.querySelector('[data-toggle="tab"][href="#tabItem2"]');
      const FAQSection = document.getElementById("FAQSection");
      const FAQCategory = document.getElementById("FAQCategory");

      FAQTab.addEventListener("click", function() {
         NoticeSection.classList.add("d-none");
         FAQSection.classList.remove("d-none");
         FAQCategory.classList.remove("d-none");
      });

      NoticeTab.addEventListener("click", function() {
         NoticeSection.classList.remove("d-none");
         FAQSection.classList.add("d-none");
         FAQCategory.classList.add("d-none");
      });
   });

   //Popup FAQ
   var boards = {!! $list->toJson() !!};
   $(document).ready(function(){
      $(".title").click(function(){
         var faqId = $(this).data("id");
         var faq = boards.find(board => board.id === faqId);

         $("#board_title").text(faq.title);
         $("#user_name").text(faq.name);
         $("#board_content").html(faq.board_content);

         var updatedAtString = faq.updated_at;
         var formattedDate = moment(updatedAtString, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD");

         $("#created_at").text(formattedDate);
         $('#edit-btn').attr('data-id', faq.id);
         $("#del_FAQ").attr('href', `/admin/boards/${faqId}/delete/`);
      })
   });

   $(document).ready(function() {
      $('.edit-button').click(function(e) {
         e.preventDefault();
         var boardId = $(this).data('id');
         var editUrl = "{{ route('admin.faq.edit', ['id' => ':boardId']) }}";
         editUrl = editUrl.replace(':boardId', boardId);
         window.location.href = editUrl;
      });
   });

   //Category
   $(document).ready(function () {
      $('#ajax-button').click(function (e) {
         var key = 0;
         e.preventDefault();
         var category = $('#category-input').val();
         var url = 'admin/faq/categories/create'
         $.ajax({
            type: 'POST',
            url: url ,
            data: {
               category: category,
               _token: '{{ csrf_token() }}'
            },
            success: function (response) {
               var nextIndex = document.querySelectorAll('.form-group').length;
               var html = `<div class="form-group" id="categroup_id_${response.result.id}">
                              <div class="form-control-wrap d-flex">
                                 <b class="align-center" style="min-width:10px;">${nextIndex}</b>
                                 <input type="text" class="form-control round-xl ml-1 mr-1" id="category_${response.result.id}"  name="category" value="${response.request.category}" readonly>
                                 <a onclick="cateEdit(${response.result.id})" class="btn btn-lg p-1" id="edit-cate_${response.result.id}"><em class="icon ni ni-edit-alt"></em></a>
                                 <a onclick="EditCate(${response.result.id})" class="btn btn-sm btn-primary round-xl text-white ml-1 px-4 d-none" id="save-cate_${response.result.id}"><em class="icon ni ni-save-fill"></em></a>
                                 <button onclick="DeleteCate(${response.result.id})" class="btn btn-lg text-danger p-1" id="delete-cate_${response.result.id}"><em class="icon ni ni-trash"></em></button>
                              </div>
                           </div>`;
               nextIndex++;
               var category = `<a class="btn btn-light text-dark filter-faq ml-2" id="category-list_${response.result.id}">${response.request.category}</a>`
               $('#list-cate').append(category);
               $('.categorie-item').append(html);
               $('#category-input').val('');
            },
            error: function (xhr, status, error) {
               console.log('Form submitted failed');
            }
         });
      });
   });

   function cateEdit(id){
      $('#category_'+id).removeAttr("readonly")
      $('#edit-cate_'+id).addClass("d-none")
      $('#save-cate_'+id).removeClass("d-none")
      $('#delete-cate_'+id).addClass('d-none')
   }

   function EditCate(id) {
      var category = $('#category_' + id).val();
      var url = 'admin/faq/categories/' + id;
      $.ajax({
         type: 'POST',
         url: url,
         data: {
            category: category,
            _token: '{{ csrf_token() }}'
         },
         success: function (response) {
            $('#category_'+id).attr("readonly", true)
            $('#edit-cate_'+id).removeClass("d-none")
            $('#save-cate_'+id).addClass("d-none")
            $('#delete-cate_'+id).removeClass('d-none')
            $('#category-list_'+id).html(category)
         },
         error: function (error) {
            console.error('cannot edit!!');
         }
      });
   }

   $(".del-cate").click(function() {
      Swal.fire({
         title: "Are you sure to delete?",
         text: "You can be lost data after this action",
         icon: "error",
         showCancelButton: true,
         confirmButtonText: "Yes",
         cancelButtonText: "Cancel",
      }).then((result) => {
         if (result.isConfirmed) {
            location.href = $(this).attr("url");
         }
      })
   });

   window.onload = function () {
      const listItems = document.querySelectorAll(".cate");
      const galleryItem = document.querySelectorAll("#FAQ-block");
      const filterFAQ = document.querySelectorAll(".filter-faq");

      function toggleActiveClass(t) {
         filterFAQ.forEach((t) => {
            t.classList.remove("active");
         }),
            t.classList.add("active");
      }

      function toggleProjects(t) {
         if ("all" === t)
            galleryItem.forEach(item => { item.classList.remove("d-none"); });
         else{
            galleryItem.forEach(item => {
               item.dataset.class === t
                  ? (item.classList.remove("d-none"))
                  : (item.classList.add("d-none"));
            });
         }
      }

      for (let t = 0; t < listItems.length; t++)
         listItems[t].addEventListener("click", function () {
            toggleActiveClass(filterFAQ[t]),
               toggleProjects(listItems[t].dataset.class);
         });
   };

   function ChangeTab() {
      const currentUrl = window.location.href;
      if (currentUrl.indexOf('?faq') === -1) {
         const newURL = currentUrl + '?faq';
         window.history.pushState({}, '', newURL);
      }else if (currentUrl.indexOf('?faq') !== -1) {
         const newURL = currentUrl.replace('?faq', '');
         window.history.pushState({}, '', newURL);
      }
   }
</script>
@endsection
