<?php


namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;
use PleskX\Api\Client;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistryWrapper;
use Fusio\Engine\ConnectorInterface;
use Joomla\Keychain\Keychain;

class PleskApiClient /*extends Connection */implements ConnectionInterface
{

    protected $connector;
    protected $KeychainRegistry;
		
   public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }/* */
    public function getName() : string
    {
        return 'PleskApiClient';
    }

    public function getRegKey($prefix='', $suffix='', $configuration){
		$hash = [    
			 'host' => $configuration->get('plesk.client.host'),   			
			 'username' => $configuration->get('plesk.client.login'),  			 
	
		 ];
		ksort($hash);
		return strtolower($this->getName()).'.000'.$prefix.sha1(json_encode($hash)).$suffix;
	}

    public function getKeychainRegistry():KeychainRegistryWrapper
	{
		return $this->KeychainRegistry;
	}
	
    protected function setKeychainRegistry(KeychainRegistryWrapper $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \PleskX\Api\Client
     */
    public function getConnection(ParametersInterface $configuration) : mixed
    { 
      //  $this->configuration=$configuration;
        $this->setKeychainRegistry($this->connector->getConnection($configuration->get('KeychainRegistry')));
		         
		
		$passwordKey = $this->getRegKey( 'plesk.client.','.password', $configuration);
		 $password_config = $configuration->get('plesk.client.password');
		 if(//!$this->KeychainRegistry->has($passwordKey) && 
			is_string($password_config) && !empty($password_config) && '' !== $password_config){
			 $this->KeychainRegistry->set($passwordKey, $password_config);
			 $configuration->set('plesk.client.password', null);
		     $password=$password_config;
		 }elseif($this->KeychainRegistry->exists($passwordKey) ){
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
		 }elseif($this->KeychainRegistry->exists($secretkeyKey) ){
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

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) : void
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
												  'The Keychain used to store PleskApiClient credentials',
													 null));
   
    }
}
