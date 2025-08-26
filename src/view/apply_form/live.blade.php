<!-- live -->
<div class="row text-right"  id='lp'>
    <div class='col-lg-3 bg-gradient text-white text-center'>
    {{trans('JOBLANG::Employment_people.LivePlace.LivePlace')}}
    </div>
    <div class='col col-lg md-form input-group  rounded border'>
        <div class="col-sm-12">
            <select class="form-control" name="livegov" id="livegov" style="width:100%" apilink='governorates' vl='id' sh='name' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.Governorator')}}" old="{{old('livegov')?? $data->value->livegov ?? ""}}" next='livecity' required>
                <option></option>
            </select>
            <select class="form-control" name="livecity" id="livecity" style="width:100%" apilink='cities' vl='id' sh='name' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.City')}}" old="{{old('livecity')?? $data->value->livecity ?? ""}}" hidden required>
                <option></option>
            </select>
        </div>
        <div class="md-form  w-100" id='liveaddress_div'  style='display:none';>
            <input type="text" minlen='5' class="form-control"  name="liveaddress" id="liveaddress" onblur='trim(this)' placeholder="{{trans('JOBLANG::Employment_People.LivePlace.Address')}}" value="{{old('liveaddress')?? $data->value->liveaddress ?? ""}}" required>
        </div>
        </div>
    <div class='col-lg-3 peach-gradient text-white text-center' id='live_info'>
        {{trans('JOBLANG::Employment_people.LivePlace.hint')}}
    </div>

</div>
