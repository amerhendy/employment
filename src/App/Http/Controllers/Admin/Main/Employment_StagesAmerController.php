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
    private $tr;
    public function setup()
    {
        $modelName='Employment_Stages';
        $this->tr=trans('JOBLANG::'.$modelName);
        $model=Employment_Stages::class;
        AMER::setModel($model);
        $routePrefix=config('Amer.Employment.route_prefix');
        AMER::setRoute($routePrefix . '/'.$modelName);
        AMER::setEntityNameStrings($this->tr['singular'], $this->tr['plural']);
        $this->Amer->setTitle($this->tr['create'], 'create');
        $this->Amer->setHeading($this->tr['create'], 'create');
        $this->Amer->setSubheading($this->tr['create'], 'create');
        $this->Amer->setTitle($this->tr['edit'], 'edit');
        $this->Amer->setHeading($this->tr['edit'], 'edit');
        $this->Amer->setSubheading($this->tr['edit'], 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->setPermisssions($modelName);
    }
    public function setPermisssions($n){
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
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
                'name'=>'text',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Stages.Name'),
            ],
            [
                'name'=>'days',
                'type'=>'number',
                'label'=>trans('JOBLANG::Employment_Stages.Days'),
            ],
            [
                'name'=>'page',
                'type'=>'model_function',
                'function_name' => 'getSlugWithLink',
                'function_parameters' =>[$this->Amer],
                'label'=>trans('JOBLANG::Employment_Stages.page'),
            ],
            [
                'name'=>'front',
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
                'name'=>'text',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Stages.Name'),
            ],
            [
                'name'=>'days',
                'type'=>'number',
                'label'=>trans('JOBLANG::Employment_Stages.Days'),
            ],
            [
                'name'=>'page',
                'type'=>'select2_from_array',
                'options'=>Employment_Stages::addpages(),
                'allows_null'=>false,
                'label'=>trans('JOBLANG::Employment_Stages.page'),
            ],
            [
                'name'=>'front',
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
