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

use Fusio\Adapter\Webfantize\Connection\KeychainRegistryWrapper;

/**
 * Gcp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class KeychainRegistry extends Connection implements ConnectionInterface 
{
    public function getName() :string
    {
        return 'KeychainRegistry';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Joomla\Keychain\Keychain
     */
    public function getConnection(ParametersInterface $config) : mixed
    {

        
        $keychain = new Keychain;
        
        $params = [
            'datastorage_address' => $config->get('datastorage_address'),
            'phrase_secret' => $config->get('phrase_secret'),
            'phrase_address' => $config->get('phrase_address'),
            'pubkey_address' => $config->get('pubkey_address'),
			'key_secret'=> $config->get('key_secret'),
            'keyid' => $config->get('keyid'),
        //   'keychain.privkey.secret' => is_string($config->get('keychain.privkey.secret')) || null,
        ];

          $isCreated = false !== file_get_contents($params['datastorage_address']);
        
                if(!$isCreated && empty($config->get('PUBLIC_KEY')) && file_exists($params['keychain.pubkey.address']) ){
					 $config->set('PUBLIC_KEY', file_get_contents($params['datastorage_address']) );
				}
		
       
			  
		       if (!file_exists($params['pubkey_address'])  || !empty($config->get('PRIVATE_KEY')) ){
                     // touch($params['keychain.pubkey.address']);
				//$privateKeyFile = __DIR__ . '/data/privkey.key';
		         //$publicKeyFile = __DIR__ . '/data/pubkey.pem';
                         if( empty($config->get('key_secret')) ) {
							 $config->set('key_secret', \sha1(\mt_rand(1,99999).\microtime())  );
						 }

                           $keys = \frdl\pki\KeyCreator::generate([
                                'private_key_bits' => 1024,
                           ], $config->get('key_secret') );

                   //file_put_contents($privateKeyFile, $keys['privkey']);
				    $tmp = tmpfile();
				    fwrite($tmp,$keys['privkey'] );
				    fseek($tmp, 0);
				     $config->set('PRIVATE_KEY', fread($tmp, strlen($keys['privkey']) ) );
				     fclose($tmp);
                     file_put_contents($params['pubkey_address'], $keys['pubkey'] );
				     $config->set('PUBLIC_KEY', file_get_contents($params['pubkey_address']) );
		         }
					  
			  
		   if (!$isCreated || !empty($config->get('PRIVATE_KEY')) ) {	  
             $temp = tmpfile();
             fwrite($temp,$config->get('PRIVATE_KEY'));
//fseek($temp, 0);
//echo fread($temp, 1024);
//fclose($temp); // dies entfernt die Datei
              $privkeypath = stream_get_meta_data($temp)['uri'];
			   
                         $keychain->createPassphraseFile($params['phrase_secret'],
                                           $params['phrase_address'],
                                           $privkeypath, 
                                              $config->get('key_secret')  );
              
              fclose($temp); 
              $config->set('key_secret', null);
              $config->set('PRIVATE_KEY', null);
			    if (!file_exists($params['pubkey_address']) ){
                  file_put_contents( $params['pubkey_address'], $config->get('PUBLIC_KEY'));
				}
			   
 
			   
			  $keychain->saveKeychain($params['datastorage_address'], 
									  $params['phrase_address'],
									  $params['pubkey_address']);
          }
        
        
        $keychain->loadKeychain($params['datastorage_address'],
                                 $params['phrase_address'],
                                 $params['pubkey_address']);
        
        
        return new KeychainRegistryWrapper($keychain, $config);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory) : void
    {
                /*
            'keychain.datastorage.address' => $config->get('keychain.datastorage.address'),
            'keychain.phrase.secret' => $config->get('keychain.phrase.secret'),
            'keychain.phrase.address' => $config->get('keychain.phrase.address'),
            'keychain.pubkey.address' => $config->get('keychain.pubkey.address'),
            'keychain.privkey.address' => $config->get('keychain.privkey.address'),
            'keychain.privkey.secret' => is_string($config->get('keychain.privkey.secret')) || null,

*/
        
        
        
         $builder->add($elementFactory->newInput('datastorage_address', 'KeychainFile', 'Where to store the registries data'));
        
         $builder->add($elementFactory->newInput('phrase_secret', 'Secret-Phrase', 'The registry storage access secret'));
        
         $builder->add($elementFactory->newInput('phrase_address', 'Phrase-File', 'Where to store the storage access secret'));
         $builder->add($elementFactory->newInput('pubkey_address', 'PublicKey-File', 'Where to store the PublicKey'));
 
         $builder->add($elementFactory->newInput('keyid', 'Key-ID', 'Identifier of the private key'));
 
          $builder->add($elementFactory->newInput('key_secret', 'Private-Key Password', 'The Password of the Private-Key'));
        
    $builder->add($elementFactory->newTextArea('PRIVATE_KEY', 'PRIVATE_KEY', 'text', 'The contents of the PRIVATE_KEY.'));    
    $builder->add($elementFactory->newTextArea('PUBLIC_KEY', 'PUBLIC_KEY', 'text', 'The contents of the PUBLIC_KEY.'));  

    }
}
