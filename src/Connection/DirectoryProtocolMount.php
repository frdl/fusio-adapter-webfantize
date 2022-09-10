<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;
use FilesystemStreamWrapper;


/**
*
* This is just an example, you should use this more dynamically e.g. in your tenancy implementation...
* Or use a reasonable template and provide it with the proper context args (e.g. host, appId, ...) to mount the correct directory
*
**/
/** example:
     $pathTemplate = '%1$s/userdata/apps/%2$d/vhosts/%3$s';    //'mount.stream.template'
     $rootPath = sprintf($pathTemplate, $baseDir, $appId, $host);
     // ==
     $rootPath = $connection->mount($baseDir, $appId, $host);
     
     $protocol://file.txt  => $rootPath.'file.txt'
*/
class DirectoryProtocolMount implements ConnectionInterface
{
	
    public function getName()
    {
        return 'DirectoryProtocolMount';
    }

         

    public function getConnection(ParametersInterface $configuration) 
    {
 /** example:
     $pathTemplate = '%1$s/userdata/apps/%2$d/vhosts/%3$s';    //'mount.stream.template'
     $rootPath = sprintf($pathTemplate, $baseDir, $appId, $host);
     // ==
     $rootPath = $connection->mount($baseDir, $appId, $host);
     
     $protocol://file.txt  => $rootPath.'file.txt'
*/
        $protocol = $configuration->get('mount.stream.protocol');
        $directory = $configuration->get('mount.stream.directory');
        $template = $configuration->get('mount.stream.template');
        
        $mount = function() use($protocol, $directory, $template){
               $params = func_get_args();
               
               $path = '';
               if(!empty($directory)){
                 $path .= $directory;
               }
               
               if(!empty($template)){
                 array_unshift($params, $template);
                 $path .= call_user_func_array('sprintf', $params);
               }               
              
              FilesystemStreamWrapper::register($protocol, $path);
               
             return $path;
        };
        $connection = new \stdclass;
        $connection->mount = $mount;
        $connection->protocol = $protocol;
        $connection->directory = $directory;
        $connection->template = $template;
		    return $connection;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
          $builder->add($elementFactory->newInput('mount.stream.protocol',
												  'The-Protocol', 'text','The protocol to register the handler for, e.g. "app" for "app://"'));
                          
          $builder->add($elementFactory->newInput('mount.stream.directory',
												  'The-Target-Directory', 'text','The directory to register the handler for.'));    
                          
          $builder->add($elementFactory->newInput('mount.path.template',
												  'The-Path-Generator-Template', 'text','The-Path-Generator-Template.Example: $pathTemplate = \'%1$s/userdata/apps/%2$d/vhosts/%3$s\'; $rootPath = $connection->mount($baseDir, $appId, $host);')); 
    }
}
