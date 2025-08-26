<!-- included_files -->
        <div class="row text-right">
            <div class='col-lg-3 bg-gradient text-white text-center'>
            {{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}
            </div>
            <div class='col col-lg rounded border'>
                <div class="col-12 container included_files_apply">
                    <ul class="list-unstyled row">
                        <li class=" list-item col-4 border-top py-2"></li>
                    </ul>
                </div>
                <div class='col-lg-12 bg-gradient text-white float-left text-left input-group'>
                    <div class="custom-file">
                        <input type="file"
                        class="custom-file-input"
                        id="inputGroupFile01"
                        aria-describedby="inputGroupFileAddon01"
                        name='uploades'
                        accept='application/pdf'
                        placeholder="{{trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles')}}"
                        value="{{old('uploades') ?? $data->value->uploades ?? ""}}"
                        required
                        >
                        <label class="custom-file-label" for="inputGroupFile01" style="width:100% !important;">{{trans("JOBLANG::Employment_People.uploadLabel")}}</label>

                    </div>
                </div>
            </div>
        </div>
