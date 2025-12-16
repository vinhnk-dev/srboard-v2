@php
   $hasNotice = false;
   $hasFAQ = false;
   $boardNotice = $boards->sortByDesc('updated_at')->sortByDesc(function ($board) { return $board->board_type_id == '1';})->take(3);
   $boardFAQ = $boards->sortByDesc('updated_at')->sortByDesc(function ($board) { return $board->board_type_id == '2';})->take(3);
@endphp
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-md-6">
            <div class="card card-bordered pricing">
                <div class="pricing-head">
                    <div class="pricing-title">
                        <h5 class="card-title title">Notice</h5>
                    </div>
                </div>            
                <div class="pricing-body overflow-auto h-max-175px">
                    <ul class="pricing-features">
                        @foreach ($boardNotice as $board)
                            @if ($board->board_type_id == "1")
                            @php
                                $hasNotice = true;
                            @endphp
                            <li>
                                <a href="{{route('notice.view',['id' => $board->id])}}"><span class="h6 my-1 text-dark">{{$board->title}}</span></a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @if (!$hasNotice)
                    <div class="pricing-body d-flex h-100 h-min-100px align-items-center justify-content-center">
                        <div>There are no registered posts.</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-bordered pricing">
            <div class="pricing-head">
                <div class="pricing-title">
                    <h5 class="card-title title">FAQ</h5>
                </div>
            </div>
            <div class="pricing-body overflow-auto h-max-175px">
                <ul class="pricing-features">
                    @foreach ($boardFAQ as $board)
                        @if ($board->board_type_id == "2")
                        @php
                            $hasFAQ = true;
                        @endphp
                        <li>
                            <a><span class="h6 my-1 text-dark">{{ $board->title }}</span></a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            @if (!$hasFAQ)
                <div class="pricing-body d-flex h-100 h-min-100px align-items-center justify-content-center">
                    <div>There are no registered posts.</div>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>