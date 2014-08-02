<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Record\Importer;

use PDOException;
use PSX\Data\Record;
use PSX\Data\Record\ImporterTestCase;
use PSX\Sql\TableAbstract;
use PSX\Sql\TableInterface;
use PSX\Sql\DbTestCase;

/**
 * TableImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TableImporterTest extends DbTestCase
{
	use ImporterTestCase;

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../../../Handler/handler_fixture.xml');
	}

	protected function getImporter()
	{
		return new Table();
	}

	protected function getRecord()
	{
		return new TestTable($this->sql);
	}

	protected function canImportComplexRecord()
	{
		return false;
	}
}

class TestTable extends TableAbstract
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
