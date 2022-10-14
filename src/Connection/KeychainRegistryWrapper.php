<?php
 
namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Joomla\Keychain\Keychain;
use Fusio\Engine\Model\Connection;
use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;

class KeychainRegistryWrapper
{
	protected $KeychainRegistry;
	protected $configuration;
    public function __construct(Keychain $KeychainRegistry, ParametersInterface $configuration)
    {
		$this->KeychainRegistry=$KeychainRegistry;
        $this->configuration = $configuration;
		$this->load();
    }	
	
	public function __call($name, $parameter){
		return call_user_func_array([$this->KeychainRegistry, $name], $parameter);
	}	
	
	public function load():Keychain {
		$this->KeychainRegistry->loadKeychain($this->configuration->get('datastorage_address'),
											  $this->configuration->get('phrase_address'), 
											  $this->configuration->get('pubkey_address'));
		return $this->KeychainRegistry;
	}
	
	
	public function __destruct(){
		$this->save();
	}
	
	public function save():Keychain {
		$this->KeychainRegistry->saveKeychain($this->configuration->get('datastorage_address'),
											  $this->configuration->get('phrase_address'), 
											  $this->configuration->get('pubkey_address'));
		return $this->KeychainRegistry;
	}	


}
