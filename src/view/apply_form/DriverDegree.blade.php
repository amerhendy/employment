<!-- driver -->
<div class="row text-right border-bottom full_Employment_Drivers" id="full_Employment_Drivers">
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
                {{trans("JOBLANG::Employment_Drivers.Employment_Drivers")}}
            </div>
            <div class='col col-lg'>
            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}
            <select class="form-group form-control w-100 select2" name="DriverDegree" id="DriverDegree" data-init-function='set_driver' option='Employment_Drivers' dd='Text'  placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}" old="{{old('DriverDegree') ?? $data->value->DriverDegree ?? ""}}" >
                        </select>
                        <div id='cardegreediv'>
                            <label class="text-right">
                            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}
                            </label>
                                <input type="date" name="DriverStart" class="form-control w-100" onblur='trim(this)' placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}" value="{{old('DriverStart') ?? $data->value->DriverStart ?? ""}}" minlen='10'>
                            <label class="text-right">
                            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}
                            </label>
                            <input type="date" name="DriverEnd" class="form-control w-100" onblur='trim(this)' value="{{old('DriverEnd') ?? $data->value->DriverEnd ?? ""}}" placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}" minlen='10'>
                        </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='driver_info'>
            {{trans("JOBLANG::Employment_People.Employment_Drivers.hint")}}
            </div>
        </div>
