<div class='row  text-right' id='newline'>
        <div class='col-sm-9 text-center'>
            <figure>
                <blockquote class="blockquote">
                {{$data['headerTitle']}}
                </blockquote>
                <figcaption class="blockquote-footer">
                @if(isset($data['headerTitleNote']))
                    <abbr class="initialism">{{$data['headerTitleNote']}} </abbr>
                    <cite>({{$request['Employment_StartAnnonces']['Employment_Stages'][0]}})</cite>
                @endif
                <br>
                @php echo implode(', ',$request['Employment_StartAnnonces']['Governorates']); @endphp
                </figcaption>
            </figure>
            {{trans('JOBLANG::apply.homepage_annonce_number')}} ({{$request['Employment_StartAnnonces']['Number']}}) {{trans('JOBLANG::apply.homepage_annonce_foryear')}} {{$request['Employment_StartAnnonces']['Year']}}
        </div>
        <div class="col-sm-3"><div class='SETQRCODE' style="position: absolute;float:left"></div></div>
</div>
            <div class='row border-bottom text-right align-items-start' id='newline'>
                @include('Employment::review.leftside')
            </div>
        @include('Employment::review.fullname')