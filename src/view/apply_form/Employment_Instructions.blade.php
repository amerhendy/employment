<!-- agreement -->
<div class="row text-right border-bottom">
            <div class='col-lg-3 primary-color nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_Instructions.Employment_Instructions')}}
            </div>
            <div class='col col-lg'>
            {{trans('JOBLANG::Employment_People.Employment_Instructions.hint')}}
            <div class="Employment_Instructions">
            </div>
                <div class="form-group form-check nsscwwbgcolor text-white">
                <input type="checkbox" value="1" class="form-check-input" id="acceptall" name="acceptall" placeholder="{{trans('JOBLANG::Employment_People.Employment_Instructions.CheckHint')}}" @if(old('acceptall') == '1') checked @endif>
                <label class="form-check-label" for="acceptall">
                    {{trans('JOBLANG::Employment_People.Employment_Instructions.CheckHint')}}
                </label>
            </div>
            </div>
        </div>
