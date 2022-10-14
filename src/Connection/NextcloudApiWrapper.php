<?php


namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;
use PleskX\Api\Client;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistryWrapper;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;
use Joomla\Keychain\Keychain;
use NextcloudApiWrapper\Wrapper;
use Doctrine\DBAL;
use Fusio\Engine\ConnectorInterface;

class NextcloudApiWrapper //extends Connection
	implements ConnectionInterface
{
	//	protected $connector;

    public function setKeychainRegistry(KeychainRegistryWrapper $KeychainRegistry){
	  $this->KeychainRegistry = $KeychainRegistry;
	}
	
	 
	public function getName():string
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
	
 /**	
	public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
	
	
   
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \NextcloudApiWrapper\Wrapper
     */
    public function getConnection(ParametersInterface $configuration) :mixed
    { 
      //  $this->configuration=$configuration;
        $this->setKeychainRegistry($this->connector->getConnection($configuration->get('KeychainRegistry')));
		         
		
		$passwordKey = $this->getRegKey( 'nextcloud.client.','.password', $configuration);
		 $password_config = $configuration->get('nextcloud.client.password');
		 if(//!$this->KeychainRegistry->has($passwordKey) && 
			is_string($password_config) && !empty($password_config) && '' !== $password_config){
			 $this->getKeychainRegistry()->set($passwordKey, $password_config);
			 $configuration->set('nextcloud.client.password', null);
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

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) :void
    {

	
          $builder->add($elementFactory->newInput('nextcloud.client.url',
												  'Nextcloud Url', 'text', 'The BaseUri/Url of the Nextcloud Server'));
		
		
          $builder->add($elementFactory->newInput('nextcloud.client.username',
												  'Username','text',  'Login: username of the Nextcloud User/Admin'));
		
		
          $builder->add($elementFactory->newInput('nextcloud.client.password',
												  'Password','text',  'The password of the Nextcloud user'));
	
   
          $builder->add($elementFactory->newConnection('KeychainRegistry',
												  KeychainRegistry::class, 
												  'The Keychain used to store Nextcloud credentials',
													 null));
   
    }
}
