<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Importer;

use DateTime;
use PSX\Data\RecordAbstract;
use PSX\Http\Message;
use PSX\Test\Environment;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    public function testJson()
    {
        $body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<record>
	<id>1</id>
	<title>foo</title>
	<date>2014-07-29T23:37:00</date>
</record>
XML;

        $request = new Message(array('Content-Type' => 'application/xml'), $body);
        $record  = Environment::getService('importer')->import(new XmlRecord(), $request);

        $this->assertEquals(1, $record->getId());
        $this->assertEquals('foo', $record->getTitle());
        $this->assertInstanceOf('DateTime', $record->getDate());
        $this->assertEquals('Tue, 29 Jul 2014 23:37:00 +0000', $record->getDate()->format('r'));
    }
}

class XmlRecord extends RecordAbstract
{
    protected $id;
    protected $title;
    protected $date;

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }
    
    public function getDate()
    {
        return $this->date;
    }
}
