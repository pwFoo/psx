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

namespace PSX\ActivityStream\ObjectType;

use PSX\Data\SerializeTestAbstract;
use PSX\DateTime;

/**
 * IssueTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class IssueTest extends SerializeTestAbstract
{
    public function testIssue()
    {
        $issue = new Issue();
        $issue->setDisplayName('Terms of Use Violation');
        $issue->setUrl('http://.../terms-of-use');
        $issue->setTypes(array('http://example.org/codes/inappropriateMaterial', 'http://example.org/codes/copyrightViolation'));

        $content = <<<JSON
  {
    "objectType": "issue",
    "displayName": "Terms of Use Violation",
    "url": "http://.../terms-of-use",
    "types": [
      "http://example.org/codes/inappropriateMaterial",
      "http://example.org/codes/copyrightViolation"
    ]
  }
JSON;

        $this->assertRecordEqualsContent($issue, $content);

        $this->assertEquals(array('http://example.org/codes/inappropriateMaterial', 'http://example.org/codes/copyrightViolation'), $issue->getTypes());
    }
}
