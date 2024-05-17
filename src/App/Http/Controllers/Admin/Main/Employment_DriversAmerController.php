<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Drivers as Employment_Drivers;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_DriversRequest as Employment_DriversRequest;

class Employment_DriversAmerController extends AmerController
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
        AMER::setModel(Employment_Drivers::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Drivers');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Drivers.singular'), trans('JOBLANG::Employment_Drivers.plural'));
        /*
        $this->Amer->setTitle(trans('JOBLANG::Employment_Drivers.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Drivers.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Drivers.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Drivers.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Drivers.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Drivers.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        if(amer_user()->can('Employment_Drivers-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Employment_Drivers-trash') == 0){$this->Amer->denyAccess ('trash');}
        if(amer_user()->can('Employment_Drivers-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Employment_Drivers-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Employment_Drivers-show') == 0){$this->Amer->denyAccess('show');}
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
                'label'=>trans('JOBLANG::Employment_Drivers.singular'),
            ]
        ]);
    }
    function fields(){
        AMER::addField([
            'name'=>'Text',
            'type'=>'Text',
            'label'=>trans('JOBLANG::Employment_Drivers.singular'),
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_DriversRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_DriversRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}