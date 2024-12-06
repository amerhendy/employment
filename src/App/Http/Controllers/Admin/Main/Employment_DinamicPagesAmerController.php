<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_DinamicPages as Employment_DinamicPages;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_DinamicPagesRequest as Employment_DinamicPagesRequest;
class Employment_DinamicPagesAmerController extends AmerController
{
    private $tr;
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
        $modelName='Employment_DinamicPages';
        $this->tr=trans('JOBLANG::'.$modelName);
        $model=Employment_DinamicPages::class;
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
                'label'=>trans('JOBLANG::Employment_DinamicPages.name'),
            ],
            [
                'name'=>'control',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_DinamicPages.Control'),
            ],
            [
                'name'=>'function',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_DinamicPages.function'),
            ]

        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'text',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_DinamicPages.name'),
            ],
            [
                'name'=>'control',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_DinamicPages.Control'),
            ],
            [
                'name'=>'function',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_DinamicPages.function'),
            ]
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_DinamicPagesRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_DinamicPagesRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}
