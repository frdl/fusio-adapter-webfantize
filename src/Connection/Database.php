<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Medoo\Medoo;
use kanduganesh\kdbv;
use Fusio\Adapter\Webfantize\DatabaseConnectionWrapper;
use Fusio\Engine\Model\Connection;

class Database extends Connection implements ConnectionInterface
{

	protected $KeychainRegistry;
    protected $Wrapper;

	
    public function getName()
    {
        return 'Database';
    }

    public function getRegKey( $prefix='', $suffix='', $configuration){<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Medoo\Medoo;
use kanduganesh\kdbv;
use Fusio\Adapter\Webfantize\Connection\DatabaseConnectionWrapper as ConnectionWrapper;
use Fusio\Engine\Model\Connection;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;

class Database extends Connection implements ConnectionInterface
{

	protected $KeychainRegistry;
    protected $Wrapper;

	
    public function getName()
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
		return strtolower($this->getName()).'.'.$this->getId().'.'.$prefix.sha1(json_encode($hash)).$suffix;
	}
    public function getKeychainRegistry():KeychainRegistry
	{
		return $this->KeychainRegistry;
	}
	
    protected function setKeychainRegistry(KeychainRegistry $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}

    public function getConnection(ParametersInterface $configuration)
    { 
 
        $this->setKeychainRegistry($this->connector->getConnection($configuration->get('KeychainRegistry')));
		$this->Wrapper = new ConnectionWrapper($this, $configuration);
		$handler=  $this->configuration->get('db.default.handler');
        $connection = $this->Wrapper->{$handler};
		return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

		 $builder->add($elementFactory->newSelect('db.default.handler', 'Default Handler', ConnectionWrapper::handlers(), 'Default Handler Type (Intent)'));
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
												  \Fusio\Adapter\Webfantize\Connection\KeychainRegistry::class, 
												  'The Keychain used to store database credentials'));
   
    }
}
		$hash = [    
			 'type' => $configuration->get('db.master.type'),    
			 'host' => $configuration->get('db.master.host'),   			
			 'username' => $configuration->get('db.master.username'),  			 
			 'port' => $configuration->get('db.master.port'),	
		 ];
		ksort($hash);
		return strtolower($this->getName()).'.'.$this->getId().'.'.$prefix.sha1(json_encode($hash)).$suffix;
	}
    public function getKeychainRegistry(){
		return $this->KeychainRegistry;
	}


    public function getConnection(ParametersInterface $configuration)
    { 
 
        $this->KeychainRegistry = $this->connector->getConnection($configuration->get('KeychainRegistry'));
		$this->Wrapper = new DatabaseConnectionWrapper($this, $configuration);
		$handler=  $this->configuration->get('db.default.handler');
        $connection = $this->Wrapper->{$handler};
		return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

		 $builder->add($elementFactory->newSelect('db.default.handler', 'Default Handler', DatabaseConnectionWrapper::handlers(), 'Default Handler Type (Intent)'));
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
												  \Fusio\Adapter\Webfantize\Connection\KeychainRegistry::class, 
												  'The Keychain used to store database credentials'));
   
    }
}
