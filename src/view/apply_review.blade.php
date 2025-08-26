@extends(Baseview('app'))

<title>{{$page_title}}</title>
@push('after_styles')
@loadStyleOnce('css/bootstrap/bootstrap.min.css')
@loadStyleOnce('css/bootstrap/bootstrap.rtl.min.css')
@loadStyleOnce('js/packages/noty/noty.css')
<link rel="stylesheet" type="text/css" href="{{asset('css/printpage.css')}}" media="print">
<style>
    @font-face {
                font-family: AmerHendyAli;
                src: url('{{asset("fonts/c.ttf")}}');
            }
    @media print{
        .noty_layout{
            display:none !important;
        }
    }
    @media screen{
        html,body {
            background: rgb(255,255,255);
            font-family: 'AmerHendyAli','Arial';
            direction: rtl;
            font-size:12pt;
        }
        #polarimg {
        object-fit: contain;
        max-width:50%;
        max-height:50%;;
        vertical-align: middle;
        border-style: none;
        }
        page{
            background: white;
            display: block;
            padding:0.5cm;
            margin:1cm;
            box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
            border:1px solid;
            width:8.27in;
            height:11.69in;
            font-size:12pt;
    }
    .border-bottom {
        border-bottom: 1px solid #000 !important;
    }
    .border-bottom-colored {
        border-bottom: 1px dashed #dee2e6 !important;
    }
    .border-right {
        border-right: 1px solid #dee2e6 !important;
    }
    .border-left {
        border-left: 1px solid #dee2e6 !important;
    }
    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }
    .border{border: 1px solid #000 !important;}
    }
    .leftside{
        font-size:1rem;
    }
</style>
@endpush
@section('content')
    @parent

<body>
    @csrf
    <div class="container" id="jobInfoSection"></div>
</body>
@endsection
@push('after_scripts')
<script>
websitelink="{{url('')}}";
</script>
<script title="" type="application/javascript">
    var data={{ Illuminate\Support\Js::from($data) }}
    if(typeof data == 'string'){
        if(startwith(data,'Content-Type: application/pdf;')){
            var  section=$('#jobInfoSection');
            var file= new Blob([data],{type:'application/pdf'});
                    var st=data.split(';\r\n');
                    var st=st[2].split('\r\n\r\n')
                    var iframe= document.createElement('iframe');
                    $(iframe).attr('style','top:0; left:0; bottom:0; right:0; width:100%; height:30cm; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;');
                    section.html(iframe);
                    iframe.src="data:application/pdf;base64,"+st[1]
        }
    }
    
</script>
@endpush
