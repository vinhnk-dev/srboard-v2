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
<form action="" method="GET" id="main-form">
   <div class="nk-block">
      <div class="nk-header-search mb-2">
         <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
               @if($display_tools)
                  {!!$tableview_tool!!}
               @endif
            </div>
         </div>
         <div class="d-flex flex-wrap ml-auto mr-2">
            @if($display_filters)
               {!!$tableview_filter!!}
            @endif
         </div>
      </div>

      <div class="overflow-auto">
         <table class="nk-tb-list is-separate nk-tb-ulist">
            <thead>
               <tr class="nk-tb-item nk-tb-head">
                  @foreach($tableHead as $k=>$v)
                  <th class="nk-tb-col"><span class="sub-text text-nowrap">{{$v}}</span></th>
                  @endforeach
                  @if($display_action)
                  <th class="nk-tb-col nk-tb-col-tools"></th>
                  @endif
               </tr>
            </thead>
            <tbody>
               @foreach($list as $modal)
               <tr class="nk-tb-item ">
                  @foreach($tableHead as $k=>$v)
                  <td class="nk-tb-col"><span class="sub-text text-nowrap">{!!$modal->getCellValue($k)!!}</span></td>
                  @endforeach
                  @if($display_action)
                  <td class="nk-tb-col nk-tb-col-tools">
                     <ul class="nk-tb-actions">
                        {!!$modal->row_actions!!}
                     </ul>
                  </td>
                  @endif
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
      @if($display_paging)
      <div class="card">
         <div class="card-inner">
            <div class="nk-block-between-md g-3">
               {!!$tableview_paging!!}
               <div class="d-flex justify-content-center gx-t3">
                  <p class="pt-1">PER PAGE:</p>
                  <div>
                     <select class="btn btn-outline-light ml-2" id="perpage" name="perpage">
                        <option value="15" @if ($perpage==15) selected @endif>15</option>
                        <option value="25" @if ($perpage==25) selected @endif>25</option>
                        <option value="50" @if ($perpage==50) selected @endif>50</option>
                     </select>
                  </div>
               </div>
            </div>
         </div>
      </div>
      @endif
</form>

<script>
   $(document).ready(function() {
      $(".bconvert").each(function() {
         var rgb = $(this).css('color').match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
         $(this).css("background-color", "rgba(" + rgb[1] + "," + rgb[2] + "," + rgb[3] + ",0.2)");
      })
   });

   $(".show-images").mouseover(function() {
      if (window.shower) window.shower.destroy();
      window.shower = new SimpleLightbox('#' + $(this).attr("id") + ' a', {});
   });

   $(".del-row").click(function() {
      Swal.fire({
         title: "Are you sure to delete?",
         showCancelButton: true,
         confirmButtonText: "Move to Trash",
         cancelButtonText: "Cancel",
         footer: '<a href="' + $(this).attr("url") + 'force" class="btn btn-danger">Delete it and relations!</a>'
      }).then((result) => {
         if (result.isConfirmed) {
            location.href = $(this).attr("url");
         }
      })
   });

   $(".forcedel-row").click(function() {
      Swal.fire({
         title: "Are you sure to delete?",
         icon: 'error',
         showCancelButton: true,
         confirmButtonText: "Delete it and relations!",
         cancelButtonText: "Cancel",
      }).then((result) => {
         if (result.isConfirmed) {
            location.href = $(this).attr("url");
         }
      })
   });

   $(".btn-export").click(function() {
      var link = document.createElement('a');
      document.body.appendChild(link);
      link.href = $(this).attr("url");
      link.click();
   });

   $("#perpage").change(function() {
      $("#main-form").submit();
   });
</script>
