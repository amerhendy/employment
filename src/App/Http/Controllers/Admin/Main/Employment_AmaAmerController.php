<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Ama as Employment_Ama;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_AmaRequest as Employment_AmaRequest;

class Employment_AmaAmerController extends AmerController
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
        AMER::setModel(Employment_Ama::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Ama');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Ama.singular'), trans('JOBLANG::Employment_Ama.plural'));
        /*
        $this->Amer->setTitle(trans('JOBLANG::Employment_Ama.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Ama.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Ama.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Ama.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Ama.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Ama.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        if(amer_user()->can('Employment_Ama-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Employment_Ama-trash') == 0){$this->Amer->denyAccess ('trash');}
        if(amer_user()->can('Employment_Ama-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Employment_Ama-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Employment_Ama-show') == 0){$this->Amer->denyAccess('show');}
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
                'label'=>trans('JOBLANG::Employment_Ama.singular'),
            ]
        ]);
    }
    function fields(){
        AMER::addField([
            'name'=>'Text',
            'type'=>'Text',
            'label'=>trans('JOBLANG::Employment_Ama.singular'),
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_AmaRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_AmaRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}