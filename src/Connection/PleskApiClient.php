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

class PleskApiClient extends Connection implements ConnectionInterface
{

   // protected $connection;
//    protected $configuration;
	protected $KeychainRegistry;
	
    public function getName()
    {
        return 'PleskApiClient';
    }

    public function getRegKey($prefix='', $suffix='', $configuration){
		$hash = [    
			 'host' => $configuration->get('plesk.client.host'),   			
			 'username' => $configuration->get('plesk.client.login'),  			 
	
		 ];
		ksort($hash);
		return strtolower($this->getName()).'.'.$this->getId().'.'.$prefix.sha1(json_encode($hash)).$suffix;
	}


    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \PleskX\Api\Client
     */
    public function getConnection(ParametersInterface $configuration) : \PleskX\Api\Client
    { 
      //  $this->configuration=$configuration;
        $this->KeychainRegistry = $this->connector->getConnection($configuration->get('KeychainRegistry'));
		         
		
		$passwordKey = $this->getRegKey( 'plesk.client.','.password', $configuration);
		 $password_config = $configuration->get('plesk.client.password');
		 if(//!$this->KeychainRegistry->has($passwordKey) && 
			is_string($password_config) && !empty($password_config) && '' !== $password_config){
			 $this->KeychainRegistry->set($passwordKey, $password_config);
			 $configuration->set('plesk.client.password', null);
		     $password=$password_config;
		 }elseif($this->KeychainRegistry->has($passwordKey) ){
		     $password=$this->KeychainRegistry->get($passwordKey);
		 }else{
			$password=null; 
		 }
		
		
		 $secretkeyKey = $this->getRegKey( 'plesk.client.','.secretkey', $configuration);
		 $secretkey_config =$configuration->get('plesk.client.secretkey');
		 if(//!$this->KeychainRegistry->has($secretkeyKey) && 
			is_string($secretkey_config) 
			&& !empty($secretkey_config)
			&& '' !== $secretkey_config){
			 $this->KeychainRegistry->set($secretkeyKey, $secretkey_config);
			 $configuration->set('plesk.client.secretkey', null);
			  $secretkey=$secretkey_config;
		 }elseif($this->KeychainRegistry->has($secretkeyKey) ){
		     $secretkey=$this->KeychainRegistry->get($secretkeyKey);
		 }else{
			$secretkey=null; 
		 }
		
		$host = $configuration->get('plesk.client.host');
		$connection  = new \PleskX\Api\Client($host);
		
		
		$methodAuth  = $configuration->get('plesk.client.authmethod');
        switch($methodAuth){
			case 'secretkey' :
				  $connection->setSecretKey($secretkey);
				break;
			case 'credentials' :
			default:
			    	$connection->setCredentials($configuration->get('plesk.client.login'), $password);
				break;
				
		}
		return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

	
          $builder->add($elementFactory->newInput('plesk.client.host',
												  'Host', 'text', 'The host of the PleskAPI Server'));
		
		
          $builder->add($elementFactory->newInput('plesk.client.login',
												  'Login','text',  'Login: username of the sysuser/admin[/reseller/client]'));
		
		
          $builder->add($elementFactory->newInput('plesk.client.password',
												  'Password','text',  'The password of the plesk user'));
		
          $builder->add($elementFactory->newInput('plesk.client.secretkey',
												  'Secretkey','text',  'Secretkey to login (optional)'));
		
	      
		  $builder->add($elementFactory->newSelect('plesk.client.authmethod', 'Method',
												  ['credentials', 'secretkey'], 
												   'Prefered authentication method (credentials=username/password  secretkey=secretkey'));
    
		/*
			  $builder->add($elementFactory->newSelect('plesk.client.update', 'Method',
												  ['', 'update'], 
												   'If you want to update the credentials/key (Optional)'));
		*/
   
          $builder->add($elementFactory->newConnection('KeychainRegistry',
												  \Fusio\Adapter\Webfantize\Connection\KeychainRegistry::class, 
												  'The Keychain used to store PleskApiClient credentials'));
   
    }
}
