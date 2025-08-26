<!-- TableArea -->
<div class="dbTable"></div>
<template id="dbTable">
    <div class="row">
        <div id="AmerTable_wrapper"></div>
        <div class="col-sm-6">
            <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none"></div>
        </div>
        <div class="col-sm-12">
            <table id="AmerTable" class="display stripe table-border nowrap row-border order-column cell-border compact">
                <thead>
                    <tr class='border'>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.uid')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.NID')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_StartAnnonces.singular')}}</th>
                        <th class='border text-center' colspan='3'>{{trans('JOBLANG::Employment_Reports.lastStage.Entry')}}</th>
                        <th class='border text-center' colspan='3'>{{trans('JOBLANG::Employment_Reports.lastStage.Convert')}}</th>
                        <th class='border text-center' colspan='3'>{{trans('JOBLANG::Employment_Reports.FinalStage')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_Jobs.plural')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.FULLname')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.LivePlace.LivePlace')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.bornPlace.bornPlace')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Sex.Sex')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.BirthDate')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Age.Age')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Connection.Connection')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_Health.Employment_Health')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_Army.Employment_Army')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Tamin.Tamin')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Khebra.Khebra')}}</th>
                        <th  class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Mosama_Educations.Mosama_Educations')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverDegree')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverStart')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverEnd')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_Stages.plural')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_seatings.Employment_seatings')}}</th>
                        <th class='border text-center' rowspan=2 data-class-name="priority">{{trans('JOBLANG::Employment_people.Uploaded_files')}}</th>
                    </tr>
                    <tr>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th class='text-center' data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.uid')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.NID')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_StartAnnonces.singular')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Name')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Result')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Reports.lastStage.Message')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Jobs.plural')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.FULLname')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.LivePlace.LivePlace')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.bornPlace.bornPlace')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Sex.Sex')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.BirthDate')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Age.Age')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Connection.Connection')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Health.Employment_Health')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Army.Employment_Army')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Ama.Employment_Ama')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Tamin.Tamin')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Khebra.Khebra')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Mosama_Educations.Mosama_Educations')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverDegree')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverStart')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Employment_Drivers.DriverEnd')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_Stages.plural')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_seatings.Employment_seatings')}}</th>
                        <th data-class-name="priority">{{trans('JOBLANG::Employment_people.Uploaded_files')}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="d-print-none"></div>
            <div id="datatable_info_stack" class="mt-sm-0 mt-2 d-print-none"></div>
            <div id="bottom_buttons"></div>
            <div class="btn-group" role="group" id="operations">
                <button type="button" class='btn btn-primary btn-sm btn-circle' id='uptodatecols'><i class="fa fa-edit"></i></button>
                <button type="button" class='btn btn-primary btn-sm btn-circle' id='printcols' onclick="window.gotoprint()"><i class="fa fa-print"></i></button>
                <button type="button" class='btn btn-primary btn-sm btn-circle' id='downloadcols'><i class="fa fa-download"></i></button>
                <button type="button" class='btn btn-primary btn-sm btn-circle' id='Seatingcols'><i class="fa fa-edit"></i></button>
            </div>
        </div>
</div>
</template>
@push('after_scripts')
<?php
$DefaultPageLength=15;
$Amer_alerts = \Alert::getMessages();
$lengthMenu=[10,15,20,50,100,1000,0];
?>
<script type="application/javascript">
    let Route="{{\Route::currentRouteName()}}";
    let SlugRoute="{{ Str::slug(\Route::currentRouteName()) }}";
    let pathinfo="{{ url(\Request::instance()->getpathInfo()) }}";
    let $lengthMenu=@json($lengthMenu);
    var DefaultPageLength = {{ $DefaultPageLength }};
    $newAlerts = @json($Amer_alerts);
    
    window.Amer.TableTranslation={
                    "emptyTable":     "{{ trans('AMER::datatables.emptyTable') }}",
                    "info":           "{{ trans('AMER::datatables.info') }}",
                    "infoEmpty":      "{{ trans('AMER::datatables.infoEmpty') }}",
                    "infoFiltered":   "{{ trans('AMER::datatables.infoFiltered') }}",
                    "infoPostFix":    "{{ trans('AMER::datatables.infoPostFix') }}",
                    "thousands":      "{{ trans('AMER::datatables.thousands') }}",
                    "lengthMenu":     "{{ trans('AMER::datatables.lengthMenu') }}",
                    "loadingRecords": "{{ trans('AMER::datatables.loadingRecords') }}",
                    //"processing":     "<img src='{{ asset('images/nsscww.gif') }}' class='Loading'>",
                    "processing":`<div class="spinner-grow text-primary " style="width: 3rem; height: 3rem;" role="status">
                                        <span class="sr-only">Loading...</span>
                                            </div>`,
                    "search": "_INPUT_",
                    "searchPlaceholder": "{{ trans('AMER::datatables.search') }}...",
                    "zeroRecords":    "{{ trans('AMER::datatables.zeroRecords') }}",
                    "paginate": {
                        "first":      "{{ trans('AMER::datatables.paginate.first') }}",
                        "last":       "{{ trans('AMER::datatables.paginate.last') }}",
                        "next":       ">",
                        "previous":   "<"
                    },
                    "aria": {
                        "sortAscending":  "{{ trans('AMER::datatables.aria.sortAscending') }}",
                        "sortDescending": "{{ trans('AMER::datatables.aria.sortDescending') }}"
                    },
                    "buttons": {
                        "copy":   "<i class='fa fa-copy'></i>",
                        "excel":  "<i class='fa fa-file-excel-o'></i>",
                        "csv":    "<i class='fa-solid fa-file-csv'></i>",
                        "pdf":    "<i class='fa fa-file-pdf-o'></i>",
                        "print":  "<i class='fa fa-print'></i>",
                        "colvis": "{{ trans('AMER::datatables.export.column_visibility') }}"
                    },
                };
                window.Amer.dataTableConfiguration.language= window.Amer.TableTranslation;
  </script>
@endpush