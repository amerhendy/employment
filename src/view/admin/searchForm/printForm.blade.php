<!-- printForm -->
<div class='container' id="printform">
    <form 
        action="{{route('EmploymentsPrintForm')}}" 
        class="{{$trojan['html']['direction'] ?? 'right'}}-justification {{$trojan['html']['direction'] ?? 'right'}}-aligned {{$trojan['html']['direction'] ?? 'right'}}-middle" 
        enctype="multipart/form-data" 
        accept-charset="utf-8" 
        acc 
        autocomplete="on" 
        target="_blank" 
        method="POST"
        name="printForm">
        @csrf
        <input type="hidden" name="print_date" id="print_date" value='{{now()}}'>
        <input type="hidden" name="printSectionForm" id="printSectionForm" value='files'>
    <div class='row'>
        <div class='col-md-2  nsscwwbgcolor' id='actionareatitle'>
            <span>{{trans('JOBLANG::Employment_Reports.printForm.action')}}</span>
        </div>
        <div class='col-sm-10' style='' id='actionarea'>
            <div class="btn-group flex-wrap" role="group" aria-label="Basic outlined example">
                <?php
                $checkbtnGroups=trans('JOBLANG::Employment_Reports.printForm.actions')
                ?>
                @foreach($checkbtnGroups as $a=>$b)
                <input type="checkbox" name="actions[]" value="{{$a}}" class="btn-check" id="btn-check-outlined_{{$a}}" autocomplete="off">
                <label class="btn btn-outline-primary" for="btn-check-outlined_{{$a}}">{{$b}}</label><br>
                @endforeach
            </div>
        </div>
        <div class='col-md-2  nsscwwbgcolor' style='' id='typestitle'>
            <span>{{trans('JOBLANG::Employment_Reports.printForm.type')}}</span>
        </div>
        <?php
        $TypesList=trans('JOBLANG::Employment_Reports.printForm.types')
        ?>
        <div class='col-sm-10' style='' id='types'>
            <select id='types' name='types' class="form-control">
                @foreach($TypesList as $a=>$b)
                    <option value="{{$a}}">{{$b}}</option>
                @endforeach
            </select>
        </div>
        <textarea id="PrintidsTextArea" name="PrintidsTextArea"></textarea>
        <div class='col-sm-6' id=''><button name="submit" type="submit" class='btn btn-primary' id='print'><span>{{trans('JOBLANG::Employment_Reports.printForm.print')}}</span></button></div>
    </div>
</form>
</div>
<!-- printForm -->+