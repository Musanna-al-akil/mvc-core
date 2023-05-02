<?php

declare(strict_types=1);

namespace Musanna\MvcCore;

use Musanna\MvcCore\db\Database;

class Application
{
    public static $ROOT_DIR;

    public string $layout = 'main';
    public string $userClass;
    public static Application $app;
    public readonly Router $router;
    public readonly Request $request;
    public readonly Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user ;
    public View $view;
    public ?Controller $controller = null;
    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app      = $this;
        $this->request  = new Request();
        $this->response = new Response();
        $this->session  = new Session();
        $this->view     = new View();
        $this->router   = new Router($this->request,$this->response);

        $this->db       = new Database($config['db']);
        $this->userClass= $config['userClass'];

        $primaryValue   = $this->session->get('user');
        if($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }else {
            $this->user = null;
        } 
    }

    public function run()
    {
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){
            $this->response->setStatusCode($e->getCode());

            echo $this->view->renderView('errors/_error', [
                'exception' => $e
            ]);
        }     
    }

    public function login(?UserModel $user)
    {
        $this->user = $user;

        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};

        $this->session->set('user',$primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }
}