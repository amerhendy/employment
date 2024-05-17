<!-- live -->
<div class="row text-right border-bottom"  id='lp'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_people.LivePlace.LivePlace')}}
            </div>
            <div class='col col-lg'>
            <div class="md-form input-group">
                <div class="col-sm-6">
                    <select class="form-control" name="LiveGov" id="LiveGov" style="width:100%" data-init-function="load_default_LiveGov" apilink='governorates' vl='id' sh='Name' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.Governorator')}}" old="{{old('LiveGov')?? $data->value->LiveGov ?? ""}}" next='LiveCity'>
                        <option></option>
                    </select>
                </div>
                <div class="col-sm-6">    
                <select class="form-control" name="LiveCity" id="LiveCity" style="width:100%" apilink='cities' vl='id' sh='Name' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.City')}}" old="{{old('LiveCity')?? $data->value->LiveCity ?? ""}}" hidden>
                        <option></option>
                    </select>
                </div>
                <div class="md-form  w-100" id='LiveAddress_div'  style='display:none';>
                    <input type="text" minlen='5' class="form-control"  name="LiveAddress" id="LiveAddress" onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.Address')}}" value="{{old('LiveAddress')?? $data->value->LiveAddress ?? ""}}">
                </div>
            </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='live_info'>
            {{trans('JOBLANG::Employment_people.LivePlace.hint')}}
            </div>
        </div>
