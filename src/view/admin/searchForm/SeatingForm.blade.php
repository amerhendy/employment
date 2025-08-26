<!-- SeatingForm -->
<?php
/*
ااختر نوع الطباعة
جدول ولا توزيع
الجدول للحضور
الجدول للنتائج لكافة الاعضاء
جدول نتائج تجميع أعضاء
*/
?>
<div class='container' id="SeatingForm">
<form 
        action="{{route('EmploymentsPrintForm')}}" 
        class="{{$trojan['html']['direction'] ?? 'right'}}-justification {{$trojan['html']['direction'] ?? 'right'}}-aligned {{$trojan['html']['direction'] ?? 'right'}}-middle" 
        enctype="multipart/form-data" 
        accept-charset="utf-8" 
        acc 
        autocomplete="on" 
        target="_blank" 
        method="POST"
        name="SeatingForm"
    >
        @csrf
        <input type="hidden" name="print_date" id="print_date" value='{{now()}}'>
        <input type="hidden" name="printSectionForm" id="printSectionForm" value='Seatings'>
        <textarea name="SeatingidsTextarea" id="SeatingidsTextarea"></textarea>
<div class='row' id="SeatingFormDiv">
        <div class='col-md-2  nsscwwbgcolor'>
            {{trans('JOBLANG::Employment_reports.SeatingForm.type')}}
        </div>
        <div class='col-sm-4'>
            <select id="SeatingPrintType" name="SeatingPrintType" data-set-select2=null class="form-control" style="width:100%">
                <option value="Table">{{trans('JOBLANG::Employment_reports.SeatingForm.table')}}</option>
                <option value="Ticket">{{trans('JOBLANG::Employment_reports.SeatingForm.Ticket')}}</option>
            </select>
        </div>
        <div class='col-md-2  nsscwwbgcolor'>
            {{trans('JOBLANG::Employment_Stages.singular')}}
        </div>
        <div class='col-sm-4'>
            <select id="SeatingPrintStage" name="SeatingPrintStage" data-set-select2=null class="form-control" style="width:100%">
                <option></option>
            </select>
        </div>
        <div class='col-md-2  nsscwwbgcolor' id="#forSeatingTablesSelect">
            {{trans('JOBLANG::Employment_Stages.singular')}}
        </div>
        <div class='col-sm-4' id="forSeatingTablesSelect">
        
            <select id="SeatingPrintTable" name="SeatingPrintTable" data-set-select2=null class="form-control" style="width:100%" >
                <option value="tableForSign">{{trans('JOBLANG::Employment_reports.SeatingForm.tableForSign')}}</option>
                <option value="tableForMembers">{{trans('JOBLANG::Employment_reports.SeatingForm.tableForMembers')}}</option>
                <option value="tableForCollection">{{trans('JOBLANG::Employment_reports.SeatingForm.tableForCollection')}}</option>
            </select>
        </div>
        <div class='col-sm-2' id=''>
            <button class='btn btn-primary btn-sm btn-circle' id='Seatingprint'><i class="fa fa-print"></i></button>
            <!--<span class='btn btn-primary btn-sm btn-circle' id='byAnnoncePrint'><i class="fa fa-print"></i></span> -->
        </div>
</div>
    </form>
</div>
<!-- SeatingForm -->