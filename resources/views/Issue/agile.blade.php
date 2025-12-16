@extends('layouts.IndexLayout', ['title' => __($title ?? 'Issues')])
@section('page-title', 'SR Board | Issues List')
@section('main')
<div class="nk-content px-0 px-md-3 pb-0 " id="agile-content">
    <div class="container-fluid px-1">
        <div class="nk-content-inner">
            <div class="nk-content-body" id="top-page-tool">
                <div class="nk-block-head nk-block-head-sm ml-2 pb-0">
                    <div class="nk-block-head-content d-flex justify-content-between">
                        <h3 class="nk-block-title page-title">Issues</h3>
                        <div class="d-flex flex-wrap ml-auto ml-2">
                            {!!$page_left_tools!!}
                        </div>
                    </div>
                </div>
                <!--------------->
                <div class="d-flex flex-wrap justify-content-between align-items-baseline mt-2">
                    <div class="d-flex flex-wrap align-items-center mr-1 mb-1">
                        <div class="nk-block">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="mt-1">
                                    <a href="#" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Add Issue</span>
                                    </a>
                                </div>
                                <div class="ml-3 mt-1">
                                    <a href="#" class="btn btn-excel">
                                        <img src="../../../dashlite/images/excel.png" class="mr-2 logo-excel">
                                        <span>Excel</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="main-form">
                        <form id="search_form" method="GET" class="agile-search-submit">
                            <div class="d-flex flex-row-reverse ml-md-auto mb-1">
                                <input type="hidden" name="search_text" value="{{request()->get('search_text')}}">
                                <div class="d-sm-inline ml-3">
                                    <input onchange="issueFilterByCreatedDate()" class="border input-image2 ip-period date-picker" placeholder="Start date" type="text" name="start_date_search" value="{{request()->get('start_date_search')}}">
                                    <span class="mx-1 tt-per-set">~</span>
                                    <input onchange="issueFilterByCreatedDate()" class="border input-image2 ip-period date-picker" placeholder="End date" type="text" name="end_date_search" value="{{request()->get('end_date_search')}}">
                                </div>
                            </div>
                            <div class="card-tools mr-n1 ml-3 d-flex">
                                <div class="d-flex border round px-2">
                                    <input type="text" id="search_text_input" name="search_text" style="margin-left:-12px" value="{{ request()->get('search_text') }}" class="form-control border-transparent form-focus-none w-auto" placeholder="Search...">
                                    <input type="hidden" id="user_id_search" name="user_assignee" value="{{ request()->get('user_assignee')}}">
                                    <button type="submit" class="btn btn-search rounded-0">
                                        <em class="icon ni ni-search" style="font-size: 1.8em; color: aliceblue;"></em>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--------------->

                <!--------------->
                <div class="d-flex flex-wrap align-items-center">
                    <span class="agile-assignee mr-2">Assignees:</span>
                    <div class="mr-2 mt-1">
                        <a href="{{ route('issues.index', ['parentid' => $parentid]) }}">
                            <div class="btn btn-light">
                                <span>ALL </span> &nbsp(<span id="total-issue-counter">{{count($issues['list'])}}</span>)
                            </div>
                        </a>
                    </div>
                    @foreach ($userGroupAssign as $uga)
                    <div class="mr-2 mt-1">
                        <a onclick="search_by_user('{{ $uga->name }}','{{ $uga->id }}')">
                            <div class="btn btn-light">
                                <span>#</span>
                                <span>{{ $uga->name }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <!--------------->
            </div>
            <div class="nk-content-body">
                <!-- Main Content -->
                <div class="d-flex agile-board mt-1" style="gap:30px;" id="agile-body">
                    @foreach ($categories as $st)
                    <?php $count = 0 ?>
                    <div id="status_container_{{$st->status_id}}" data-statusid="{{ $st->status_id }}" class="border rounded border-light agile-tb droppable-1 status_id({{ $st->id }})">
                        <div class="border-bottom p-2 agile-stt">
                            <span>{{ $st->status_name }}</span>
                            <span id="status_{{ $st->id }}" class="agile-stt-count">0</span>
                        </div>
                        <div class="connected-sortable agile-body-container">
                            @foreach ($issues['list'] as $issue)
                            @if ($issue->status == $st->status_id)
                            <?php $count++ ?>
                            <div data-issueid="{{$issue->id}}" data-issueurl="{{ route('issues.changestt', ['parentid' => $issue->project_id, 'id'=>$issue->id]) }}" id="issue_box{{$issue->id}}" class="border {!!$issue->getCellValue('due_date_agile_border')!!} rounded agile-box draggable-item issue_{{$issue->id}}">
                                <a href="{{ route('issues.view', ['parentid' => $issue->project_id, 'id'=>$issue->id]) }}">
                                    <div class="text-primary p-2">
                                        <div class="d-flex align-center" id="issue-card-header-{{$issue->id}}">
                                            <b class="text-blue" style="font-size: medium">{{$issue->project_code}}-{{$issue->id}}</b>
                                            {!!$issue->getCellValue('due_date_agile')!!}
                                        </div>
                                        <h5 class="card-title agile-box-tt text-break">{{$issue->title}}</h5>
                                        <p class="card-text text-dark text-break"><b>Reporter: </b>{{$issue->repo()->getReporter($issue->id, true)}}</p>
                                    </div>
                                </a>
                            </div>
                            @endif
                            @endforeach
                            <script>
                                $("#status_{{ $st->id }}").html("{{$count}}")
                            </script>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/dashlite/assets/js/jquery.ui.sortable-animation.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#agile-body").css('height', 'calc(100vh - 10px - ' + $('#agile-content').css('margin-top') + ' - ' + $('#agile-content').css('padding-top') + ' - ' + $('#top-page-tool').css('height') + ')')
    })

    function search_by_user(user, id) {
        $("#search_text_input").val(user);
        $("#user_id_search").val(id)
        var form = $("#search_form");
        form.submit();
    }
    init();

    function init() {
        $(".connected-sortable").sortable({
            animation: 350,
            connectWith: ".connected-sortable",
            stack: '.connected-sortable ul',
            stop: function(event, ui) {
                var actionUrl = ui.item.data("issueurl")
                var issueid = ui.item.data("issueid")
                var statusid = ui.item.parent().parent().data("statusid");
                var newPositions = [];
                $("#status_container_" + statusid + " .draggable-item").each(function() {
                    newPositions.push($(this).data("issueid"));
                });
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to update the issue's status.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, update it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: actionUrl,
                            type: 'POST',
                            data: {
                                newStatus: statusid,
                                position: newPositions,
                                _token: '{{ csrf_token() }}'
                            },
                            success: (response) => {
                                var res = $.parseJSON(response);
                                $("#issue-card-header-"+ issueid + ' .alert-due').removeClass('alert-' + res.oldDeadline);
                                $("#issue_box"+issueid).removeClass('border-' + res.oldDeadline);
                                $("#issue-card-header-"+ issueid + ' .alert-due').addClass('alert-' + res.deadline);
                                $("#issue_box"+issueid).addClass('border-' + res.deadline);
                                flashy('Updated successfully !', {timeout: 3000, type : 'flashy__success'});
                            },
                            error: function(response) {
                                flashy('Update failed !', {timeout: 3000, type : 'flashy__danger'});
                            }
                        });
                    } else {
                        location.reload();
                    }
                });
            }
        }).disableSelection();
    }
</script>
@endsection