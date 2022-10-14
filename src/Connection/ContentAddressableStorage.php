<?php

namespace Fusio\Adapter\Webfantize\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Model\Connection;
use frdl\cta\Server;

class ContentAddressableStorage implements ConnectionInterface
{
	
    public function getName() : string
    {
        return 'ContentAddressableStorage';
    }

         

    public function getConnection(ParametersInterface $configuration) : mixed
    {

		// max(80, intval(''!==$configuration->get('cta.options.chunksize') ? $configuration->get('cta.options.chunksize') : 80)));
		 $chunksize  = ''!==trim($configuration->get('cta.options.chunksize')) ? intval($configuration->get('cta.options.chunksize') ) : 80;
		
         $StorageServer = new Server([
             'chunksize' => $chunksize,
             Server::URIS_DIR => $configuration->get('cta.storagedir.uris'),
             Server::CHUNKS_DIR => $configuration->get('cta.storagedir.chunks'),
             Server::FILES_DIR => $configuration->get('cta.storagedir.files'),             
         ], true);
    
		    return $StorageServer;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) :void
    {
          $builder->add($elementFactory->newInput('cta.options.chunksize',
												  'Chunksize', 'integer','Chunksize - IT IS HIGHLY RECOMMENDED THAT YOU SET THIS VALUE TO 80'));
                               
          $builder->add($elementFactory->newInput('cta.storagedir.uris',
												  'UriStorage Directory', 'text','The directory for the UriStorage (will be created if not exists).'));
                          
          $builder->add($elementFactory->newInput('cta.storagedir.chunks',
												  'ChunksStorage Directory', 'text','The directory for the ChunksStorage (will be created if not exists).'));
                          
          $builder->add($elementFactory->newInput('cta.storagedir.files',
												  'FilesStorage Directory', 'text','The directory for the FilesStorage (will be created if not exists).'));
		   
    }
}
