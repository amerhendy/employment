<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Instructions as Employment_Instructions;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_InstructionsRequest as Employment_InstructionsRequest;

class Employment_InstructionsAmerController extends AmerController
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
        AMER::setModel(Employment_Instructions::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Instructions');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Instructions.singular'), trans('JOBLANG::Employment_Instructions.plural'));
        $this->Amer->setTitle(trans('JOBLANG::Employment_Instructions.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Instructions.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Instructions.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Instructions.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Instructions.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Instructions.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
		$this->setPermisssions('Employment_Instructions');
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
                'name'=>'FileName',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Instructions.singular'),
            ],
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'FileName',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_Instructions.singular'),
            ],
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_InstructionsRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_InstructionsRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}