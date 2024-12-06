 <!-- driver -->
<div class="row text-rightfull_Employment_Drivers" id="full_Employment_Drivers">
            <div class='col-lg-3 bg-gradient text-white text-center'>
                {{trans("JOBLANG::Employment_Drivers.Employment_Drivers")}}
            </div>
            <div class='col col-lg  rounded border'>
            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}
            <select class="form-group form-control w-100 select2" name="driverdegree" id="driverdegree" option='Employment_Drivers' dd='text'  placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree")}}" old="{{old('driverdegree') ?? $data->value->driverdegree ?? ""}}" required>
                        </select>
                        <div id='cardegreediv'>
                            <label class="text-right">
                            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}
                            </label>
                                <input type="text" name="driverstart" data-provide="datepicker" class="form-control w-100" onblur='trim(this)' placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart")}}" value="{{old('driverstart') ?? $data->value->driverstart ?? ""}}" minlen='10' required>
                            <label class="text-right">
                            {{trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}
                            </label>
                            <input type="text" name="driverend" data-provide="datepicker" class="form-control w-100" onblur='trim(this)' value="{{old('driverend') ?? $data->value->driverend ?? ""}}" placeholder="{{trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd")}}" minlen='10' required>
                        </div>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='driver_info'>
            {{trans("JOBLANG::Employment_People.Employment_Drivers.hint")}}
            </div>
        </div>
