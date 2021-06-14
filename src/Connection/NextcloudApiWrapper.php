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
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;
use NextcloudApiWrapper\Wrapper;

class NextcloudApiWrapper //extends Connection
	implements ConnectionInterface
{
/*
   // protected $connection;
    protected $wrapper;
	protected $KeychainRegistry;
	
    public function __construct(Wrapper $NextcloudApiWrapper, KeychainRegistry $KeychainRegistry){
	  $this->wrapper=$NextcloudApiWrapper;
	  $this->setKeychainRegistry($KeychainRegistry);
	}
	
	
    public function __get($property)
    {
		$name = strtolower($property);
		
        switch($name){
			case 'client' :
			case 'connection' :
				 return $this->getWrapper()->getConnection();
				break;
			case 'users' :
				 return $this->getWrapper()->getUsersClient();
				break;
			case 'shares' :
				 return $this->getWrapper()->getSharesClient();
				break;
			case 'groups' :
				 return $this->getWrapper()->getGroupsClient();
				break;
			case 'cloudshares' :
				 return $this->getWrapper()->getFederatedCloudSharesClient();
				break;
			case 'apps' :
				return  $this->getWrapper()->getAppsClient();
				break;
			default:
				 throw new \Exception(sprintf('Undefined member of %s: "%s"', $this->getName(), $property));
				break;
		}
		
    }	
	
	
 
    public function getWrapper()
	{
		return $this->wrapper;
	}	
	*/
    public function setKeychainRegistry(KeychainRegistry $KeychainRegistry){
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
		return strtolower($this->getName()).'.'.$this->getId().'.'.$prefix.sha1(json_encode($hash)).$suffix;
	}

    public function getKeychainRegistry():KeychainRegistry
	{
		return $this->KeychainRegistry;
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
		 }elseif($this->getKeychainRegistry()->has($passwordKey) ){
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
