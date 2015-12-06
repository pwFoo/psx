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

namespace PSX\Api\Resource\Listing;

use PSX\Api\Documentation;
use PSX\Api\DocumentationInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\ListingInterface;
use PSX\Cache;
use PSX\Data\Schema;

/**
 * CachedListing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CachedListing implements ListingInterface
{
    protected $listing;
    protected $cache;
    protected $expire;

    public function __construct(ListingInterface $listing, Cache $cache, $expire = null)
    {
        $this->listing = $listing;
        $this->cache   = $cache;
        $this->expire  = $expire;
    }

    public function getResourceIndex()
    {
        $item = $this->cache->getItem('api-resources');

        if ($item->isHit()) {
            return $item->get();
        } else {
            $result = $this->listing->getResourceIndex();

            $item->set($result, $this->expire);

            $this->cache->save($item);

            return $result;
        }
    }

    public function getDocumentation($sourcePath)
    {
        $item = $this->cache->getItem('api-resource-' . substr(md5($sourcePath), 0, 16));

        if ($item->isHit()) {
            return $item->get();
        } else {
            $doc = $this->listing->getDocumentation($sourcePath);

            if ($doc instanceof DocumentationInterface) {
                $this->materializeDocumentation($doc);

                $item->set($doc, $this->expire);

                $this->cache->save($item);

                return $doc;
            }
        }

        return null;
    }

    /**
     * A resource can contain schema definitions which are only resolved if we
     * actual call the getDefinition method i.e. the schema is stored in a
     * database. So before we cache the documentation we must get the actual
     * definition object which we can serialize
     *
     * @param \PSX\Api\DocumentationInterface $documentation
     */
    protected function materializeDocumentation(DocumentationInterface $documentation)
    {
        $versions = $documentation->getResources();

        foreach ($versions as $version => $resource) {
            foreach ($resource as $method) {
                $request = $method->getRequest();
                if ($request) {
                    $method->setRequest(new Schema($request->getDefinition()));
                }

                $responses = $method->getResponses();
                foreach ($responses as $statusCode => $response) {
                    $method->addResponse($statusCode, new Schema($response->getDefinition()));
                }
            }
        }
    }
}
