<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_StaticPages as Employment_StaticPages;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_StaticPagesRequest as Employment_StaticPagesRequest;

class Employment_StaticPagesAmerController extends AmerController
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
        $modelName='Employment_StaticPages';
        $this->tr=trans('JOBLANG::'.$modelName);
        $model=Employment_StaticPages::class;
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
                'name'=>'name',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_StaticPages.Name'),
            ],
            [
                'name'=>'content',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_StaticPages.Content'),
            ],
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'Name',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_StaticPages.Name'),
            ],
            [
                'name'=>'Content',
                'type'=>'wysiwyg',
                'label'=>trans('JOBLANG::Employment_StaticPages.Content'),
            ],
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_StaticPagesRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_StaticPagesRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}
