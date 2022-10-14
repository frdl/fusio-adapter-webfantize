<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Spatie\TemporaryDirectory\TemporaryDirectory as TempDir;
use Fusio\Engine\Model\Connection;

class TemporaryDirectory implements ConnectionInterface
{
	
    public function getName() :string
    {
        return 'TemporaryDirectory';
    }

         
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Spatie\TemporaryDirectory\TemporaryDirectory
     */
    public function getConnection(ParametersInterface $configuration) : mixed
    {
        $location =  $configuration->get('tmp.dir.location');
        $name =  $configuration->get('tmp.dir.name');
		$temporaryDirectory = (new TempDir());
		if(!empty($location)){
			$temporaryDirectory->location($location);
		}
		
		if(!empty($name)){
			$temporaryDirectory->name($name);
		}
		
		if('auto' ===  $configuration->get('tmp.dir.create') ){
			$temporaryDirectory->create();
		}
		return $temporaryDirectory;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) :void
    {
          $builder->add($elementFactory->newInput('tmp.dir.location',
												  'Temp-Directory', 
												  'text',
												  'Leave blank to use the systems/users temp dir as default location'));
		
          $builder->add($elementFactory->newInput('tmp.dir.name',  
												  'Directory Name',
												  'text',
												  'The base nanme of the tmp directory. (Leave empty to use defaults from context(s))'));
   
		  		
		  $builder->add($elementFactory->newSelect('tmp.dir.create', 'Create-Method',
												  ['auto', 'manually'], 
												   'When to create the Temporary Directory (auto=immediatly, manually=lazy by user)'));
		
    }
}
