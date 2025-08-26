<!-- agreement -->
<div class="row text-right">
            <div class='col-lg-3 primary-color bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_Instructions.Employment_Instructions')}}
            </div>
            <div class='col col-lg rounded border'>
            {{trans('JOBLANG::Employment_People.Employment_Instructions.hint')}}
            <div class="Employment_Instructions" style="text-align: justify;">
            </div>
                <div class="form-group form-check">
                <input type="checkbox" value="1" class="form-check-input" id="acceptall" name="acceptall" placeholder="{{trans('JOBLANG::Employment_People.Employment_Instructions.CheckHint')}}" @if(old('acceptall') == '1') checked @endif required>
                <label class="form-check-label" for="acceptall" style="width:100% !important;">
                    {{trans('JOBLANG::Employment_People.Employment_Instructions.CheckHint')}}
                </label>
            </div>
            </div>
        </div>
