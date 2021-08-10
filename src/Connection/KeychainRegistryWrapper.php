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
