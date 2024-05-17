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
        AMER::setModel(Employment_StaticPages::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_StaticPages');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_StaticPages.singular'), trans('JOBLANG::Employment_StaticPages.plural'));
        /*
        $this->Amer->setTitle(trans('JOBLANG::Employment_StaticPages.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_StaticPages.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_StaticPages.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_StaticPages.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_StaticPages.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_StaticPages.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
        $this->Amer->enableDetailsRow ();
        $this->Amer->allowAccess ('details_row');
        if(amer_user()->can('Employment_StaticPages-add') == 0){$this->Amer->denyAccess('create');}
        if(amer_user()->can('Employment_StaticPages-trash') == 0){$this->Amer->denyAccess ('trash');}
        if(amer_user()->can('Employment_StaticPages-update') == 0){$this->Amer->denyAccess('update');}
        if(amer_user()->can('Employment_StaticPages-delete') == 0){$this->Amer->denyAccess('delete');}
        if(amer_user()->can('Employment_StaticPages-show') == 0){$this->Amer->denyAccess('show');}
        */
    }
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
    protected function setupListOperation(){
        AMER::addColumns([
            [
                'name'=>'Name',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_StaticPages.Name'),
            ],
            [
                'name'=>'Content',
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