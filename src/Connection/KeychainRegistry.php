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
use Joomla\Keychain\Keychain;
use Fusio\Engine\Model\Connection;
/**
 * Gcp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class KeychainRegistry extends Connection implements ConnectionInterface
{
    public function getName()
    {
        return 'KeychainRegistry';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Joomla\Keychain\Keychain
     */
    public function getConnection(ParametersInterface $config) : Keychain
    {

        
        $keychain = new Keychain;
        
        $params = [
            'keychain.datastorage.address' => $config->get('keychain.datastorage.address'),
      //      'keychain.phrase.secret' => $config->get('keychain.phrase.secret'),
            'keychain.phrase.address' => $config->get('keychain.phrase.address'),
            'keychain.pubkey.address' => $config->get('keychain.pubkey.address'),
            'keychain.privkey.keyid' => $config->get('keychain.privkey.keyid'),
        //   'keychain.privkey.secret' => is_string($config->get('keychain.privkey.secret')) || null,
        ];

          $isCreated = false !== file_get_contents($params['keychain.datastorage.address']);
        
          if (!$isCreated) {
             $temp = tmpfile();
fwrite($temp,$config->get('PRIVATE_KEY'));
//fseek($temp, 0);
//echo fread($temp, 1024);
//fclose($temp); // dies entfernt die Datei
              
                         $keychain->createPassphraseFile($params['keychain.phrase.secret'],
                                           $params['keychain.phrase.address'],
                                          $temp, 
                                             $params['keychain.privkey.secret']);
              
              fclose($temp); 
              $config->set('keychain.privkey.secret', null);
              $config->set('PRIVATE_KEY', null);
              file_put_contents( $params['keychain.pubkey.address'], $config->get('PUBLIC_KEY'));
          }
        
        
        $keychain->loadKeychain($params['keychain.datastorage.address'],
                                 $params['keychain.phrase.address'],
                                 $params['keychain.pubkey.address']);
        
        
        return $keychain;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
                /*
            'keychain.datastorage.address' => $config->get('keychain.datastorage.address'),
            'keychain.phrase.secret' => $config->get('keychain.phrase.secret'),
            'keychain.phrase.address' => $config->get('keychain.phrase.address'),
            'keychain.pubkey.address' => $config->get('keychain.pubkey.address'),
            'keychain.privkey.address' => $config->get('keychain.privkey.address'),
            'keychain.privkey.secret' => is_string($config->get('keychain.privkey.secret')) || null,

*/
        
        
        
         $builder->add($elementFactory->newInput('keychain.datastorage.address', 'KeychainFile', 'Where to store the registries data'));
        
         $builder->add($elementFactory->newInput('keychain.phrase.secret', 'Secret-Phrase', 'The registry storage access secret'));
        
         $builder->add($elementFactory->newInput('keychain.phrase.address', 'Phrase-File', 'Where to store the storage access secret'));
         $builder->add($elementFactory->newInput('keychain.pubkey.address', 'PublicKey-File', 'Where to store the PublicKey'));
    //     $builder->add($elementFactory->newInput('keychain.privkey.address', 'PrivateKey-File', 'Where to load the '));
         $builder->add($elementFactory->newInput('keychain.privkey.keyid', 'Key-ID', 'Identifier of the private key'));
     //   $isCreated = false !== file_get_contents($params['keychain.datastorage.address']);
      //  if (!$isCreated) {
          $builder->add($elementFactory->newInput('keychain.privkey.secret', 'Private-Key Password', 'The Password of the Private-Key'));
        
    $builder->add($elementFactory->newTextArea('PRIVATE_KEY', 'PRIVATE_KEY', 'text', 'The contents of the PRIVATE_KEY.'));    
    $builder->add($elementFactory->newTextArea('PUBLIC_KEY', 'PUBLIC_KEY', 'text', 'The contents of the PUBLIC_KEY.'));  
        
        

      //  }

          
     //   $builder->add($elementFactory->newInput('projectId', 'Project-Id', 'The project ID from the Google Developers Console'));
       // $builder->add($elementFactory->newTextArea('keyFile', 'Key-File', 'json', 'The contents of the service account credentials .json file retrieved from the Google Developers Console.'));
    }
}

