<!-- submit -->
<div class="row text-right border-bottom" id="submitDiv">
            <div class='col-lg nsscwwbgcolor text-white text-center'>
                <div class='previewinput btn btn-info btn-rounded btn-block my-4 waves-effect z-depth-0'  id='review'  formtarget="_blank" formaction="{{url('','review')}}" onclick="beforeclick($(this))">
                <i class="fa fa-television fa-fw fa-lg" aria-hidden="true"></i>
                {{trans('JOBLANG::apply.pagetitle.review')}}
                </div>
                <div class='applyinput btn btn-success btn-rounded btn-block my-4 waves-effect z-depth-0'  id='submit'  formtarget="_blank" formaction="{{url('','review')}}" onclick="beforeclick($(this))">
                <i class="fa fa-calendar-check-o fa-lg fa-fw" aria-hidden="true"></i>
                    {{trans('JOBLANG::apply.apply_buttom_apply')}}
                </div>
                <div class='applyinput btn btn-light btn-rounded btn-block my-4 waves-effect z-depth-0'  id='submit'  formtarget="_blank" formaction="{{url('','review')}}" onclick="window.print()">
                <i class="fa fa-print fa-fw fa-lg"></i>
                {{trans('JOBLANG::apply.preview_buttom_print')}}
                </div>


                </div>
                </div>
                <!--
                <input type='submit' class='btn btn-amber d-flex p-3 my-3 btn-block waves-effect waves-light' id='headers' name='newapply' value='{{trans('trojan.apply_buttom_apply')}}' onclick="submit();">
                -->
