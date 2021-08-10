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
use Fusio\Engine\Model\Connection;
use PleskX\Api\Client;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistryWrapper;
use Joomla\Keychain\Keychain;
use NextcloudApiWrapper\Wrapper;
use Doctrine\DBAL;
use Fusio\Engine\ConnectorInterface;
class NextcloudApiWrapper //extends Connection
	implements ConnectionInterface
{
		protected $connector;

    public function setKeychainRegistry(KeychainRegistryWrapper $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}
	
	 
	public function getName()
    {
        return 'NextcloudApiWrapper';
    }

    public function getRegKey($prefix='', $suffix='', $configuration){
		$hash = [    
			 'nextcloud.server' => $configuration->get('nextcloud.client.url'),   			
			 'username' => $configuration->get('nextcloud.client.username'),  			 
	
		 ];
		ksort($hash);
		return strtolower($this->getName()).'.000.'.$prefix.sha1(json_encode($hash)).$suffix;
	}

    public function getKeychainRegistry(): KeychainRegistryWrapper
	{
		return $this->KeychainRegistry;
	}
	
	
	public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
	
	
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \NextcloudApiWrapper\Wrapper
     */
    public function getConnection(ParametersInterface $configuration) 
    { 
      //  $this->configuration=$configuration;
        $this->setKeychainRegistry($this->connector->getConnection($configuration->get('KeychainRegistry')));
		         
		
		$passwordKey = $this->getRegKey( 'nextcloud.client.','.password', $configuration);
		 $password_config = $configuration->get('nextcloud.client.password');
		 if(//!$this->KeychainRegistry->has($passwordKey) && 
			is_string($password_config) && !empty($password_config) && '' !== $password_config){
			 $this->getKeychainRegistry()->set($passwordKey, $password_config);
			 $configuration->set('nextcloud.client', null);
		     $password=$password_config;
		 }elseif($this->getKeychainRegistry()->exists($passwordKey) ){
		     $password=$this->getKeychainRegistry()->get($passwordKey);
		 }else{
			$password=null; 
		 }
		
		
		
		$wrapper    = Wrapper::build($configuration->get('nextcloud.client.url'), 
									 $configuration->get('nextcloud.client.username'), 
									 $password);
		
		return $wrapper;
	  ///  return new self($wrapper, $this->getKeychainRegistry());
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

	
          $builder->add($elementFactory->newInput('nextcloud.client.url',
												  'Nextcloud Url', 'text', 'The BaseUri/Url of the Nextcloud Server'));
		
		
          $builder->add($elementFactory->newInput('nextcloud.client.username',
												  'Username','text',  'Login: username of the Nextcloud User/Admin'));
		
		
          $builder->add($elementFactory->newInput('nextcloud.client.password',
												  'Password','text',  'The password of the Nextcloud user'));
	
   
          $builder->add($elementFactory->newConnection('KeychainRegistry',
												  \Fusio\Adapter\Webfantize\Connection\KeychainRegistry::class, 
												  'The Keychain used to store Nextcloud credentials',
													 null));
   
    }
}
