<!-- SearchForm -->
@php
    $TabBtns=['searchByAnnonce','searchByNID','searchByUID','searchByNAME','appliedForGrievance','SeatingNumbers','Filter'];
@endphp
<div class="row">
    <div class="col-3">
        <!-- Tab navs -->
        <div class="nav flex-column nav-tabs text-center" id="v-tabs-tab" role="tablist" aria-orientation="vertical" data-bs-theme="dark">
            @foreach($TabBtns as $btnlinked)
            <button 
                class="nav-link" 
                id="{{$btnlinked}}_link" 
                data-bs-toggle="tab" 
                data-bs-target="#{{$btnlinked}}_tab" 
                type="button" 
                role="tab" 
                aria-controls="{{trans('JOBLANG::Employment_Reports.'.$btnlinked)}}" aria-selected="true">
                    {{trans('JOBLANG::Employment_Reports.'.$btnlinked)}}
            </button>
            @endforeach
        </div>
        <!-- Tab navs -->
    </div>
    <div class="col-9">
        <!-- Tab content -->
        <div class="tab-content" id="v-tabs-tabContent">
        @foreach($TabBtns as $btnlinked)
            <div class="tab-pane fade show" id="{{$btnlinked}}_tab" role="tabpanel" aria-labelledby="{{$btnlinked}}_link">
                <div class='container right-direction'>
                    <div class='row'>
                        <div class='col-sm-12 border-bottom border-danger'>
                            <h4>{{trans('JOBLANG::Employment_Reports.'.$btnlinked)}}</h4>
                        </div>
                    </div>
                    <div class='row' id="content_{{$btnlinked}}">
                        @include("Employment::admin.searchForm.".$btnlinked)
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
<!-- SearchForm -->