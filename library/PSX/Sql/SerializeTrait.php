<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Sql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types;
use Doctrine\DBAL\Types\Type;

/**
 * SerializeTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait SerializeTrait
{
	protected static $mapping = array(
		TableInterface::TYPE_SMALLINT => Type::SMALLINT,
		TableInterface::TYPE_INT      => Type::INTEGER,
		TableInterface::TYPE_BIGINT   => Type::BIGINT,
		TableInterface::TYPE_BOOLEAN  => Type::BOOLEAN,
		TableInterface::TYPE_DECIMAL  => Type::DECIMAL,
		TableInterface::TYPE_FLOAT    => Type::FLOAT,
		TableInterface::TYPE_DATE     => Type::DATE,
		TableInterface::TYPE_DATETIME => Type::DATETIME,
		TableInterface::TYPE_TIME     => Type::TIME,
		TableInterface::TYPE_VARCHAR  => Type::STRING,
		TableInterface::TYPE_TEXT     => Type::TEXT,
		TableInterface::TYPE_BLOB     => Type::BLOB,
		TableInterface::TYPE_ARRAY    => Type::TARRAY,
		TableInterface::TYPE_OBJECT   => Type::OBJECT,
	);

	protected function unserializeType($value, $type)
	{
		$type     = (($type >> 20) & 0xFF) << 20;
		$platform = $this->connection->getDatabasePlatform();

		if(isset(self::$mapping[$type]))
		{
			return Type::getType(self::$mapping[$type])->convertToPHPValue($value, $platform);
		}
		else
		{
			return Type::getType(Type::STRING)->convertToPHPValue($value, $platform);
		}
	}

	protected function serializeType($value, $type)
	{
		$type     = (($type >> 20) & 0xFF) << 20;
		$platform = $this->connection->getDatabasePlatform();

		if(isset(self::$mapping[$type]))
		{
			return Type::getType(self::$mapping[$type])->convertToDatabaseValue($value, $platform);
		}
		else
		{
			return Type::getType(Type::STRING)->convertToDatabaseValue($value, $platform);
		}
	}

	public static function getTypeByDoctrineType(Type $type)
	{
		$mapping = array_flip(self::$mapping);

		if(isset($mapping[$type->getName()]))
		{
			return $mapping[$type->getName()];
		}
		else
		{
			return TableInterface::TYPE_VARCHAR;
		}
	}
}
