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

/**
 * Gcp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class KeychainRegistry implements ConnectionInterface
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
        /*
$keychainFile = '/etc/project/config/keychain.dat';
$passPhrasePath = '/etc/project/config/keychain.passphrase';
$publicKeyPath = '/etc/project/config/keychain.pem';

$privateKeyFile = __DIR__ . '/data/privkey.key';
 $_PRIVATEKEYPASSWORD__ 
$passPhrase 

*/
        
        $keychain = new Keychain;
        
        $params = [
            'keychain.datastorage.address' => $config->get('keychain.datastorage.address'),
            'keychain.phrase.secret' => $config->get('keychain.phrase.secret'),
            'keychain.phrase.address' => $config->get('keychain.phrase.address'),
            'keychain.pubkey.address' => $config->get('keychain.pubkey.address'),
            'keychain.privkey.address' => $config->get('keychain.privkey.address'),
            'keychain.privkey.secret' => is_string($config->get('keychain.privkey.secret')) || null,
        ];

        $isCreated = false !== file_get_contents($params['keychain.datastorage.address']);
        if (!$isCreated) {
           $keychain->createPassphraseFile($params['keychain.phrase.secret'],
                                           $params['keychain.phrase.address'],
                                            $params['keychain.privkey.address'], 
                                             $params['keychain.privkey.secret']);
        }

        
        $keychain->loadKeychain($params['keychain.datastorage.address'],
                                 $params['keychain.phrase.address'],
                                 $params['keychain.pubkey.address']);
        
        
        return $keychain;
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
     //   $builder->add($elementFactory->newInput('projectId', 'Project-Id', 'The project ID from the Google Developers Console'));
       // $builder->add($elementFactory->newTextArea('keyFile', 'Key-File', 'json', 'The contents of the service account credentials .json file retrieved from the Google Developers Console.'));
    }
}
