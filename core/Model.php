<?php

namespace core;

use \ClanCats\Hydrahon\Builder;
use \ClanCats\Hydrahon\Query\Sql\FetchableInterface;

class Model
{
    protected static Builder $h;
    protected static string $tableName;

    public function __construct()
    {
        self::checkH();
    }

    public static function checkH()
    {
        if (self::$h == null) {
            $connection = Database::getInstance();
            self::$h = new Builder('mysql', function ($query, $queryString, $queryParameters) use ($connection) {
                $statement = $connection->prepare($queryString);
                $statement->execute($queryParameters);

                if ($query instanceof FetchableInterface) {
                    return $statement->fetchAll(\PDO::FETCH_ASSOC);
                }
            });
        }

        self::$h = self::$h->table(self::getTableName());
    }

    public static function getTableName(): string
    {
        if (isset(static::$tableName)) {
            return static::$tableName;
        } else {
            $className = explode('\\', get_called_class());
            $className = end($className);
            return strtolower($className) . 's';
        }
    }

    public static function select($fields = [])
    {
        self::checkH();
        return self::$h->select($fields);
    }

    public static function insert($fields = [])
    {
        self::checkH();
        return self::$h->insert($fields);
    }

    public static function update($fields = [])
    {
        self::checkH();
        return self::$h->update($fields);
    }

    public static function delete()
    {
        self::checkH();
        return self::$h->delete();
    }

}