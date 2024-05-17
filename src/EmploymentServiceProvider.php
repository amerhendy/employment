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
    protected $defer = false;
    public $pachaPath="Amerhendy\Employment\\";
    public function register(): void
    {
        require_once __DIR__.'/macro.php';
    }
    public function boot(Router $router): void
    {
        $path=base_path('vendor/AmerHendy/Employment/src/');
        $this->loadConfigs();
        $this->registerMigrations($path.'database/migrations');
        $this->loadroutes($this->app->router);
        $this->loadTranslationsFrom(__DIR__.'/Lang','JOBLANG');
        $this->loadViewsFrom($path.'/view', 'Employment');
        //$this->addmainmenu();
        $this->publishFiles();
        $this->disk();
        $this->temperoryurl();
    }
    public function disk(){
        app()->config['filesystems.disks.'.config('Amer.employment.root_disk_name')] = [
            'driver'=>'local',
            'root' => storage_path('app/Employment'),
            'url' => env('APP_URL').'/storage/Amer/Employment/',
            'visibility' => 'public',
        ];
    }
    public function temperoryurl(){
        Storage::disk(config('Amer.employment.root_disk_name'))->buildTemporaryUrlsUsing(
            function (string $path, DateTime $expiration, array $options) {
                return URL::temporarySignedRoute(
                    'local.temp',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            }
        );
    }
    public function loadConfigs(){
        foreach(getallfiles(__DIR__.'/config/Amer') as $file){
            $this->mergeConfigFrom($file,Str::replace('/','.',Str::afterLast(Str::remove('.php',$file),'config/')));
        }
    }
    public function loadroutes(Router $router)
    {
        $packagepath=base_path('vendor/AmerHendy/Employment/src/');
        $routepath=$this->getallfiles($packagepath.'/Route/');
        foreach($routepath as $path){
            //$this->loadRoutesFrom($path);
        }
        $this->loadRoutesFrom($packagepath.'/Route/route.php');
        Route::group($this->apirouteConfiguration(), function () use($packagepath){
            $this->loadRoutesFrom($packagepath.'/Route/api.php');
        });
    }
    protected function apirouteConfiguration()
    {
        return [
            'prefix' => 'api/v1',
            'middleware' => 'client',
            'name'=>config('Amer.employment.routeName_prefix','EmploymentApi'),
            'namespace'  =>config('Amer.employment.Controllers','\\Amerhendy\Employment\App\Http\Controllers\\'),
        ];
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
    function publishFiles()  {
        $pb=config('Amer.employment.package_path') ?? __DIR__;
        $config_files = [$pb.'/config/Amer' => config_path('Amer')];
        $this->publishes($config_files, 'employment:config');
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