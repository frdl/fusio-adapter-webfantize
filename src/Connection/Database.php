<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Medoo\Medoo;
use kanduganesh\kdbv;
use Fusio\Adapter\Webfantize\Connection\DatabaseConnectionWrapper as ConnectionWrapper;
use Fusio\Engine\Model\Connection;
use Fusio\Engine\ConnectorInterface;
use Joomla\Keychain\Keychain;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistryWrapper;

class Database /* extends Connection */ implements ConnectionInterface
{

	protected $KeychainRegistry;
    protected $Wrapper;

     protected $connector;
	
    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
		 
    public function getName() : string
    {
        return 'Database';
    }

    public function getRegKey( $prefix='', $suffix='', $configuration){
		$hash = [    
			 'type' => $configuration->get('db.master.type'),    
			 'host' => $configuration->get('db.master.host'),   			
			 'username' => $configuration->get('db.master.username'),  			 
			 'port' => $configuration->get('db.master.port'),	
		 ];
		ksort($hash);
		return strtolower($this->getName()).'.000.'.$prefix.sha1(json_encode($hash)).$suffix;
	}
    public function getKeychainRegistry():KeychainRegistryWrapper
	{
		return $this->KeychainRegistry;
	}
	
    protected function setKeychainRegistry(KeychainRegistryWrapper $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}

    public function getConnection(ParametersInterface $configuration) :mixed
    { 
 
        $this->setKeychainRegistry($this->connector->getConnection($configuration->get('KeychainRegistry')));
		$this->Wrapper = new ConnectionWrapper($this, $configuration);
		//$handler=  $configuration->get('db.default.handler');
       // $connection = $this->Wrapper->{$handler};
		return $this->Wrapper;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) : void
    {

		 $builder->add($elementFactory->newSelect('db.default.handler', 'Default Handler', array_keys(ConnectionWrapper::handlers()), 'Default Handler Type (Intent)'));
		 $builder->add($elementFactory->newSelect('db.master.type', 'Type', \PDO::getAvailableDrivers(), 'PDO Driver type'));
    
		
          $builder->add($elementFactory->newInput('db.master.host',
												  'Host', 'text', 'The host of the database'));
          $builder->add($elementFactory->newInput('db.master.database',
												  'Database','text',  'Database name/or sqlite: my/file/path/database.db or :memory:'));
		
		
          $builder->add($elementFactory->newInput('db.master.migration',
												  'Migration Database','text',  'Source (file) for upgrade or make schema export'));
		
          $builder->add($elementFactory->newInput('db.master.charset',
												  'Charset', 'text', 'The charset of the database', 'utf8mb4'));
          $builder->add($elementFactory->newInput('db.master.collation',
												  'Collation','text',  'The collation of the database',  'utf8mb4_general_ci'));
		
		
		// PDO::getAvailableDrivers()
          $builder->add($elementFactory->newInput('db.master.port',
												  'Port', 'text', 'The port of the database', 3306));
          $builder->add($elementFactory->newInput('db.master.username',
												  'Username', 'text', 'The username of the database'));
          $builder->add($elementFactory->newInput('db.master.password',
												  'Password','text',  'The password of the database'));
          $builder->add($elementFactory->newInput('db.master.prefix',
												  'Prefix', 'text', 'The prefix of the tables'));
		
   

          $builder->add($elementFactory->newConnection('KeychainRegistry',
												  KeychainRegistry::class, 
												  'The Keychain used to store database credentials',
												//	  array_keys($this->connector->getAll())
													  null
													  ));
   
    }
}
