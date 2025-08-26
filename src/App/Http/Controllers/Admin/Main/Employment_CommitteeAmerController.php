<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use \Amerhendy\Employment\App\Models\Employment_Committee as Employment_Committee;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Http\Requests\Employment_CommitteeRequest as Employment_CommitteeRequest;

class Employment_CommitteeAmerController extends AmerController
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
        AMER::setModel(Employment_Committee::class);
        AMER::setRoute(config('Amer.employment.route_prefix') . '/Employment_Committee');
        AMER::setEntityNameStrings(trans('JOBLANG::Employment_Committee.singular'), trans('JOBLANG::Employment_Committee.plural'));
        $this->Amer->setTitle(trans('JOBLANG::Employment_Committee.create'), 'create');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Committee.create'), 'create');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Committee.create'), 'create');
        $this->Amer->setTitle(trans('JOBLANG::Employment_Committee.edit'), 'edit');
        $this->Amer->setHeading(trans('JOBLANG::Employment_Committee.edit'), 'edit');
        $this->Amer->setSubheading(trans('JOBLANG::Employment_Committee.edit'), 'edit');
        $this->Amer->addClause('where', 'deleted_at', '=', null);
		$this->setPermisssions('Employment_Committee');
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
                'label' => trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces'),
                'type'=>'select',
                'name' => 'annonce_id',
                'entity' => 'Employment_StartAnnonces',
                'attribute' => ['Number','Year'],
                //'attribute' => 'Number',
                'array_view'=>[
                    'translate'=>"اعلان رقم ? لسنة ?",
                    //'divider'=>':::',
                ],
            ],
            [
                'type'=>'number',
                'name'=>'Number',
                'label'=>trans('JOBLANG::Employment_Committee.Number')
            ],
            [
                'type'=>'text',
                'name'=>'Name',
                'label'=>trans('JOBLANG::Employment_Committee.Name')
            ],
            [
                'type'=>'datetime',
                'name'=>'Committee_Date',
                'label'=>trans('JOBLANG::Employment_Committee.Date')
            ]
        ]);
    }
    function fields(){
        AMER::addFields([
            [
                'label' => trans('JOBLANG::Employment_StartAnnonces.Employment_StartAnnonces'),
                'type'=>'select2',
                'name' => 'annonce_id',
                'entity' => 'Employment_StartAnnonces',
                'attribute' => ['Number','Year'],
                //'attribute' => 'Number',
                'array_view'=>[
                    'translate'=>"اعلان رقم ? لسنة ?",
                    //'divider'=>':::',
                ],
            ],
            [
                'type'=>'number',
                'name'=>'Number',
                'label'=>trans('JOBLANG::Employment_Committee.Number')
            ],
            [
                'type'=>'text',
                'name'=>'name',
                'label'=>trans('JOBLANG::Employment_Committee.Name')
            ],
            [
                'type'=>'text',
                'name'=>'type',
                'label'=>trans('JOBLANG::Employment_Committee.type')."حولها اختييار"
            ],
            [
                'type'=>'datetime',
                'name'=>'Committee_date',
                'label'=>trans('JOBLANG::Employment_Committee.Date')
            ],
            [
                'type'=>'table',
                'name'=>'Committee_Memebers',
                'default'=>[
                    [
                        'Positions'=>'',
                    'Name'=>'',
                    'Job'=>'','Sign'=>''
                    ]
                ]
            ]
        ]);
    }
    protected function setupCreateOperation()
    {
        AMER::setValidation(Employment_CommitteeRequest::class);
        $this->fields();
    }
    protected function setupUpdateOperation()
    {
        AMER::setValidation(Employment_CommitteeRequest::class);
        $this->fields();
    }
    public function destroy($id)
    {
        $this->Amer->hasAccessOrFail('delete');
        $data=$this->Amer->model::remove_force($id);
        return $data;
    }
}
