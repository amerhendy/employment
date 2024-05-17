<!-- connection -->
<div class="row text-right border-bottom" id='div_con'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_people.Connection.Connection')}}
            </div>
            <div class='col col-lg'>
            <div class="row">
                      <div class="col-sm">
                          <label class="text-right">
                          {{trans('JOBLANG::Employment_people.Connection.LandLine')}}
                            </label>
                          <input type="tel" name="ConnectLandline" value="{{old('ConnectLandline') ?? $data->value->ConnectLandline ?? ""}}" class="form-control w-100" onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_people.Connection.LandLine')}}" minlen='8'>
                      </div>
                      <div class="col-sm">
                          <label class="text-right col_require">
                          {{trans('JOBLANG::Employment_people.Connection.Mobile')}}
                            </label>
                          <input type="tel" name="ConnectMobile" value="{{old('ConnectMobile') ?? $data->value->ConnectMobile ?? ""}}" class="form-control w-100" onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_people.Connection.Mobile')}}" minlen='10'>
                      </div>
                      <div class="col-sm">
                          <label class="text-right col_require">
                          {{trans('JOBLANG::Employment_people.Connection.Email')}}
                            </label>
                          <input type="email" name="ConnectEmail" value="{{old('ConnectEmail')?? $data->value->ConnectEmail ?? ""}}" class="form-control w-100" onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_people.Connection.Email')}}">
                      </div>
                  </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='connection_info'>
            {{trans('JOBLANG::Employment_people.Connection.hint')}}
            </div>
        </div>
