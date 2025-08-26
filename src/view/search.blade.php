@extends(Baseview('app'))
@push('after_styles')
<style>
.requireinput{
            border-color: #0B90C4;
        }
        .has-error{
            border-color: rgb(185, 74, 72) !important;
        }
        .noty_body{
            color:#fff !important;
            background-color: rgba(0,183,74,var(--mdb-bg-opacity)) !important;
            background-image: var(--mdb-gradient) !important;
            --mdb-bg-opacity: 1;
        }
    </style>
@endpush
@push('scripts')
<script type="application/javascript">
    jstrans['nidisnot14']="{{trans('JOBLANG::apply.nidisnot14')}}";
    jstrans['pleasefillinputs']="{{trans('trojan.pleasefillinputs')}}";
    jstrans['nid']="{{trans('JOBLANG::Employment_People.NID')}}";
    jstrans['uid']="{{trans('JOBLANG::Employment_People.uid')}}";
    jstrans['fullname']="{{trans('JOBLANG::Employment_People.FULLname')}}";
    jstrans['nid_not_Exists']="{{trans('employment.this_nid_not_in_annonce')}}";
    jstrans['nid_phisical_error']="{{trans('employment.please_enter_right_nid')}}";
    jstrans['Search']="{{trans('JOBLANG::apply.Search.Search')}}";
    jstrans['result']="{{trans('JOBLANG::apply.Search.TheResult')}}";
    jstrans['message']="{{trans('JOBLANG::apply.Search.TheMessage')}}";
    jstrans['instructions']="{{trans('JOBLANG::apply.Search.Instructions')}}";
    
    jstrans['annonce_name']="{{trans('JOBLANG::Employment_StartAnnonces.singular')}}";
    jstrans['homepage_annonce_number']="{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_number')}}";
    jstrans['homepage_annonce_foryear']="{{trans('JOBLANG::Employment_StartAnnonces.homepage_annonce_foryear')}}";
    jstrans['job_name']="{{trans('JOBLANG::Employment_Jobs.plural')}}";
    jstrans['code']="{{trans('JOBLANG::Employment_Jobs.Code')}}";

    jstrans['lookforresult']="{{trans('employment.lookforresult')}}";
    
    
    jstrans['stop']="{{trans('employment.stop')}}";
    jstrans['youcantcomplete']="{{trans('employment.youcantcomplete')}}";
    const STATUS={{ Illuminate\Support\Js::from($data['status']) }};
  </script>
@loadScriptOnce('js/packages/jquery-qrcode-master/jquery-qrcode-master/dist/jquery-qrcode.js')
@loadScriptOnce(['js/employment/search.js',"type='module'"])
@endpush
@section('content')
   @parent
   <form class="right-justification right-aligned right-middle">
    <input type="hidden" value="{{$job}}" id='job' name="job">
    <input type="hidden" value="{{$annonce}}" id='annonce' name="annonce">
    <input type="hidden" value="search" id='page' name="page">
    @csrf
    @method('POST')
    <div class="container-fluid border rounded shadow d-none" id="selectbox">
        <div class="row text-right border-bottom" id='al_div'>
            <div class="col-sm-2">
                {{trans('JOBLANG::Employment_People.NID')}}
            </div>
            <div class="col-sm-5">
                <input type="number" name="nid" id="nid" class="form-control w-100">
            </div>
            <div class="col-sm-1 btn btn-primary text-center p-0 my-0 text-white rounded searchbutton"  name="search" id='search'>
            {{trans('JOBLANG::apply.Search.Search')}}
            </div>
            <div class="col-sm-1 btn btn-primary text-center p-0 my-0 text-white rounded searchbutton" id='printBTN' stylefile='@json([asset("css/bootstrap/bootstrap.min.css"),asset("css/bootstrap/bootstrap.rtl.min.css")])'>
            {{trans('JOBLANG::apply.preview_buttom_print')}}
            </div>
        </div>
    </div>
</form>
<br>
<!-------------------------------------->
<template id="SearchResultTemplate">
    <div class="border border-primary rounded" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultLongTitle">
                    {{trans('JOBLANG::apply.Search.searchpage_searchresult')}}
                </h5>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 text-right">
                            <div class="row">
                                <div class="col-md-3 text-right">{{trans('JOBLANG::Employment_People.uid')}}</div>
                                <div class="col-md text-right" id="SearchResult_id"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-right">{{trans('JOBLANG::Employment_People.NID')}}</div>
                                <div class="col-md text-right" id="SearchResult_nid"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-right">{{trans('JOBLANG::Employment_People.FULLname')}}</div>
                                <div class="col-md  text-right" id="SearchResult_fullname"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-right">{{trans('JOBLANG::Employment_StartAnnonces.singular')}}</div>
                                <div class="col-md  text-right" id="SearchResult_annonce"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-right">{{trans('JOBLANG::Employment_Jobs.plural')}}</div>
                                <div class="col-md  text-right" id="SearchResult_job"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 text-right">{{trans('JOBLANG::apply.Search.TheResult')}}</div>
                                <div class="col-md  text-right" id="SearchResult_result"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 text-right">{{trans('JOBLANG::apply.Search.TheMessage')}}</div>
                                <div class="col-md  text-right" id="SearchResult_message"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 text-right">{{trans('JOBLANG::apply.Search.Instructions')}}</div>
                                <div class="col-md  text-right" id="SearchResult_Instructions"></div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="row">
                                <div class="col-sm-12" id="QrCode"></div>
                                <div class="col-sm-12" id="servertime"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<!-------------------------------------->
<div id='demo' class="container">

</div>

@endsection
