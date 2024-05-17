<!-- arm -->
<div class="row text-right border-bottom" id='div_Employment_Army'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_Army.Employment_Army')}}
            </div>
            <div class='col col-lg'>
                <select class="form-control" name="Arm_id" id="Employment_Army" style="width:100%" data-init-function='set_arm' option='Employment_Army' dd='Text' placeholder="{{trans('JOBLANG::Employment_Army.Employment_Army')}}" old="{{old('Employment_Army') ?? $data->value->Employment_Army ?? ""}}"></select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='Employment_Army_info'>
            {{trans('JOBLANG::Employment_People.Employment_Army.hint')}}
            </div>
        </div>
