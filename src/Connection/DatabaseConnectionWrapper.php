<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Medoo\Medoo;
use kanduganesh\kdbv;
use Fusio\Adapter\Webfantize\Connection\Database;

class DatabaseConnectionWrapper
{
    
	//protected $KeychainRegistry;
	protected $Database;
	protected $configuration;
	protected static $_handlers = [
	    'api' => [
			'class'=>            \Medoo\Medoo::class,		
			'params'=>[
							 'db.master.type' => 'type',    
			                 'db.master.host'=> 'host',   
			                 'db.master.database'=>  'database',   
			                 'db.master.username'=>   'username', 
			                 'db.master.password'=>   'password',			 
		                     'db.master.port'=>	 'port',	
			                 'db.master.prefix'=> 'prefix',	
			                 'db.master.charset' => 'charset',	
			                 'db.master.collation' => 'collation',
				],
		],
	    'migration' =>[ 
			'class'=>\kanduganesh\kdbv::class,		
					  'params'=>[
						//	 'db.master.type' => 'type',    
			                 'db.master.host'=> 'HOST',   
			                 'db.master.database'=>  'DATABASE',   
			                 'db.master.username'=>   'USER', 
			                 'db.master.password'=>   'PASS',			 
		                     'db.master.port'=>	 'PORT',	
			                 'db.master.prefix'=> 'PREFIX',	
			           ///      'db.master.charset' => 'charset',	
			           //      'db.master.collation' => 'collation',
						     'db.master.migration'=>'KDBV',
						  
		
							] 
				
	     ],
	];
	protected $connections = [
	
	];
    public function __construct(Database $Database, ParametersInterface $configuration)
    {
		$this->Database=$Database;
        $this->configuration = $configuration;
		//$this->KeychainRegistry=$this->Database->getKeychainRegistry();
    }
    public static function handlers()
    {
        return self::$_handlers;
    }
    public function connect($intent)
    {
			
		if(!isset($this->connections[$intent])){
			$handler= $this->intent($intent);
	
      	$class=$handler['class'];
      	$params=$handler['params'];
	  
         $passwordKey = $this->Database->getRegKey( 'db.user.','.password', $this->configuration);
		 $password_config = $this->configuration->get('db.master.password');
		 if(is_string($password_config) && !empty($password_config) && '' !== $password_config){
			  $this->Database->getKeychainRegistry()->set($passwordKey, $password_config);
			  $this->configuration->set('db.master.password', null);
		 }
		
		$password = $this->Database->getKeychainRegistry()->exists($passwordKey)			
		                 ? $this->Database->getKeychainRegistry()->get($passwordKey) 
			             : $password_config;
		
		$config =[];
		foreach($params as $id=>$key){
			$config[$id]=$this->configuration->get($key);
		}
		$config[$params['db.master.password']]=$password;
		
		$this->connections[$intent] =new $class($config);
			
	  }
        return $this->connections[$intent];
    }

    public function intent($intent){
		
		if(''===$intent || 'default'===$intent){
			if(!empty($this->configuration->get('db.default.handler'))){
				$intent = $this->configuration->get('db.default.handler');
			}
		}
		
		if(!isset(self::$_handlers[$intent])){
			throw new \Exception('Invalid connection intent '.$handler.' in '.__CLASS__);
		}
		/*
		if(!isset($this->connections[$intent])){
			$this->connections[$handler] = $this->connect(self::$_handlers[$handler]);
		}
		
		return $this->connections[$handler];
		*/
		return self::$_handlers[$intent];
	}

}