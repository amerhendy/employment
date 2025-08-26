<!-- health -->
<div class="row text-right" id='div_health'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_Health.Employment_Health')}}
            </div>
            <div class='col col-lg  rounded border'>
                <select class="form-control" name="health_id" id="Health_id" style="width:100%" option='Employment_Health' dd='text' placeholder="{{trans('JOBLANG::Employment_Health.Employment_Health')}}" old="{{old('Health_id') ?? $data->value->Health_id ?? ""}}" required>
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='health_info'>

            </div>
        </div>
