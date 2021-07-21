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

use Doctrine\Common\Cache\FilesystemCache;
use Eljam\CircuitBreaker\Breaker;
use Eljam\CircuitBreaker\Event\CircuitEvents;
use Symfony\Component\EventDispatcher\Event;

use Fusio\Adapter\Webfantize\Connection\CircuitBreakerConnection;
use Fusio\Engine\ConnectorInterface;

class CircuitBreakerProtectedConnectionWrapper 
//	extends Connection 
	implements ConnectionInterface
{
	protected $connector;
	public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
 
    public function getName()
    {
        return 'CircuitBreakerProtectedConnectionWrapper';
    }


 
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Fusio\Adapter\Webfantize\Connection\CircuitBreakerConnection
     */
    public function getConnection(ParametersInterface $configuration) 
    { 
		$FilesystemCache = $this->connector->getConnection($configuration->get('FilesystemCache'));
		$Connection = $this->connector->getConnection($configuration->get('connection'));

		$breaker = new Breaker($this->getName().'-'.$this->getId(), [	
			'ignore_exceptions' => false
		], $FilesystemCache);

		$connection = new CircuitBreakerConnection($breaker, $Connection);

		return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {

	/*
          $builder->add($elementFactory->newInput('circuit.breaker.name',
												  'Name', 'text', 'The Name of the CircuitBreakerProtectedConnectionWrapper'));
		*/
		
         $builder->add($elementFactory->newConnection('FilesystemCache',
			 \Fusio\Adapter\Webfantize\Connection\FilesystemCache::class, 
			 'The FilesystemCache used to store the CircuitBreaker Data',
			 null));


          $builder->add($elementFactory->newConnection('connection',
												  \Fusio\Engine\Model\Connection::class, 
												  'The connection to wrap into the circuit-breaker',
													 null));
   
    }
}