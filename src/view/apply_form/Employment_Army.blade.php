<!-- arm -->
<div class="row text-right" id='div_Employment_Army'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_Army.Employment_Army')}}
            </div>
            <div class='col col-lg  rounded border'>
                <select class="form-control" name="arm_id" id="Employment_Army" style="width:100%" option='Employment_Army' dd='text' placeholder="{{trans('JOBLANG::Employment_Army.Employment_Army')}}" old="{{old('Employment_Army') ?? $data->value->Employment_Army ?? ""}}" required></select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Employment_Army_info'>
            {{trans('JOBLANG::Employment_People.Employment_Army.hint')}}
            </div>
        </div>
