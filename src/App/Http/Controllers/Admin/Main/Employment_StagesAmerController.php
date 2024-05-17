<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Stages as Employment_Stages;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_StagesRequest as Employment_StagesRequest;

class Employment_StagesAmerController extends AmerController
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
        AMER::setModel(Employment_Stages::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Stages');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Stages.singular'), trans('JOBLANG::Employment_Stages.plural'));
        /*
        $this->Amer->setTitle(trans('JOBLANG::Employment_Stages.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Stages.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Stages.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Stages.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Stages.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Stages.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        if(amer_user()->can('Employment_Stages-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Employment_Stages-trash') == 0){$this->Amer->denyAccess ('trash');}
        if(amer_user()->can('Employment_Stages-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Employment_Stages-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Employment_Stages-show') == 0){$this->Amer->denyAccess('show');}
        */
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
    protected function setupListOperation(){
        AMER::addColumns([
            [
                'name'=>'Text',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Stages.Name'),
            ],
            [
                'name'=>'Days',
                'type'=>'number',
                'label'=>trans('JOBLANG::Employment_Stages.Days'),
            ],
            [
                'name'=>'Page',
                'type'=>'model_function',
                'function_name' => 'getSlugWithLink',
                'function_parameters' =>[$this->Amer],
                'label'=>trans('JOBLANG::Employment_Stages.page'),
            ],
            [
                'name'=>'Front',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_Stages.front'),
                'inline'=>true,
                'options'=>[
                    '0'=>trans('JOBLANG::Employment_Stages.front0'),
                    '1'=>trans('JOBLANG::Employment_Stages.front1'),
                ]
            ],
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'Text',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Stages.Name'),
            ],
            [
                'name'=>'Days',
                'type'=>'number',
                'label'=>trans('JOBLANG::Employment_Stages.Days'),
            ],
            [
                'name'=>'Page',
                'type'=>'select2_from_array',
                'options'=>Employment_Stages::addpages(),
                'allows_null'=>false,
                'label'=>trans('JOBLANG::Employment_Stages.page'),
            ],
            [
                'name'=>'Front',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_Stages.front'),
                'inline'=>true,
                'options'=>[
                    '0'=>trans('JOBLANG::Employment_Stages.front0'),
                    '1'=>trans('JOBLANG::Employment_Stages.front1'),
                ]
            ],
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_StagesRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_StagesRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}