<!-- mir -->
        <div class="row text-right"  id='div_maritalstatus_id'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}
            </div>
            <div class='col col-lg rounded border'>
                <select class="form-control" name="maritalstatus_id" id="maritalstatus_id" style="width:100%" option='Employment_MaritalStatus' dd='text' placeholder="{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}" old="{{old('maritalstatus_id') ?? $data->value->maritalstatus_id ?? ""}}" required>
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='maritalstatus_id_info'>
            </div>
        </div>
