<div class="nk-block">
   <a onclick="search_by_status(0)">
      <div class="border-left count-issue-status custom-cursor border-secondary @if(request()->get('status_search')=='' || request()->get('status_search')==0) status-selected @endif">
         <h4 class="title issue-text text-secondary">TOTAL</h4>
         <div class="issue_number" id="total_issue_number">{{$totalRow}}</div>
      </div>
   </a>
   @foreach($categories as $st)
   <a onclick="search_by_eachstatus({{$st->status_id}})" class="custom_checkbox_stt">
      <div class="issue-block count-issue-status custom-cursor
      @if(in_array($st->status_id,explode(',',request()->get('status_search')))) status-selected
      @endif" style="border-left:4px solid {{$st->color}}; border-color: {{$st->color}}">
         <h4 class="title issue-text text-status" style="color:{{$st->color}}">{{$st->status_name}}</h4>
         <div class="issue_number">{{$st->issuesNum}}</div>
      </div>
   </a>
   <div class="custom-control custom-control-sm custom-checkbox">
      <input type="checkbox" class="custom-control-input" id="{{$st->status_id}}" @if(in_array($st->status_id,explode(',',request()->get('status_search')))) checked @endif>
      <label class="custom-control-label check-stt" for="{{$st->status_id}}" onclick="search_by_status({{$st->status_id}})"></label>
   </div>
   @endforeach
</div>

<form id="search_by_status" action="{{$form_action}}" method="GET">
   <input id="status_search_input" type="hidden" name="status_search" value="{{request()->status_search}}">
   @if(request()->search != '') <input type="hidden" name="search" value="{{request()->search}}"> @endif
   @if(request()->start_date_search != '') <input type="hidden" name="start_date_search" value="{{request()->start_date_search}}">@endif
   @if(request()->end_date_search != '') <input type="hidden" name="end_date_search" value="{{request()->end_date_search}}">@endif
   @if(request()->perpage != '') <input type="hidden" name="perpage" value="{{request()->perpage}}">@endif
</form>

<script>
   function search_by_status(status) {
      var id_status = document.getElementById('status_search_input').value;
      if (id_status == '') {
         id_status = [];
      } else {
         id_status = id_status.split(',');
      }

      if (status == 0) {
         document.getElementById('status_search_input').value = '';
      } else if (id_status.includes(String(status))) {
         id_status = id_status.filter((val) => val != String(status))
         document.getElementById('status_search_input').value = id_status.join(',')
      } else {
         if (id_status.length < 6) {
            id_status.push(status);
            document.getElementById('status_search_input').value = id_status.join(',');
         } else {
            alert("You can only use up to 6 filters at a time.");
         }
      }

      document.getElementById('search_by_status').submit();
   }

   function search_by_eachstatus(status) {
      document.getElementById('status_search_input').value = status;
      document.getElementById('search_by_status').submit();
   }

   $(document).ready(function(){
      var total = 0;

   });
</script>
