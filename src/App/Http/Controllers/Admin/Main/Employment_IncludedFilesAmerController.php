<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_IncludedFiles as Employment_IncludedFiles;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_IncludedFilesRequest as Employment_IncludedFilesRequest;

class Employment_IncludedFilesAmerController extends AmerController
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
        AMER::setModel(Employment_IncludedFiles::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_IncludedFiles');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_IncludedFiles.singular'), trans('JOBLANG::Employment_IncludedFiles.plural'));
        $this->Amer->setTitle(trans('JOBLANG::Employment_IncludedFiles.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_IncludedFiles.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_IncludedFiles.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_IncludedFiles.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_IncludedFiles.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_IncludedFiles.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
		$this->setPermisssions('Employment_IncludedFiles');
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
                'label'=>trans('JOBLANG::Employment_IncludedFiles.singular'),
            ],
            [
                'name'=>'checked',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_IncludedFiles.checked'),
                'options'=>[
                    '0'=>trans('JOBLANG::Employment_IncludedFiles.checked0'),
                    '1'=>trans('JOBLANG::Employment_IncludedFiles.checked1'),
                    '2'=>trans('JOBLANG::Employment_IncludedFiles.checked2'),
                ]
            ]
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'name'=>'FileName',
                'type'=>'text',
                'label'=>trans('JOBLANG::Employment_IncludedFiles.singular'),
            ],
            [
                'name'=>'checked',
                'type'=>'radio',
                'label'=>trans('JOBLANG::Employment_IncludedFiles.checked'),
                'options'=>[
                    'mandatory'=>trans('JOBLANG::Employment_IncludedFiles.mandatory'),
                    'Non-binding'=>trans('JOBLANG::Employment_IncludedFiles.Non-binding'),
                    'According_to_the_job'=>trans('JOBLANG::Employment_IncludedFiles.According_to_the_job'),
                ]
            ]
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_IncludedFilesRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_IncludedFilesRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}
