<!-- health -->
<div class="row text-right border-bottom" id='div_health'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_Health.Employment_Health')}}
            </div>
            <div class='col col-lg' >
                <select class="form-control" name="Health_id" id="Health_id" style="width:100%" data-init-function='sethealth' option='Employment_Health' dd='Text' placeholder="{{trans('JOBLANG::Employment_Health.Employment_Health')}}" old="{{old('Health_id') ?? $data->value->Health_id ?? ""}}">
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='health_info'>
            
            </div>
        </div>
