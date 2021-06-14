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
/*


//The base path to Nextcloud api entry point, dont forget the last '/'
$basePath   = 'http://my.domain.com/nextcloud/ocs/';
$username   = 'admin';
$password   = 'potatoes';

$wrapper    = Wrapper::build($basePath, $username, $password);

// https://docs.nextcloud.com/server/12/admin_manual/configuration_user/user_provisioning_api.html
$userClient                 = $wrapper->getUsersClient();
$groupsClient               = $wrapper->getGroupsClient();
$appsClient                 = $wrapper->getAppsClient();

// https://docs.nextcloud.com/server/12/developer_manual/core/ocs-share-api.html
$sharesClient               = $wrapper->getSharesClient();
$federatedCloudSharesClient = $wrapper->getFederatedCloudSharesClient();

//Instance of \NextcloudApiWrapper\NextcloudResponse
$response   = $userClient->getUsers();
$code       = $response->getStatusCode();   //status code
$users      = $response->getData();         //data as array
$message    = $response->getStatus();       //status message
$guzzle     = $response->getRawResponse();  //Guzzle response
Making your own requests
If you'd like to perform your own requests, you can use the underlying nextcloud connection class to perform them.

$connection = new \NextcloudApiWrapper\Connection($basePath, $username, $password);

//To perform simple requests
$response   = $connection->request('GET', 'cloud/users');

//To perform requests which needs the 'application/x-www-form-urlencoded' header
//and are not performed in POST
$response   = $connection->pushDataRequest('PUT', 'cloud/' . $username . '/disable');

//To perform requests which holds some values to submit
$response   = $connection->submitRequest('POST', 'cloud/users', [
    'userid'    => 'potatoes',
    'password'  => 'tortilla'
]);


*/
class NextcloudApiWrapper extends Connection implements ConnectionInterface
{

   // protected $connection;
    protected $wrapper;
	protected $KeychainRegistry;
	
    protected function __construct(\NextcloudApiWrapper\Wrapper $NextcloudApiWrapper, KeychainRegistry $KeychainRegistry){
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
    public function getWrapper():Wrapper
	{
		return $this->wrapper;
	}	
    protected function setKeychainRegistry(KeychainRegistry $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Fusio\Adapter\Webfantize\Connection\NextcloudApiWrapper
     */
    public function getConnection(ParametersInterface $configuration) : NextcloudApiWrapper
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
		
		
	    return new self($wrapper, $this->getKeychainRegistry());
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
