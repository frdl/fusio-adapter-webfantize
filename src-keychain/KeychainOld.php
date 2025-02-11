<?php

namespace Webfan\Patch{
   $sep = 'X19oYWx0X2NvbXBpbGVyKCk7'; 
   $code = @file_get_contents('https://webfan.de/install/?source=Webfan\KeychainOld');
   if(false===$code){
	throw new \Exception('Could not read source in '.__FILE__.' line '.__LINE__);   
   }
   list($sourcecode,$sigdata) = explode(base64_decode($sep), $code, 2);
	
   if(!file_put_contents(__FILE__, $sourcecode)){
	throw new \Exception('Could not write source in '.__FILE__.' line '.__LINE__);   
   }	
	
  return require __FILE__;
}


namespace Webfan{

use Joomla\Registry\Registry;

/**
 * Keychain Class
 *
 * @since  1.0
 */
class KeychainOld extends Registry
{
	/**
	 * Method to use for encryption.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $method = 'aes-128-cbc';

	/**
	 * Initialisation vector for encryption method.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $iv = '1234567890123456';


	/**
	 * Create a passphrase file
	 *
	 * @param   string  $passphrase            The passphrase to store in the passphrase file.
	 * @param   string  $passphraseFile        Path to the passphrase file to create.
	 * @param   string  $privateKeyFile        Path to the private key file to encrypt the passphrase file.
	 * @param   string  $privateKeyPassphrase  The passphrase for the private key.
	 *
	 * @return  boolean  Result of writing the passphrase file to disk.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function createPassphraseFile($passphrase, $passphraseFile, $privateKeyFile, $privateKeyPassphrase)
	{
		$privateKey = \openssl_get_privatekey(file_get_contents($privateKeyFile), $privateKeyPassphrase);

		if (!$privateKey)
		{
			throw new \RuntimeException('Failed to load private key.');
		}

		$crypted = '';

		if (!\openssl_private_encrypt($passphrase, $crypted, $privateKey))
		{
			throw new \RuntimeException('Failed to encrypt data using private key.');
		}

		return file_put_contents($passphraseFile, $crypted);
	}


	/**
	 * Delete a registry value (very simple method)
	 *
	 * @param   string  $path  Registry Path (e.g. joomla.content.showauthor)
	 *
	 * @return  mixed  Value of old value or boolean false if operation failed
	 *
	 * @since   1.0
	 * @deprecated  2.0  Use `Registry::remove()` instead.
	 */
	public function deleteValue($path)
	{
		return $this->remove($path);
	}


	/**
	 * Load a keychain file into this object.
	 *
	 * @param   string  $keychainFile    Path to the keychain file.
	 * @param   string  $passphraseFile  The path to the passphrase file to decript the keychain.
	 * @param   string  $publicKeyFile   The file containing the public key to decrypt the passphrase file.
	 *
	 * @return  boolean  Result of loading the object.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function loadKeychain($keychainFile, $passphraseFile, $publicKeyFile)
	{
		if (!file_exists($keychainFile))
		{
			throw new \RuntimeException('Attempting to load non-existent keychain file');
		}

		$passphrase = $this->getPassphraseFromFile($passphraseFile, $publicKeyFile);

		$cleartext = \openssl_decrypt(file_get_contents($keychainFile), $this->method, $passphrase, true, $this->iv);

		if ($cleartext === false)
		{
			throw new \RuntimeException('Failed to decrypt keychain file');
		}

		return $this->loadObject(json_decode($cleartext));
	}


	/**
	 * Save this keychain to a file.
	 *
	 * @param   string  $keychainFile    The path to the keychain file.
	 * @param   string  $passphraseFile  The path to the passphrase file to encrypt the keychain.
	 * @param   string  $publicKeyFile   The file containing the public key to decrypt the passphrase file.
	 *
	 * @return  boolean  Result of storing the file.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function saveKeychain($keychainFile, $passphraseFile, $publicKeyFile)
	{
		$passphrase = $this->getPassphraseFromFile($passphraseFile, $publicKeyFile);
		$data       = $this->toString('JSON');

		$encrypted = @\openssl_encrypt($data, $this->method, $passphrase, true, $this->iv);

		if ($encrypted === false)
		{
			throw new \RuntimeException('Unable to encrypt keychain');
		}

		return file_put_contents($keychainFile, $encrypted);
	}


	/**
	 * Get the passphrase for this keychain
	 *
	 * @param   string  $passphraseFile  The file containing the passphrase to encrypt and decrypt.
	 * @param   string  $publicKeyFile   The file containing the public key to decrypt the passphrase file.
	 *
	 * @return  string  The passphrase in from passphraseFile
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function getPassphraseFromFile($passphraseFile, $publicKeyFile)
	{
		if (!file_exists($publicKeyFile))
		{
			throw new \RuntimeException('Missing public key file');
		}

		$publicKey = \openssl_get_publickey(file_get_contents($publicKeyFile));

		if (!$publicKey)
		{
			throw new \RuntimeException('Failed to load public key.');
		}

		if (!file_exists($passphraseFile))
		{
			throw new \RuntimeException('Missing passphrase file');
		}

		$passphrase = '';

		if (!\openssl_public_decrypt(file_get_contents($passphraseFile), $passphrase, $publicKey))
		{
			throw new \RuntimeException('Failed to decrypt passphrase file');
		}

		return $passphrase;
	}
}

}
