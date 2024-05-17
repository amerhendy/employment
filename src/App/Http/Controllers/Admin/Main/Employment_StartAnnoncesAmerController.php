<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces as Employment_StartAnnonces;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_StartAnnoncesRequest as Employment_StartAnnoncesRequest;

class Employment_StartAnnoncesAmerController extends AmerController
{
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\ListOperation;
    use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
    //use \Amerhendy\Amer\App\Http\Controllers\Base\Operations\CreateOperation;
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
        AMER::setModel(Employment_StartAnnonces::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_StartAnnonces');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_StartAnnonces.singular'), trans('JOBLANG::Employment_StartAnnonces.plural'));
        /*
        $this->Amer->setTitle(trans('JOBLANG::Employment_StartAnnonces.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_StartAnnonces.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_StartAnnonces.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_StartAnnonces.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_StartAnnonces.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_StartAnnonces.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        if(amer_user()->can('Employment_StartAnnonces-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Employment_StartAnnonces-trash') == 0){$this->Amer->denyAccess ('trash');}
        if(amer_user()->can('Employment_StartAnnonces-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Employment_StartAnnonces-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Employment_StartAnnonces-show') == 0){$this->Amer->denyAccess('show');}
        */
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
    protected function setupListOperation(){
        AMER::addColumns([
            [
                'name'=>'Governorates',
                'type'=>'select',
                'attribute'=>'Name',
                'label'=>trans('AMER::Governorates.singular'),
            ],
            [
                'name'=>'Number',
                'type'=>'Number',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceNumber'),
            ],
            [
                'name'=>'Year',
                'type'=>'year',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceYear'),
                'startyear'=>2022,
                'endyear'=>2024,
            ],
            [
                'name'=>'Description',
                'type'=>'textarea',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.Description'),
            ],
            [
                'name'=>'Stage_id',
                'type'=>'select',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceStage'),
                'model'=>'Amerhendy\Employment\App\Models\Employment_Stages',
                'attribute'=>'Text',
                'entity'=>'Employment_Stages',
            ],
            [
                'name'=>'Employment_Qualifications',
                'type'=>'select',
                'label'=>trans('JOBLANG::Employment_Qualifications.Employment_Qualifications'),
                'model'=>'Amerhendy\Employment\App\Models\Employment_Qualifications',
                'attribute'=>'Text',
            ],
            [
                'name'=>'Status',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.Status'),
                'options'=>[
                    'Draft'=>trans('JOBLANG::Employment_StartAnnonces.StatusDraft'),
                    'Publish'=>trans('JOBLANG::Employment_StartAnnonces.StatusPublished'),
                ]
            ],
            [
                'name'=>'Slug',
                'type'=>'text',
                'attributes'=>['disabled' => 'disabled'],
            ],
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'Governorates',
                'type'=>'select2_multiple',
                'attribute'=>'Name',
                'label'=>trans('AMER::Governorates.singular'),
            ],
            [
                'name'=>'Number',
                'type'=>'Number',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceNumber'),
            ],
            [
                'name'=>'Year',
                'type'=>'year',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceYear'),
                'startyear'=>now()->format('Y')-2,
                'endyear'=>now()->format('Y')+1,
            ],
            [
                'name'=>'Description',
                'type'=>'textarea',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.Description'),
            ],
            [
                'name'=>'Stage_id',
                'type'=>'select2',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.AnnonceStage'),
                'model'=>'Amerhendy\Employment\App\Models\Employment_Stages',
                'attribute'=>'Text',
            ],
            [
                'name'=>'Employment_Qualifications',
                'type'=>'select2_multiple',
                'label'=>trans('JOBLANG::Employment_Qualifications.Employment_Qualifications'),
                'model'=>'Amerhendy\Employment\App\Models\Employment_Qualifications',
                'attribute'=>'Text',
                'select_all'=>true,
                'options'   => (function ($query) {
                    return $query->where('Type', 'Public')->get();
                }),
            ],
            [
                'name'=>'Status',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_StartAnnonces.Status'),
                'options'=>[
                    'Draft'=>trans('JOBLANG::Employment_StartAnnonces.StatusDraft'),
                    'Publish'=>trans('JOBLANG::Employment_StartAnnonces.StatusPublished'),
                ]
            ],
            [
                'name'=>'Slug',
                'type'=>'text',
                'attributes'=>['disabled' => 'disabled'],
            ],
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_StartAnnoncesRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_StartAnnoncesRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}