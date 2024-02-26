<!-- born -->
<div class="row text-right border-bottom" id='bp'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_people.bornPlace.bornPlace')}}
            </div>
            <div class='col col-lg'>
                <div class="md-form input-group">
                    <div class="col-sm-6">
                        <label for"BornGov">{{trans('JOBLANG::Employment_people.bornPlace.Governorate')}}</label>
                        <select class="form-control" name="BornGov" id="BornGov" style="width:100%" data-init-function="load_default_LiveGov" apilink='governorates' vl='id' sh='Name' placeholder="{{trans('JOBLANG::Employment_people.bornPlace.Governorate')}}" old="{{old('BornGov') ?? $data->value->BornGov}}" next='BornCity'>
                            <option></option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                    <label for"BornGov">{{trans('JOBLANG::Employment_people.bornPlace.City')}}</label>
                    <select class="form-control" name="BornCity" id="BornCity" style="width:100%;display:none" apilink='cities' vl='id' sh='Name' placeholder="{{trans('JOBLANG::Employment_people.bornPlace.City')}}" old="{{old('BornCity') ?? $data->value->BornCity}}">
                        <option></option>
                    </select>
                    </div>
                
                    
            </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='born_info'>
            {{trans('JOBLANG::Employment_people.bornPlace.bornPlaceHint')}}
            </div>
        </div>
