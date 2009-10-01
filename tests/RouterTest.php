<?php

namespace tests;
use \library\Router;

class RouterTest extends \PHPUnit_Framework_Testcase
{
    public function testSingleton()
    {
        $router1 = Router::getInstance();
        $router2 = Router::getInstance();
        
        $this->assertEquals($router1, $router2);
    }

    public function testConnect()
    {
        $router = Router::getInstance();
        $router->connect('/bla/blub/', 'bla', 'test');
        $router->connect('/bla/blub', 'bla', 'test');
        $router->connect('bla/blub/', 'bla', 'test');
        
        $this->assertEquals(count($router->getRoutes()), 1);

        $router->connect('bla/bla/', 'bla', 'bla');
        $this->assertEquals(count($router->getRoutes()), 2);
    }

    public function testMatch()
    {       
        $router = Router::getInstance();
        $this->assertEquals($router->match('/'), array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $this->assertEquals($router->match('index'), array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $this->assertEquals($router->match('index/'), array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $this->assertEquals($router->match('index/index'), array(
            'controller' => 'index',
            'action' => 'index'
        ));
        $this->assertEquals($router->match('index/bla'), array(
            'controller' => 'index',
            'action' => 'bla'
        ));
        $this->assertEquals($router->match('/bla'), array(
            'controller' => 'bla',
            'action' => 'index'
        ));
        $this->assertEquals($router->match('/bla/blub'), array(
            'controller' => 'bla',
            'action' => 'test'
        ));
        $this->assertEquals($router->match('bla/blub'), array(
            'controller' => 'bla',
            'action' => 'test'
        ));
        $this->assertEquals($router->match('bla/blub/'), array(
            'controller' => 'bla',
            'action' => 'test'
        ));
    }

    public function testGetUrlFor()
    {
        $router = Router::getInstance();
        $this->assertEquals($router->getUrlFor(), '/test/url/index/index');
        $this->assertEquals($router->getUrlFor('index'), '/test/url/index/index');
        $this->assertEquals($router->getUrlFor('index', 'index'), '/test/url/index/index');
        $this->assertEquals($router->getUrlFor('index', 'test'), '/test/url/index/test');
        $this->assertEquals($router->getUrlFor('bla', 'blub'), '/test/url/bla/blub');
        $this->assertEquals($router->getUrlFor('bla', 'test'), '/test/url/bla/blub');
        $this->assertEquals($router->getUrlFor('bla'), '/test/url/bla/index');
    }

    protected function setup()
    {
        $router = Router::getInstance();
        $router->connect('bla/blub', 'bla', 'test');
    }
}
