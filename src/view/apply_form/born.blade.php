<!-- born -->
<div class="row text-right" id='bp'>
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_people.bornPlace.bornPlace')}}
            </div>
            <div class='col col-lg  rounded border'>
                <div class="md-form input-group">
                    <div class="col-sm-12">
                        <select class="form-control" name="borngov" id="borngov" style="width:100%" apilink='governorates' vl='id' sh='name' placeholder="{{trans('JOBLANG::Employment_people.bornPlace.Governorate')}}" old="{{old('borngov') ?? $data->value->borngov ?? ""}}" next='borncity' required>
                            <option></option>
                        </select>
                    <select class="form-control" name="borncity" id="borncity" style="width:100%;display:none" apilink='cities' vl='id' sh='name' placeholder="{{trans('JOBLANG::Employment_people.bornPlace.City')}}" old="{{old('borncity') ?? $data->value->borncity ?? ""}}"  required>
                        <option></option>
                    </select>
                    </div>


            </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='born_info'>
            {{trans('JOBLANG::Employment_people.bornPlace.bornPlaceHint')}}
            </div>
        </div>
