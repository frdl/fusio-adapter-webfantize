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

use Fusio\Adapter\Webfantize\Connection\CircuitBreakerProtectedConnectionWrapper;


class CircuitBreakerConnection
{

    protected $Breaker;
	protected $Connection;
    public function __construct(Breaker $Breaker, ConnectionInterface $Connection)
    {
       $this->Connection=$Connection;
	   $this->Breaker=$Breaker;
    }
	
	public function __call($method, $params){
		$Connection = $this->Connection;
		$Breaker = $this->Breaker;
		
        $result = $Breaker->protect(function () use(&$Connection, &$Breaker, $method, $params) {
                  // throw new \Exception("An error as occured");
                    // return 'ok';
			 return call_user_func_array([$Connection, $method], $params);
        });	
		
		return $result;
	}
	
	public function __get($name){
		switch($name){
			case 'breaker':
				  return $this->Breaker;
				break;
			case 'connection':
				  return $this->Connection;
				break;
			default:
				throw new \Exception(__CLASS__.'->'.$name.' is not accessable');
			break;
		}
	}
/*
$fileCache  = new FilesystemCache('./store', 'txt');

//Create a circuit for github api with a file cache and we want to exclude all exception.
$breaker = new Breaker('github_api', ['ignore_exceptions' => true], $fileCache);

$breaker->addListener(CircuitEvents::SUCCESS, function (Event $event) {
    $circuit = $event->getCircuit();
    echo "Success:".$circuit->getFailures()."\n";
});

$breaker->addListener(CircuitEvents::FAILURE, function (Event $event) {
    $circuit = $event->getCircuit();
    echo "Increment failure:".$circuit->getFailures()."\n";
});

$breaker->addListener(CircuitEvents::OPEN, function (Event $event) {
    $circuit = $event->getCircuit();
    echo sprintf("circuit %s is open \n", $circuit->getName());
});

$breaker->addListener(CircuitEvents::CLOSED, function (Event $event) {
    $circuit = $event->getCircuit();
    echo sprintf("circuit %s is closed \n", $circuit->getName());
});

$breaker->addListener(CircuitEvents::HALF_OPEN, function (Event $event) {
    $circuit = $event->getCircuit();
    echo sprintf("circuit %s is half-open \n", $circuit->getName());
});

$result = $breaker->protect(function () {
    throw new \Exception("An error as occured");
    // return 'ok';
});
*/
 

}
