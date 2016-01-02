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

namespace PSX\Data\Record\Importer\Test;

use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;

/**
 * Table
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Table extends TableAbstract
{
    public function getName()
    {
        return 'news';
    }

    public function getColumns()
    {
        return array(
            'id'       => TableInterface::TYPE_INT | 10 | TableInterface::PRIMARY_KEY | TableInterface::AUTO_INCREMENT,
            'title'    => TableInterface::TYPE_VARCHAR | 16,
            'active'   => TableInterface::TYPE_BOOLEAN,
            'disabled' => TableInterface::TYPE_BOOLEAN,
            'count'    => TableInterface::TYPE_INT,
            'rating'   => TableInterface::TYPE_FLOAT,
            'date'     => TableInterface::TYPE_DATETIME,
        );
    }
}
