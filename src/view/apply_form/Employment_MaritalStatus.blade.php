<!-- mir -->
        <div class="row text-right border-bottom"  id='div_MaritalStatus_id'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
            </div>
            <div class='col col-lg'>
                <select class="form-control" name="MaritalStatus_id" id="MaritalStatus_id" style="width:100%" data-init-function='set_mir' option='Employment_MaritalStatus' dd='Text' placeholder="{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}" old="{{old('MaritalStatus_id') ?? $data->value->MaritalStatus_id ?? ""}}">
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='MaritalStatus_id_info'>
            </div>
        </div>
