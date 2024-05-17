<!-- included_files -->
        <div class="row text-right border-bottom">
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}
            </div>
            <div class='col col-lg'>
                <div class="col-12 container included_files_apply">
                    <ul class="list-unstyled row">
                        <li class=" list-item col-4 border-top py-2"></li>
                    </ul>
                </div>
                <div class='col-lg-12 nsscwwbgcolor text-white float-left text-left'>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputGroupFile01"
                        aria-describedby="inputGroupFileAddon01"  name='uploades' accept='application/pdf' placeholder="{{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}" value="{{old('uploades') ?? $data->value->uploades ?? ""}}">
                        <label class="custom-file-label" for="inputGroupFile01">{{trans("JOBLANG::Employment_People.uploadLabel")}}</label>
                        
                    </div>
                </div>

                </div>
            </div>
        </div>
