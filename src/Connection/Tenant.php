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
use Fusio\Engine\Model\Connection;
use Fusio\Engine\Repository;
use Fusio\Engine\Model\ConnectionMemory; 
use Fusio\Engine\Model\AppMemory;
use Fusio\Engine\Model\UserMemory;
use Fusio\Engine\Context;
use Fusio\Engine\Repository\RepositoryInterface;

use Dflydev\DotAccessData\Data;

use Configula\ConfigValues;

class Tenant
//	extends Connection
	implements ConnectionInterface, TenantInterface, ContextInterface, RepositoryInterface
{
  
	
    protected $action=null;	
	
	protected $context = null; 
    protected $_connections=[];		
    protected $_connection=null;
	protected $vhostsDir;
	protected $fusio_dir;
	protected $configFusioWebfantized=[];
	protected $UserMemory =null;
	protected $AppMemory=null;
	protected $ConnectionMemory=null;
	protected $alias=[];
	protected $modules=[];
	
	
	
    public function getName()
    {
        return 'Tenant';
    }	
    /**
     * @param integer $routeId
     * @param string $baseUrl
     * @param \Fusio\Engine\Model\AppInterface $app
     * @param \Fusio\Engine\Model\UserInterface $user
    */
    public function __construct(
		                        $routeId = null,
								$baseUrl = null, 
								\Fusio\Engine\Model\AppInterface $app = null,
								\Fusio\Engine\Model\UserInterface $user = null,
							   array $connections=[])
    {
        $this->routeId = $routeId;
        $this->baseUrl = $baseUrl;
        $this->app     = $app;
        $this->user    = $user;
		$this->context=new Context($routeId, $baseUrl, $app, $user);
		$this->_connections = $connections;
    }
    
	
       public function mount( $alias, $id)
	   {
		   $this->alias[$alias]=$id;
		   return $this;
	   }
    /**
     * @param \Fusio\Engine\Model\ConnectionInterface $connection
     */
    public function add(\Fusio\Engine\Model\ConnectionInterface $connection)
    {
        $this->_connections[$connection->getId()] = $connection;
    }

    /**
     * @return \Fusio\Engine\Model\ConnectionInterface[]
     */
    public function getAll()
    {
        return $this->_connections;
    }

	
	
	
	
	
    /**
     * @param integer|string $id
     * @return \Fusio\Engine\Model\ConnectionInterface|null
     */
    protected function _get($id)
    {
        if (empty($this->_connections)) {
            return null;
        }

        if (isset($this->_connections[$id])) {
            return $this->_connections[$id];
        }

        foreach ($this->_connections as $connection) {
            if ($connection->getName() == $id) {
                return $connection;
            }
        }

        return null;
    }
	
	protected function get($id)
    {
      
		if (isset($this->alias[$alias])) {
            $id=$this->alias[$alias];
        }

       return $this->_get($id);
    }
	
       public function connection( $alias) :ConnectionInterface
	   {
		   
	   }
	
       public function vhost( $stage=null):VHostInterface
	   {
		   
	   }
       public function config(array $config = null):array
	   {
		   
		   return $config;
	   }
	
    /**
     * @inheritdoc
     */
    public function getRouteId()
    {
        return $this->routeId;
    }

    /**
     * @inheritdoc
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @inheritdoc
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @inheritdoc
     */
    public function withAction(\Fusio\Engine\Model\ActionInterface $action)
    {
        $me = clone $this;
        $me->action = $action;

        return $me;
    }


    /**
     * @inheritdoc
     */
    public function withConnection($connection)
    {
        $me = clone $this;
        $me->_connection = $connection;

        return $me;
    }
  




       
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Fusio\Adapter\Webfantize\TenantInterface
     */
    public function getConnection(ParametersInterface $configuration): Tenant
    {
 
		 $this->Tenancy = $this->connector->getConnection($configuration->get('Tenancy'));

    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

          $builder->add($elementFactory->newConnection('Tenancy',
												  \Fusio\Adapter\Webfantize\Connection\Tenancy::class, 
												  'The Tenancy used to act as Tenancy master provider'));
      
    }
}