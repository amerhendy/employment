<!-- app -->
<!DOCTYPE html>
<html lang="{{config('amer.lang') ?? 'ar-eg'}}" dir="{{config('amer.lang') ?? 'rtl'}} " prefix="{{config('amer.co_name') ?? 'HCWW'}}" data-bs-theme="auto">
    <head>
    <title>{{$page_title ?? config('Amer.amer.co_name') ?? 'Amer'}} :: {{config('Amer.amer.co_name') ?? 'Amer'}} </title>
        <base href="{{url('')}}">
        <meta name="theme-color" content    ="{{config('Amer.amer.html.theme-color') ?? 'white'}}">
        <meta name="description" content="{{config('Amer.amer.html.description') ?? 'AmerHendy'}}" />
        <meta http-equiv="Content-Type" content="text/html; charset={{config('Amer.amer.ENCODE') ?? 'UTF-8'}}">
        <meta name="language" content="Arabic">
        <meta name="revisit-after" content="7 days">
        <meta name="author" content="amer hendy">
        <meta name="generator" content="amer hendy"/>
        <meta name="referrer" content="origin"/>
        <meta name="referrer" content="origin-when-crossorigin"/>
        <meta name="referrer" content="origin-when-cross-origin"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{asset ('images/logo.png') ?? ''}}" rel="icon">
        <link href="{{asset ('images/logo.png') ?? ''}}" rel="apple-touch-icon">
        <link rel="alternate" type="application/rss+xml" title="Simplest Web" href="{{url('rss') ?? ''}}" />
        <?php
        $mainstyle=[
            ['url'=>'css/bootstrap/bootstrap.min.css','media'=>'all'],
            ['url'=>'css/bootstrap/bootstrap.rtl.min.css','media'=>'all'],
            ['url'=>'css/bootstrap/bootstrap-grid.rtl.min.css','media'=>'all'],
            ['url'=>'css/bootstrap/bootstrap-reboot.rtl.min.css','media'=>'all'],
            ['url'=>'css/bootstrap/bootstrap-utilities.rtl.min.css','media'=>'all'],
            ['url'=>'css/awesom/all.css','media'=>'all'],
            ['url'=>'css/awesom/brands.css','media'=>'all'],
            ['url'=>'css/awesom/fontawesome.css','media'=>'all'],
            ['url'=>'css/awesom/regular.css','media'=>'all'],
            ['url'=>'css/awesom/solid.css','media'=>'all'],
            ['url'=>'css/awesom/svg-with-js.css','media'=>'all'],
            ['url'=>'css/awesom/v4-shims.css','media'=>'all'],
            ['url'=>'js/packages/aos/aos.css','media'=>'all'],
            ['url'=>'js/packages/sweetalert/sweetalert2.min.css','media'=>'all'],
            ['url'=>'js/packages/noty/noty.css','media'=>'all'],
            ['url'=>'css/printpage.css','media'=>'print'],
            ['url'=>'css/printpagescreen.css','media'=>'screen'],
        ];
        ?>
        @if($mainstyle !== null)
            @foreach ($mainstyle as $path)
                @php
                    $csspath=$path['url']."?v=".config('amer.cachebusting_string');
                    $cssmediatype=$path['media'];
                @endphp
                @loadStyleOnce($csspath,$cssmediatype)
            @endforeach
        @endif
        <style>
            :root,
            [data-bs-theme=light] {
            --bs-link-color-rgb: 250, 250, 250;
        }
        [data-bs-theme=light] {
            --fa--map--maker:#000;
            
        }
            @font-face {
                font-family: AmerHendyAli;
                src: url('{{asset("fonts/c.ttf")}}');
            }
            body{
                font-family: 'AmerHendyAli', sans-serif,'Big Shoulders Display', cursive;
                direction: rtl;
            }
            html,body,div,li,nav,ul,a,.breadcrumb,header,.section ,.pace{
                    font-family: 'AmerHendyAli'!important; 
                    direction: rtl;
                }
        </style>
    </head>
    <body>
        <?php
        $coInfo=config('Amer.amer');
        ?>
        <template id="PageTemplate">    
            <page data-id="" size="A4" class="con" style="margin: 0;padding: 0;">
                <table class="pagecontent" cellpadding="0" cellspacing="0" style="width: 100%;resize: none;margin: 0;padding: 0;">
                    <thead class="header">
                        <tr>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        <tr id="headerTable" class="border-bottom">
                            <td colspan="4">{{$coInfo['hc_name']}}
                                <br>
                                {{$coInfo['co_name']}}
                                <br>{{trans('JOBLANG::Employment_Reports.printForm.PageName')}}: <i id="pageidentify"></i>
                            </td>
                            <td colspan="4"><img src="{{asset($coInfo['co_logo'])}}" id="polarimg"></td>
                            <td colspan="4">الاعلان رقم (<i id="annonceNmber"></i>) لسنة (<i id="annonceYear"></i>)
                        <br>
                        الوظيفة: <i id="shortJobName"></i>
                        <br>
                        الرقم التعريفى: <i id="Uid"></i>
                        <br>
                        الرقم القومى: <i id="Nid"></i>
                        <br>
                        تاريخ الاستعلام: <i id="Date"></i>
                    </td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <P class="fs-6 fw-bolder text-wrap text-break">{{implode("<br>",$coInfo['co_address'])}}</p>
                            </td>
                            <td colspan="4">
                            <P class="fs-6 fw-bolder text-wrap text-break"></p>
                            @foreach(config('Amer.amer.socialmedia.fax') as $a=>$b)
                                    <span class="fs-6">
                                        <span class="{{ $b['icon'] ?? 'fa fa-fax'}}"></span>
                                        {{$b['link']}}
                                    </span>
                            @endforeach
                            @foreach(config('Amer.amer.socialmedia.phone') as $a=>$b)
                                    <span class="fs-6">
                                        <span class="{{ $b['icon'] ?? 'fa fa-phone'}}"></span>
                                        {{$b['link']}}
                                    </span>
                            @endforeach
                            </td>
                            <td colspan="4">
                                @foreach(config('Amer.amer.socialmedia.email') as $a=>$b)
                                    <span class="fs-6">
                                        <span class="{{ $b['icon'] ?? 'fa fa-email'}}"></span>
                                        {{$b['link']}}
                                    </span>
                            @endforeach
                            @foreach(config('Amer.amer.socialmedia.website') as $a=>$b)
                                    <span class="fs-6">
                                        <span class="{{ $b['icon'] ?? 'fa fa-home'}}"></span>
                                        {{$b['link']}}
                                    </span>
                            @endforeach
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </page>
            </template>
    </body>
    <?php
    $websiteLink=url('');
    ?>
    <script type="application/javascript">
    var websitelink="{{$websiteLink}}";
    var api=websitelink+`/api/{{config('Amer.amer.api_version')}}/`;
    const clientInfo=`{{base64_encode(json_encode([base64_encode(env("API_CLIENT_ID") ?? ""),base64_encode(env("API_CLIENT_SECRET") ?? "")]))}}`;
    const Data={{ Illuminate\Support\Js::from($data) }};
    </script>
    <?php
    $mainscript=[
        'js/jquery/jquery-3.6.0.min.js',
        'js/bootstrap/bootstrap.bundle.min.js',
        'js/packages/aos/aos.js',
        'js/packages/sweetalert/sweetalert2.all.min.js',
        'js/packages/noty/noty.min.js',
        'js/packages/jquery-qrcode-master/jquery-qrcode-master/dist/jquery-qrcode.js',
        'js/website.js',
        'js/forms.js',
        'js/apiRequest.js',
        'js/employment/adminUptodatePrint.js',
    ];
    ?>
        @foreach ($mainscript as $path)
            @loadScriptOnce($path)
        @endforeach
</html>
