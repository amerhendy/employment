<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_LagnaPersons as Employment_LagnaPersons;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_LagnaPersonsRequest as Employment_LagnaPersonsRequest;

class Employment_LagnaPersonsAmerController extends AmerController
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
        AMER::setModel(Employment_LagnaPersons::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_LagnaPersons');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_LagnaPersons.singular'), trans('JOBLANG::Employment_LagnaPersons.plural'));
        $this->Amer->setTitle(trans('JOBLANG::Employment_LagnaPersons.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_LagnaPersons.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_LagnaPersons.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_LagnaPersons.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_LagnaPersons.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_LagnaPersons.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
		$this->setPermisssions('Employment_LagnaPersons');
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
                'name'=>'Position',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Position'),
                'inline'=>true,
                'options'=>[
                    1=>trans('JOBLANG::Employment_LagnaPersons.Position1'),
                    2=>trans('JOBLANG::Employment_LagnaPersons.Position2'),
                ]
            ],
            [
                'name'=>'Name',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Name'),
            ],
            [
                'name'=>'Mon',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Mon'),
            ],
            [
                'name'=>'Signs',
                'type'=>'summernote',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Signs'),
            ],
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'Position',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Position'),
                'inline'=>true,
                'options'=>[
                    1=>trans('JOBLANG::Employment_LagnaPersons.Position1'),
                    2=>trans('JOBLANG::Employment_LagnaPersons.Position2'),
                ]
            ],
            [
                'name'=>'Name',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Name'),
            ],
            [
                'name'=>'Mon',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Mon'),
            ],
            [
                'name'=>'Signs',
                'type'=>'summernote',
                'label'=>trans('JOBLANG::Employment_LagnaPersons.Signs'),
            ],
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_LagnaPersonsRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_LagnaPersonsRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}