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
use Fusio\Adapter\Webfantize\TenantInterface;
use Fusio\Adapter\Webfantize\VHostInterface;
use Fusio\Adapter\Webfantize\TenancyInterface;

use Fusio\Adapter\Webfantize\Connection\KeychainRegistry;


class Tenancy implements ConnectionInterface, TenancyInterface
{

	protected $fusioDir;
	protected $vhostsDir;
	protected $defaultDomain;
    public function getName()
    {
        return 'Tenancy';
    }


       protected function boot( $fusioDir,$vhostsDir, $defaultDomain){
		   $this->fusioDir=$fusioDir;
		   $this->vhostsDir=$vhostsDir;
		  $this->defaultDomain=$defaultDomain;		   
	   }

       public function config():array
	   {
		   
		   return $this->config;
	   }
		/**
	Tenant::__construct(
		                        $routeId = null,
								$baseUrl = null, 
								\Fusio\Engine\Model\AppInterface $app = null,
								\Fusio\Engine\Model\UserInterface $user = null,
							   array $connections=[])
							   */
       public function getByHost( $host):TenantInterface
	   {
		   
	   }
       public function getByDomain( $domain):TenantInterface
	   {
		   
	   }
	
	
	  public function get($identifier):TenantInterface
	   {
		   
	   }
         
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Fusio\Adapter\Webfantize\Connection\Tenancy
     */
    public function getConnection(ParametersInterface $configuration)// : Tenancy
    {	/*	
		$Tenancy = new self($configuration->get('tenancy.directory.fusio'),
						   $configuration->get('tenancy.directory.vhosts'),
						   $configuration->get('tenancy.default.domain'));
		return $Tenancy;
				   */
		$this->boot($configuration->get('tenancy.directory.fusio'),
						   $configuration->get('tenancy.directory.vhosts'),
						   $configuration->get('tenancy.default.domain'));
        return $this;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
          $builder->add($elementFactory->newInput('tenancy.directory.fusio',
												  'fusio-Directory', 'text', 'The Directory where the master fusio resists'));
		
          $builder->add($elementFactory->newInput('tenancy.directory.vhosts',
												  'vHosts-Directory', 'text', 'The Directory where the tenants vhosts are found'));
		
          $builder->add($elementFactory->newInput('tenancy.default.domain',
												  'Domain','text',  'The (unassigned) Default Domain'));
		
    }
}