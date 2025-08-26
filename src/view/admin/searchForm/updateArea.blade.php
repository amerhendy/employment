<!-- updateArea -->
<div class='container' id="updatearea">
<div class='row'>
    <form name="uptodateform" class="right-justification right-aligned right-middle ">
    {{csrf_field()}}
    <input type='hidden' value="{{Auth()->guard('Amer')->user()->id}}" name='publisher'>
    <textarea name="uptoidsTextarea" id="uptoidsTextarea"></textarea>
      <div class="container border rounded shadow">
      <div class="row text-right border-bottom" id='new_res_div'>
          <div class="col-sm-2">
            {{trans('JOBLANG::Employment_Reports.UpToDateForm.newStage')}}
          </div>
          <div class="col-sm-10">
              <select class='select2 form-control w-80' id='new_stage' name='new_stage'></select>
          </div>
      </div>
      <div class="row text-right border-bottom" id='new_res_div'>
          <div class="col-sm-2">
            {{trans('JOBLANG::Employment_Reports.UpToDateForm.newStatus')}}
          </div>
          <div class="col-sm-10">
              <select class='select2 form-control w-80' id='new_res' name='new_res'></select>
          </div>
      </div>
      <div class="row">
        <div class="col-sm-4 btn-group" role="group" style="">
          <button type="button" class="btn btn-primary btn-sm btn-circle" id="addDegrees">{{trans('JOBLANG::Employment_Reports.UpToDateForm.addDegrees')}}</button>
          <button type="button" class="btn btn-primary btn-sm btn-circle" id="selectMessageTemplate">{{trans('JOBLANG::Employment_Reports.UpToDateForm.selectMessageTemplate')}}</button>
        </div>
      </div>
      <div class="row text-right border-bottom">
      <div class="row"  style="overflow:scroll !important;overflow-x: hidden !important;"  id='new_Degrees_div'>
      </div>
      <div class="row text-right border-bottom" id='message_div'>
        <div class="col-sm-2">
          {{trans('JOBLANG::Employment_Reports.UpToDateForm.messageText')}}
          <abbr title="attribute">({{trans('JOBLANG::Employment_Reports.UpToDateForm.Required')}})</abbr>
        </div>
        <div class="col-sm-10">
        @php
    $field['extra_plugins']=[
                    'a11yhelp',
                    'adobeair','ajax','autocomplete','autoembed','autogrow','autolink','balloonpanel','balloontoolbar',
                    'bidi','clipboard','codesnippet','codesnippetgeshi','colorbutton','colordialog','copyformatting','devtools','dialog',
                    'dialogadvtab','div','divarea','docprops','editorplaceholder','embed','embedbase','embedsemantic',
                    'emoji','exportpdf','find','font','forms','iframe','iframedialog','image','image2','justify','link',
                    'liststyle','magicline','mentions','newpage','pagebreak','panelbutton','pastefromgdocs',
                    'pastefromlibreoffice','pastefromword','pastetools','placeholder','preview','print','scayt','selectall','showblocks','smiley','sourcedialog','specialchar',
                    'stylesheetparser','table','tableresize','tableselection','tabletools','templates','textmatch','textwatcher','uicolor','widget','wsc','xml'
                ];
    $field['extra_plugins'] = isset($field['extra_plugins']) ? implode(',', $field['extra_plugins']) : "embed,widget";
    $defaultOptions = [
        "filebrowserBrowseUrl" => Amerurl('elfinder/ckeditor'),
        "extraPlugins" => $field['extra_plugins'],
        "embed_provider" => "//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}",
        'height'=>400,
        'autoGrow_minHeight'=>400,
        'autoGrow_maxHeight'=>600,
        'language'=>'ar'
    ];
    $field['options'] = array_merge($defaultOptions, $field['options'] ?? []);
@endphp
          <textarea name="editor1" data-init-function="bpFieldInitCKEditorElement" data-options="{{ trim(json_encode($field['options'])) }}" id="editor1" rows="10" cols="200" class="form-control">
        </textarea>
        </div>
      </div>
          <div id='demo'></div>
          <div class="row text-right border-bottom" id='export_div'>
            <div class="col-sm-12">
              <input type="button" name="send" value="{{trans('JOBLANG::Employment_Reports.UpToDateForm.addUserToStatus')}}" id='send' class="btn btn-success btn-sm btn-block">
            </div>
        </div>
      </div>
  </form>
  </div>
  </div>
  </div>
  <!-- updateArea -->
  @push('after_styles')
  <style>
    textarea.cke_source{
      color:var(--bs-emphasis-color);
      background-color:var(--bs-body-bg) !important;
    }
    .cke_dialog_ui_input_textarea, .cke_wysiwyg_frame, .cke_wysiwyg_div{
      color:var(--bs-emphasis-color) !important;
      background-color:var(--bs-body-bg) !important;
    }
  </style>
  @endpush
@push('after_scripts')
@include(fieldview('inc.formFailedScript'))
    @loadScriptOnce('js/packages/ckeditor/ckeditor.js')
    @loadScriptOnce('js/packages/ckeditor/adapters/jquery.js')
    @loadOnce('bpFieldInitCKEditorElement')
    <script>
            
    </script>
    @endLoadOnce
    @endpush