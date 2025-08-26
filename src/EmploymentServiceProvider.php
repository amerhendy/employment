<?php
namespace Amerhendy\Employment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Amerhendy\Employment\App\Helpers\AmerHelper;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanel;
use Amerhendy\Employment\App\Helpers\Library\AmerPanel\AmerPanelFacade;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
class EmploymentServiceProvider extends ServiceProvider{
    use \Amerhendy\Amer\App\Helpers\Library\Database\PublishesMigrations;

    protected $commands = [];
    protected $defer = false;
    public static $pachaPath="Amerhendy\Employment\\";
    public static $config;
    public function register(): void
    {
        require_once __DIR__.'/macro.php';
    }
    public function boot(Router $router): void
    {
        $this->loadConfigs();
        self::$config=Config('Amer.Employment');
        self::$pachaPath=cleanDir(__DIR__);
        $this->loadViewsFrom(cleanDir([self::$pachaPath,'view']), 'Employment');
        $this->loadTranslationsFrom(cleanDir([self::$pachaPath,"Lang"]), 'JOBLANG');
        $this->registerMigrations(cleanDir([self::$pachaPath,"database",'migrations']));
        $this->loadroutes($this->app->router);
        $this->setDisks();
        $this->temperoryurl();
        //$this->addmainmenu();
    }
    public function loadConfigs(){
        foreach(getallfiles(__DIR__.'/config') as $file){
            if(!Str::contains($file, 'config'.DIRECTORY_SEPARATOR."Amer".DIRECTORY_SEPARATOR)){
                $name=Str::afterLast(Str::remove('.php',$file),'config'.DIRECTORY_SEPARATOR);
            }else{
                $name='Amer.'.ucfirst(Str::afterLast(Str::remove('.php',$file),'config'.DIRECTORY_SEPARATOR."Amer".DIRECTORY_SEPARATOR));
            }

            $this->mergeConfigFrom(
                $file,$name
            );
        }
    }
    public function loadroutes(Router $router)
    {
        $routepath=getallfiles(cleanDir([self::$pachaPath,'Route']));
        foreach($routepath as $path){
            if(\Str::contains($path, 'public.php')){
                Route::group([
            'prefix' =>'api/'.config('Amer.Amer.api_version')??'v1',
            'name'=>(config('Amer.Employment.routeName_prefix') ?? 'amer').'Api',
            'namespace'  =>config('Amer.Employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers'),
        ], function () use($path){
                    $this->loadRoutesFrom($path);
                });
            }elseif(!\Str::contains($path, 'api.php')){
                $this->loadRoutesFrom($path);
            }else{
                Route::group($this->apirouteConfiguration(), function () use($path){
                    $this->loadRoutesFrom($path);
                });
            }
        }
    }
    protected function apirouteConfiguration()
    {
        return [
            'prefix' =>'api/'.config('Amer.Amer.api_version')??'v1',
            'middleware' => 'client',
            'name'=>(config('Amer.Employment.routeName_prefix') ?? 'amer').'Api',
            'namespace'  =>config('Amer.Employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers'),
        ];
    }
    public function setDisks(){
        app()->config['filesystems.disks.'.config('Amer.Employment.root_disk_name')] = [
            'driver'=>'local',
            'root' => storage_path('app/Employment'),
            'url' => env('APP_URL').'/storage/Amer/Employment/',
            'visibility' => 'public',
        ];
    }
    public function temperoryurl(){
        Storage::disk(config('Amer.Employment.root_disk_name'))->buildTemporaryUrlsUsing(
            function (string $path, DateTime $expiration, array $options) {
                return URL::temporarySignedRoute(
                    'local.temp',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            }
        );
    }
    public function addmainmenu(){
        $sidelayout_path=resource_path('views/vendor/Amer/Base/inc/menu/mainmenu.blade.php');
        $file_lines=File::lines($sidelayout_path);
        if(!$this->getLastLineNumberThatContains("Route('Mosama.index')",$file_lines->toArray())){
            $newlines=[];
            $newlines[]="@if(Auth::guard('Employers'))";
            $newlines[]="<!-- {{Route('Mosama.index')}} --><li class=\"nav-item\"><a href=\"{{Route('Mosama.index')}}\" class=\"rounded nav-link\"><span class=\"fab fa-servicestack\"></span>{{trans('EMPLANG::Mosama_JobTitles.Mosama_JobTitles')}}</a></li>";
            $newlines[]='@endif';
            $newarr=array_merge($file_lines->toArray(),$newlines);
            $new_file_content = implode(PHP_EOL, $newarr);
            File::put($sidelayout_path,$new_file_content);
        }
    }
    public function getLastLineNumberThatContains($needle, $haystack,$skipcomment=false)
    {
        $matchingLines = array_filter($haystack, function ($k) use ($needle,$skipcomment) {
            if($skipcomment == true){
                if(!Str::startsWith(trim($k),'//')){
                    return strpos($k, $needle) !== false;
                }
            }else{
                    return strpos($k, $needle) !== false;
            }

        });
        if ($matchingLines) {
            return array_key_last($matchingLines);
        }

        return false;
    }
    function getallfiles($path){
        $files = array_diff(scandir($path), array('.', '..'));
        $out=[];
        foreach($files as $a=>$b){
            if(is_dir($path."/".$b)){
                $out=array_merge($out,getallfiles($path."/".$b));
            }else{
                $ab=Str::after($path,'/vendor');
                $ab=Str::replace('//','/',$ab);
                $ab=Str::finish($ab,'/');
                $out[]=$ab.$b;
            }
        }
        return $out;
    }
}
