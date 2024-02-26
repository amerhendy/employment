<!-- mir -->
        <div class="row text-right border-bottom"  id='div_job_select'>
            <div class='col-lg-3 nsscwwbgcolor text-white text-center'>
            {{trans('JOBLANG::apply.Employment_Jobs.selectJob')}}
            </div>
            <div class='col col-lg'>
                <select class="form-control" name="select_job" id="select_job" style="width:100%" data-init-function='select_job' option='job' dd='text' placeholder="{{trans('jobs.please_select_job')}}" old="{{old('select_job') ?? $job}}">
                </select>
            </div>
            <div class='col-lg-3 peach-gradient text-white text-center' id='select_job_info'>
            {{trans('JOBLANG::apply.Employment_Jobs.selectJobInfo')}}
            </div>
        </div>
