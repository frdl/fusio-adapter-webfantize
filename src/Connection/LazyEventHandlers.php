<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;
use Webfan\App\EventModule;

class LazyEventHandlers implements ConnectionInterface
{
	
    public function getName()
    {
        return 'LazyEventHandlers';
    }

         

    public function getConnection(ParametersInterface $configuration) 
    {
		     EventModule::setBaseDir($configuration->get('runtime.events.dir'));
         $EventHandlers = EventModule::action(self::class);
        /* 
        You can use the static method(s) to switch the "action" (events-group) in build/runtime processes...
         $EventHandlers::action('your-events-group-name')->...
         $EventHandlers::register('your-events-group-name', 'your-event-name', static function($eventName, $emitter, \webfan\hps\Event $Event){
              print_r($Event->getArgument("testParam"));
         });
        */
		    return $EventHandlers;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
          $builder->add($elementFactory->newInput('runtime.events.dir',
												  'Runtime-Compiled-Events-Directory', 'text','The Directory to save and load the compiled lazy eventhandlers.'));
		   
    }
}
