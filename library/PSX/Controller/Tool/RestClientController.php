<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Controller\Tool;

use PSX\Api\DocumentedInterface;
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\Record;
use PSX\Data\Schema\Generator;
use PSX\Data\Schema\Documentation;
use PSX\Data\WriterInterface;

/**
 * RestClientController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RestClientController extends ViewAbstract
{
	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	public function onGet()
	{
		parent::onGet();

		$this->template->set(__DIR__ . '/../Resource/rest_client_controller.tpl');

		$this->setBody(array(
			'links' => [
				[
					'rel'  => 'self',
					'href' => $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RestClientController'),
				],
				[
					'rel'  => 'router',
					'href' => $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RoutingController'),
				],
			]
		));
	}
}