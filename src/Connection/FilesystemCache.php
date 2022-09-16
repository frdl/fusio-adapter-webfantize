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
use Doctrine\Common\Cache\FilesystemCache as FSCache;
use Fusio\Engine\Model\Connection;

class FilesystemCache implements ConnectionInterface
{
	
    public function getName() : string
    {
        return 'FilesystemCache';
    }

         
    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    public function getConnection(ParametersInterface $configuration)  : mixed
    {
        $FilesystemCache = new FSCache($configuration->get('cache.fs.dir'), 
								   $configuration->get('cache.fs.ext'));
		
		return $FilesystemCache;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) : void
    {
          $builder->add($elementFactory->newInput('cache.fs.dir',
												  'Cache-Directory', 'text','The Directory where the cache resists'));
		
          $builder->add($elementFactory->newInput('cache.fs.ext',  
												  'Cache-Fileextension','text','The Cache-Fileextension of the caches'));
   
    }
}
