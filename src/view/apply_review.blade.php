<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="language" content="ar-eg">
<title>{{$page_title}}</title>
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
</head>
<body>
    <input type='hidden' name="QR" value="{{$request['QR']}}" data-init-function="SETQRCODE">
    @csrf
<page size="A4" class="con" id="">
    <header>
        <div class='row border-bottom' id='newline'>
            <div class='col-sm-4 text-center'>
            {{config('Amer.amer.co_name')}}
                <br>قطاع تنمية الموارد البشرية</p>
            </div>
            <div class='col-sm-4 logoarea'>
                        <div class='col-sm'><img id='polarimg'></div>
                    
                </div>
            <div class='col-sm-4 text-center'>
            {{config('Amer.amer.co_name_english')}}
                    <br>التاريخ:{{$request['user']['apply_date']}}
                </p>
            </div>
        </div>
    </header>
    <main>
    @include('Employment::review.main')
    </main>
    
</page>
</body>
<script>
websitelink="{{url('')}}";
</script>
@loadScriptOnce('js/jquery/jquery-3.6.0.min.js')
@loadScriptOnce('js/bootstrap/bootstrap.bundle.min.js')
@loadScriptOnce('js/packages/aos/aos.js')
@loadScriptOnce('js/packages/sweetalert/sweetalert2.all.min.js')
@loadScriptOnce('js/packages/noty/noty.min.js')
@loadScriptOnce('js/website.js')
@loadScriptOnce('js/forms.js')
@loadScriptOnce('js/packages/jquery-qrcode-master/jquery-qrcode-master/dist/jquery-qrcode.js')
<script title="" type="application/javascript">
    var requestUserData={{ Illuminate\Support\Js::from($request['user']) }}
    jv_errors=[];
    jv_errors['apply_date']="{{trans('employment.apply_date')}}";
    jv_errors['annonce_id']="{{trans('employment.homepage_annonce_number')}}";
    jv_errors['homepage_annonce_foryear']="{{trans('employment.homepage_annonce_foryear')}}";
    jv_errors['job_id']="{{trans('jobs.name')}}";
    jv_errors['fullname']="{{trans('employment.FULLname')}}";
    jv_errors['nid']="{{trans('employment.nid')}}";
    jv_errors['birth_date']="{{trans('employment.birth_date')}}";
    jv_errors['gender_id']="{{trans('employment.sex')}}";
    jv_errors['birth_blace']="{{trans('employment.birth_blace')}}";
    jv_errors['live_place']="{{trans('employment.live_place')}}";
    jv_errors['age']="{{trans('employment.age')}}";
    jv_errors['connect_landline']="{{trans('employment.landline_phone')}}";
    jv_errors['connect_mobile']="{{trans('employment.mobile_phone')}}";
    jv_errors['connect_email']="{{trans('employment.email')}}";
    jv_errors['health']="{{trans('health.health')}}";
    jv_errors['mir']="{{trans('mir.mir')}}";
    jv_errors['arm']="{{trans('arm.arm')}}";
    jv_errors['ama']="{{trans('ama.khedma_ama')}}";
    jv_errors['education']="{{trans('education.education')}}";
    jv_errors['edu_year']="{{trans('education.apply_edu_year')}}";
    jv_errors['khebra']="{{trans('jobs.khebra')}}";
    jv_errors['malaftaminy']="{{trans('employment.mlaftaminy')}}";
    jv_errors['degree']="{{trans('driver.driver_degree')}}";
    jv_errors['degreestart']="{{trans('driver.driver_start')}}";
    jv_errors['degreeend']="{{trans('driver.driver_end')}}";
    jv_errors['stage']="{{trans('stages.employment_stages')}}";
</script>
@loadScriptOnce('js/employment/apply_review.js')
<script title="" type="application/javascript">
initializeFieldsWithJavascript('page');

</script>

</html>
