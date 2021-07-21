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
use Spatie\TemporaryDirectory\TemporaryDirectory as TempDir;
use Fusio\Engine\Model\Connection;

class TemporaryDirectory implements ConnectionInterface
{
	
    public function getName()
    {
        return 'TemporaryDirectory';
    }

         
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Spatie\TemporaryDirectory\TemporaryDirectory
     */
    public function getConnection(ParametersInterface $configuration) : TempDir
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

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
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