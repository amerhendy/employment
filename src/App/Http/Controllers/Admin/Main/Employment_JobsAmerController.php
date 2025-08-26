<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Jobs as Employment_Jobs;
use Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_JobsRequest as Employment_JobsRequest;
use Symfony\Component\HttpFoundation\Request;
class Employment_JobsAmerController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    //use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation  {store as traitStore;}
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\UpdateOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\DeleteOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ShowOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\TrashOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CloneOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\BulkCloneOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\BulkDeleteOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\FetchOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\InlineCreateOperation;
    public function setup()
    {
        AMER::setModel(Employment_Jobs::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Jobs');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Jobs.singular'), trans('JOBLANG::Employment_Jobs.plural'));
        $this->Amer->setTitle(trans('JOBLANG::Employment_Jobs.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Jobs.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Jobs.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Jobs.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Jobs.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Jobs.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
		$this->setPermisssions('Employment_Jobs');
    }
    public function setPermisssions($n){
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        $this->Amer->enableBulkActions();
        $accesslist=['update','list', 'show','trash','reorder','delete','create','clone','BulkDelete'];
        foreach ($accesslist as $l) {
            if(amer_user()->canper($n.'-'.$l) === false){$this->Amer->denyAccess($l);}
        }
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
    protected function setupListOperation(){
        AMER::addColumns([
            [
                'label' => trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces'),
                'type'=>'select',
                'name' => 'annonce_id',
                'entity' => 'Employment_StartAnnonces',
                'attribute' => ['Number','Year'],
                //'attribute' => 'Number',
                'array_view'=>[
                    'translate'=>"اعلان رقم ? لسنة ?",
                    //'divider'=>':::',
                ],
            ],
            [
            'label' => trans('EMPLANG::Mosama_Groups.Mosama_Groups'),
            'type'=>'select',
            'name' => 'Group_id',
            'entity' => 'Mosama_Groups',
            'attribute' => 'text',
            'model' => \Amerhendy\Employers\App\Models\Mosama_Groups::class,
            ],
            [
                'label' => trans('EMPLANG::Mosama_JobTitles.singular'),
                'type'=>'select',
                'name' => 'JobTitle_id',
                'model' => \Amerhendy\Employers\App\Models\Mosama_JobTitles::class,
                'entity'=>'Mosama_JobTitles',
                'attribute'=>'text',
            ],
            [
                'label' => trans('EMPLANG::Mosama_JobNames.Mosama_JobNames'),
                'type'=>'select',
                'name' => 'job_id',
                'model' => \Amerhendy\Employers\App\Models\Mosama_JobNames::class,
                'entity'=>'Mosama_JobNames',
                'attribute'=>'text',
            ]
        ]);
    }
    function fields(){

        $routes=$this->Amer->routelist;
        $tab1=trans('JOBLANG::Employment_Jobs.tab1');
        $tab2=trans('JOBLANG::Employment_Jobs.tab2');
        $tab3=trans('JOBLANG::Employment_Jobs.tab3');
        $tab4=trans('JOBLANG::Employment_Jobs.tab4');
        $tab5=trans('JOBLANG::Employment_Jobs.tab5');
        $tab6=trans('JOBLANG::Employment_Jobs.tab6');
        $tab7=trans('JOBLANG::Employment_Jobs.tab7');
        //dd($routes['fetchGovernorates']['as']);
        Amer::addField([
            'tab'=>$tab1,
            'label' => trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces'),
            //'type' => 'Employment::fields.admin.E_J_annonce_Cities',
            'type'=>'select2',
            'name' => 'annonce_id', // the db column for the foreign key
            'entity' => 'Employment_StartAnnonces', // the method that defines the relationship in your Model
            'attribute' => ['Number','Year'], // foreign key attribute that is shown to user
            //'model' => \Amerhendy\Employment\App\Models\Employment_StartAnnonces::class,
            'options'   => (function ($query) {
                 return $query->orderBy('Number', 'ASC')->get();
             }),
             'allows_null'=>false,
        ]);
        Amer::addField([
            'tab'=>$tab1,
            'label' => trans('EMPLANG::Mosama_Groups.Mosama_Groups'),
            //'type' => 'Employment::fields.admin.E_J_annonce_Cities',
            'type'=>'select2',
            'name' => 'group_id', // the db column for the foreign key
            'entity' => 'Mosama_Groups', // the method that defines the relationship in your Model
            'attribute' => 'text', // foreign key attribute that is shown to user
            'model' => \Amerhendy\Employers\App\Models\Mosama_Groups::class,
             'allows_null'=>false,
        ]);
        Amer::addField([
            'tab'=>$tab1,
            'label' => trans('EMPLANG::Mosama_JobTitles.Mosama_JobTitles'),
            'type'=>'select2_from_ajax',
            'name' => 'job_title_id',
            'data_source'=>$routes['fetchMosama_JobTitles']['as'],
            'model' => \Amerhendy\Employers\App\Models\Mosama_JobTitles::class,
            'entity'=>'Mosama_JobTitles',
            'attribute'=>'text',
            'placeholder'=> trans('EMPLANG::Mosama_JobTitles.Mosama_JobTitles'),
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['Group_id'],
            'selectall'=>true
        ]);
        Amer::addField([
            'tab'=>$tab1,
            'label' => trans('EMPLANG::Mosama_JobNames.Mosama_JobNames'),
            'type'=>'select2_from_ajax',
            'name' => 'job_name_id',
            'data_source'=>$routes['fetchMosama_JobNames']['as'],
            //'model' => \Amerhendy\Employers\App\Models\Mosama_JobNames::class,
            'entity'=>'Mosama_JobNames',
            'attribute'=>'text',
            'placeholder'=> trans('EMPLANG::Mosama_JobNames.Mosama_JobNames'),
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['Mosama_JobTitles'],
            'selectall'=>true
        ]);
        Amer::addField([
            'tab'=>$tab1,
            'name'=>'Code',
            'type'=>'text',
            'label'=>trans('JOBLANG::Employment_Jobs.Code'),
        ]);
        Amer::addField([
            'tab'=>$tab1,
            'name'=>'description',
            'type'=>'textarea',
            'label'=>trans('JOBLANG::Employment_Jobs.Description'),
        ]);

        Amer::addField([
            'tab'=>$tab2,
            'name'=>'OrgStru_Sections',
            'type'=>'select2_multiple',
            'entity'=>'OrgStru_Sections',
            'label'=>trans('EMPLANG::OrgStru_Sections.OrgStru_Sections'),
            'attribute'=>'text',
            'select_all'=>true
        ]);
        Amer::addField([
            'tab'=>$tab2,
            'name'=>'OrgStru_Areas',
            'entity'=>'OrgStru_Areas',
            'label'=>trans('EMPLANG::OrgStru_Areas.OrgStru_Areas'),
            'attribute'=>'text',
            'select_all'=>true,
            'placeholder'=> trans('EMPLANG::OrgStru_Areas.OrgStru_Areas'),
            'type'=>'select2_from_ajax_multiple',
            'data_source'=>$routes['fetchOrgStru_Areas']['as'],
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['OrgStru_Sections'],
        ]);
        Amer::addField([
            'tab'=>$tab2,
            'name'=>'OrgStru_Mahatas',
            'entity'=>'OrgStru_Mahatas',
            'label'=>trans('EMPLANG::OrgStru_Mahatas.OrgStru_Mahatas'),
            'attribute'=>'text',
            'select_all'=>true,
            'placeholder'=> trans('EMPLANG::OrgStru_Mahatas.OrgStru_Mahatas'),
            'type'=>'select2_from_ajax_multiple',
            'data_source'=>$routes['fetchOrgStru_Mahatas']['as'],
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['OrgStru_Areas'],
        ]);
        Amer::addField([
            'tab'=>$tab3,
            'name'=>'Employment_Ama',
            'type'=>'select2_multiple',
            'label'=>trans('JOBLANG::Employment_Ama.Employment_Ama'),
            'attribute'=>'Text',
            'select_all'=>true
        ]);
        Amer::addField([
            'tab'=>$tab3,
            'name'=>'Employment_Army',
            'type'=>'select2_multiple',
            'label'=>trans('JOBLANG::Employment_Army.Employment_Army'),
            'attribute'=>'Text',
            'select_all'=>true
        ]);
        Amer::addField([
            'tab'=>$tab3,
            'name'=>'Employment_MaritalStatus',
            'type'=>'select2_multiple',
            'label'=>trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus'),
            'attribute'=>'Text',
            'select_all'=>true
        ]);
        Amer::addField([
            'tab'=>$tab3,
            'name'=>'Employment_Health',
            'type'=>'select2_multiple',
            'label'=>trans('JOBLANG::Employment_Health.Employment_Health'),
            'attribute'=>'Text',
            'select_all'=>true
        ]);
        Amer::addField([
            'tab'=>$tab3,
            'label' => trans('EMPLANG::Mosama_Educations.Mosama_Educations'),
            'type'=>'select2_from_ajax_multiple',
            'name' => 'Mosama_Educations',
            'data_source'=>$routes['fetchMosama_Educations']['as'],
            'model' => \Amerhendy\Employers\App\Models\Mosama_Educations::class,
            'attribute'=>'text',
            'placeholder'=> trans('EMPLANG::Mosama_Educations.Mosama_Educations'),
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['Mosama_JobTitles'],
            'selectall'=>true
        ]);
        Amer::addField([
            'tab'=>$tab4,
            'name'=>'Employment_Qualifications',
            'type'=>'select2_multiple',
            'entity'=>'Employment_Qualifications',
            'label'=>trans('JOBLANG::Employment_Qualifications.Employment_Qualifications'),
            'attribute'=>'Text',
            'options'   => (function ($query) {
                return $query->where('Type', 'Private')->get();
            }),
            'select_all'=>true,
        ]);
        Amer::addField([
            'tab'=>$tab4,
            'name'=>'Employment_IncludedFiles',
            'type'=>'select2_multiple',
            'entity'=>'Employment_IncludedFiles',
            'label'=>trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles'),
            'attribute'=>'FileName',
            'select_all'=>true,
        ]);
        Amer::addField([
            'tab'=>$tab4,
            'name'=>'Employment_Instructions',
            'type'=>'select2_multiple',
            'entity'=>'Employment_Instructions',
            'label'=>trans('JOBLANG::Employment_Instructions.Employment_Instructions'),
            'attribute'=>'Text',
            'select_all'=>true
        ]);

        Amer::addField([
            'tab'=>$tab5,
            'label' => trans('JOBLANG::Employment_Jobs.CityBornLive'),
            'type'=>'select2_from_ajax_multiple',
            'name' => 'Cities',
            'data_source'=>$routes['fetchGovernorates']['as'],
            'entity'=>'Cities',
            'attribute'=>'Name',
            'hint'=> trans('JOBLANG::Employment_Jobs.CityBornLivehint'),
            'placeholder'=> trans('AMER::Cities.Cities'),
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['annonce_id'],
            'selectall'=>true
        ]);
        Amer::addField([
            'tab'=>$tab5,
            'type'=>'number',
            'name'=>'Count',
            'label'=>trans('JOBLANG::Employment_Jobs.Count')
        ]);

        Amer::addField([
            'tab'=>$tab5,
            'type'=>'number',
            'name'=>'Age',
            'label'=>trans('JOBLANG::Employment_Jobs.Age')
        ]);
        Amer::addField([
            'tab'=>$tab5,
            'type'=>'date_picker',
            'name'=>'AgeIn',
            'label'=>trans('JOBLANG::Employment_Jobs.AgeIn')
        ]);
        Amer::addField([
            'tab'=>$tab6,
            'type'=>'radio',
            'name'=>'Driver',
            'label'=>trans('JOBLANG::Employment_Jobs.Driver'),
            'options'=>[
                0=>trans('JOBLANG::Employment_Jobs.Driver0'),
                1=>trans('JOBLANG::Employment_Jobs.Driver1'),
            ],
        ]);
        Amer::addField([
            'tab'=>$tab6,
            'label' => trans('JOBLANG::Employment_Drivers.Employment_Drivers'),
            'type'=>'select2_from_ajax_multiple',
            'name' => 'Employment_Drivers',
            'data_source'=>$routes['fetchEmployment_Drivers']['as'],
            'model' => \Amerhendy\Employment\App\Models\Employment_Drivers::class,
            'attribute'=>'Text',
            'placeholder'=> trans('EMPLANG::Employment_Drivers.Employment_Drivers'),
            'include_all_form_fields' => true,
            'minimum_input_length'    => 0,
            'dependencies'            => ['Driver'],
            'selectall'=>true
        ]);
        Amer::addField([
            'tab'=>$tab7,
            'label' => trans('JOBLANG::Employment_Jobs.Status'),
            'type'=>'radio',
            'name' => 'Status',
            'options'=>[
                'Publish'=>trans('JOBLANG::Employment_Jobs.StatusPublish'),
                'Draft'=>trans('JOBLANG::Employment_Jobs.StatusDraft'),
            ]
        ]);
        Amer::addField([
            'tab'=>$tab7,
            'type'=>'hidden',
            'name' => 'Slug',
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setRequiredFields(Employment_JobsRequest::class);
        AMER::setValidation(Employment_JobsRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_JobsRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
    public function fetchGovernorates(){
        $get=$_GET;
        $form=$get['form'];
        $annonce_id=\Arr::where($form,function($v,$K){
            return $v['name']=='annonce_id';
        });
        $annonce_id=$annonce_id[array_keys($annonce_id)[0]]['value'];
        $gov=\Amerhendy\Amer\App\Models\Governorates::whereHas('Employment_StartAnnonces',function($query)use($annonce_id){
            return $query->where('annonce_id',$annonce_id);
        })->get()->toArray();
        $govids=[];
        if(count($gov)){
            foreach($gov as $a=>$b){
                $govids[]=$b['id'];
            }
        }
        return $this->fetch([
            'model' =>\Amerhendy\Amer\App\Models\Cities::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($govids) {
                return $model->whereHas('Governorates',function($q)use($govids){
                    return $q->whereIn('Governorates.id',$govids);
                });
            }
        ]);
    }
    public function fetchMosama_JobTitles()
    {
        $get=$_GET;
        $form=$get['form'];
        $Mosama_Groups=\Arr::where($form,function($v,$K){
            return $v['name']=='Group_id';
        });
        if(count($Mosama_Groups)){
            $Mosama_Groups=$Mosama_Groups[array_keys($Mosama_Groups)[0]]['value'];
        }
        return $this->fetch([
            'model' =>\Amerhendy\Employers\App\Models\Mosama_JobTitles::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($Mosama_Groups) {
                return $model->whereHas('Mosama_Groups',function($q)use($Mosama_Groups){
                    return $q->where('Mosama_Groups.id',$Mosama_Groups);
                });
            }
        ]);
    }
    public function fetchMosama_JobNames()
    {
        $get=$_GET;
        $form=$get['form'];
        $Mosama_JobTitles=\Arr::where($form,function($v,$K){
            return $v['name']=='JobTitle_id';
        });
        if(count($Mosama_JobTitles)){$Mosama_JobTitles=$Mosama_JobTitles[array_keys($Mosama_JobTitles)[0]]['value'];}
        return $this->fetch([
            'model' =>\Amerhendy\Employers\App\Models\Mosama_JobNames::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($Mosama_JobTitles) {
                return $model->whereHas('Mosama_JobTitles',function($q)use($Mosama_JobTitles){
                    return $q->where('Mosama_JobTitles.id',$Mosama_JobTitles);
                });
            }
        ]);
    }
    public function fetchMosama_Educations()
    {
        $get=$_GET;
        $form=$get['form'];
        $Mosama_JobTitles=\Arr::where($form,function($v,$K){
            return $v['name']=='JobTitle_id';
        });
        //dd($Mosama_JobTitles,$form);
        if(count($Mosama_JobTitles))
        {
            $Mosama_JobTitles=$Mosama_JobTitles[array_keys($Mosama_JobTitles)[0]]['value'];
        }
        return $this->fetch([
            'model' =>\Amerhendy\Employers\App\Models\Mosama_Educations::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($Mosama_JobTitles) {
                return $model->whereHas('Mosama_JobTitles',function($q)use($Mosama_JobTitles){
                    return $q->where('Mosama_JobTitles.id',$Mosama_JobTitles);
                });
            }
        ]);
    }
    public function fetchEmployment_Drivers()
    {
        $get=$_GET;
        $form=$get['form'];
        $Driver=\Arr::where($form,function($v,$K){
            return $v['name']=='Driver';
        });
        //dd($Mosama_JobTitles,$form);
        if(count($Driver))
        {
            $Driver=$Driver[array_keys($Driver)[0]]['value'];
        }
        if($Driver == 1){
            return [];
        }
        return $this->fetch([
            'model' =>\Amerhendy\Employment\App\Models\Employment_Drivers::class,
            'searchable_attributes' => 'Text',
        ]);
    }
    function fetchOrgStru_Areas(){
        $get=$_GET;
        $form=$get['form'];
        $OrgStru_Sections=\Arr::where($form,function($v,$K){
            return $v['name']=='OrgStru_Sections[]';
        });
        if(!count($OrgStru_Sections)){return [];}
        $OrgStru_Sections=(\Arr::pluck($OrgStru_Sections,'value'));
        return $this->fetch([
            'model' =>\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Areas::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($OrgStru_Sections) {
                return $model->whereHas('OrgStru_Sections',function($q)use($OrgStru_Sections){
                    return $q->whereIn('OrgStru_Sections.id',$OrgStru_Sections);
                });
            }
        ]);
    }
    function fetchOrgStru_Mahatas(){
        $get=$_GET;
        $form=$get['form'];
        $OrgStru_Areas=\Arr::where($form,function($v,$K){
            return $v['name']=='OrgStru_Areas[]';
        });
        if(!count($OrgStru_Areas)){return [];}
        $OrgStru_Areas=(\Arr::pluck($OrgStru_Areas,'value'));
        return $this->fetch([
            'model' =>\Amerhendy\Employers\App\Models\OrgStru\OrgStru_Mahatas::class,
            'searchable_attributes' => 'Name',
            'paginate' => 10,
            'query' => function($model)use($OrgStru_Areas) {
                return $model->whereHas('OrgStru_Areas',function($q)use($OrgStru_Areas){
                    return $q->whereIn('OrgStru_Areas.id',$OrgStru_Areas);
                });
            }
        ]);
    }
}
