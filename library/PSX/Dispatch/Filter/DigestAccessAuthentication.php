<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Dispatch\Filter;

use Closure;
use PSX\Data\RecordStoreInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Dispatch\Filter\DigestAccessAuthentication\Digest;
use PSX\Exception;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Authentication;

/**
 * Implementation of the http digest authentication. Note the digest
 * authentication has the advantage that the password is not transported in
 * plaintext over the wire instead a hash is used. This has the downside that we 
 * need to rebuild and compare the hash on the server side and therefor we need 
 * the password as plain text (wich requires you to store the password as 
 * plaintext) or in exactly the hash format wich is used by the digest function 
 * "md5([username]:[realm]:[pw])" wich is probably not often the case. If you 
 * need www-authentication you probably should use https + basic authentication 
 * since you are not required to store the password in such a format
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DigestAccessAuthentication implements FilterInterface
{
	protected $ha1Callback;
	protected $digestStore;
	protected $digest;

	protected $successCallback;
	protected $failureCallback;
	protected $missingCallback;

	/**
	 * The ha1Callback should return "md5([username]:[realm]:[pw])" wich is then
	 * used to compare the response from the client. If its successful the 
	 * onSuccess callback is called else the onFailure. If the Authorization 
	 * header is missing the onMissing callback is called. The default behavior
	 * is to store the nonce and opaque in the session but you can overwrite 
	 * that by providing a nonce and opaque in the constructor and overwrite the
	 * default onMissing callback
	 *
	 * @param Closure $ha1Callback
	 * @param PSX\Data\RecordStoreInterface $digestStore
	 */
	public function __construct(Closure $ha1Callback, RecordStoreInterface $digestStore)
	{
		$this->ha1Callback = $ha1Callback;
		$this->digestStore = $digestStore;

		$this->onSuccess(function(){
			// authentication successful
		});

		$this->onFailure(function(){
			throw new Exception('Invalid username or password');
		});

		$this->onMissing(function(Response $response) use ($digestStore) {
			$digest = new Digest();
			$digest->setNonce(sha1(time() + uniqid()));
			$digest->setOpaque(sha1(session_id()));

			$digestStore->save('digest', $digest);

			$params = array(
				'realm'  => 'psx',
				'qop'    => 'auth,auth-int',
				'nonce'  => $digest->getNonce(),
				'opaque' => $digest->getOpaque(),
			);

			$response->setStatusCode(401);
			$response->setHeader('WWW-Authenticate', 'Digest ' . Authentication::encodeParameters($params));

			throw new Exception('Missing authorization header');
		});

		// load digest from store
		$this->loadDigest();
	}

	public function loadDigest()
	{
		$this->digest = $this->digestStore->load('digest');
	}

	public function handle(Request $request, Response $response)
	{
		$authorization = $request->getHeader('Authorization');

		if(!empty($authorization))
		{
			$parts = explode(' ', $authorization, 2);
			$type  = isset($parts[0]) ? $parts[0] : null;
			$data  = isset($parts[1]) ? $parts[1] : null;

			if($type == 'Digest' && !empty($data))
			{
				$params = Authentication::decodeParameters($data);
				$algo   = isset($params['algorithm']) ? $params['algorithm'] : 'MD5';
				$qop    = isset($params['qop']) ? $params['qop'] : 'auth';

				if(!$this->digest instanceof Digest)
				{
					throw new Exception('Digest not available');
				}

				if($this->digest->getOpaque() != $params['opaque'])
				{
					throw new Exception('Invalid opaque');
				}

				// build ha1
				$ha1 = call_user_func_array($this->ha1Callback, array($params['username']));

				if($algo == 'MD5-sess')
				{
					$ha1 = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $params['cnonce']);
				}

				// build ha2
				if($qop == 'auth-int')
				{
					$ha2 = md5($request->getMethod() . ':' . $request->getUrl()->getPath() . ':' . md5($request->getBody()));
				}
				else
				{
					$ha2 = md5($request->getMethod() . ':' . $request->getUrl()->getPath());
				}

				// build response
				if($qop == 'auth' || $qop == 'auth-int')
				{
					$hash = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $params['nc'] . ':' . $params['cnonce'] . ':' . $qop . ':' . $ha2);
				}
				else
				{
					$hash = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $ha2);
				}

				if(strcmp($hash, $params['response']) === 0)
				{
					$this->callSuccess($response, $hash);
				}
				else
				{
					$this->callFailure($response);
				}
			}
			else
			{
				$this->callMissing($response);
			}
		}
		else
		{
			$this->callMissing($response);
		}
	}

	public function onSuccess(Closure $successCallback)
	{
		$this->successCallback = $successCallback;
	}

	public function onFailure(Closure $failureCallback)
	{
		$this->failureCallback = $failureCallback;
	}

	public function onMissing(Closure $missingCallback)
	{
		$this->missingCallback = $missingCallback;
	}

	protected function callSuccess(Response $response)
	{
		call_user_func_array($this->successCallback, array($response));
	}

	protected function callFailure(Response $response)
	{
		call_user_func_array($this->failureCallback, array($response));
	}

	protected function callMissing(Response $response)
	{
		call_user_func_array($this->missingCallback, array($response));
	}
}