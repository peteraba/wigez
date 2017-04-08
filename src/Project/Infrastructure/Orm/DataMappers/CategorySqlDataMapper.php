<?php

namespace Project\Infrastructure\Orm\DataMappers;

use Opulence\Orm\DataMappers\SqlDataMapper;
use Project\Domain\Entities\Category as Entity;

class CategorySqlDataMapper extends SqlDataMapper implements ICategoryDataMapper
{
    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        if (!$entity instanceof Entity) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Customer entity.');
        }

        $statement = $this->writeConnection->prepare(
            'INSERT INTO categories (`name`) VALUES (:name)'
        );
        $statement->bindValues(
            [
                'name' => [$entity->getName(), \PDO::PARAM_STR],
            ]
        );
        $statement->execute();

        $entity->setId($this->writeConnection->lastInsertId());
    }

    /**
     * @param Entity $entity
     */
    public function delete($entity)
    {
        if (!$entity instanceof Entity) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Customer entity.');
        }

        $statement = $this->writeConnection->prepare(
            'UPDATE categories SET deleted = 1 WHERE id = :id'
        );
        $statement->bindValues(
            [
                'id' => [$entity->getId(), \PDO::PARAM_INT],
            ]
        );
        $statement->execute();
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $sql = $this->getQuery();

        return $this->read($sql, [], self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param array $where
     * @param bool  $joinCustomer
     *
     * @return string
     */
    private function getQuery(array $where = [1], bool $joinCustomer = false)
    {
        $sqlParts = [];

        $sqlParts[] = 'SELECT categories.id, categories.`name`';
        $sqlParts[] = 'FROM categories';

        if ($joinCustomer) {
            $sqlParts[] = 'INNER JOIN categories_customers ON categories.id = categories_customers.category_id';
        }

        $sqlParts[] = 'WHERE';
        $sqlParts[] = implode(' AND ', $where);
        $sqlParts[] = 'AND categories.deleted = 0';

        return implode(' ', $sqlParts);
    }

    /**
     * @param int|string $id
     *
     * @return array|null
     */
    public function getById($id)
    {
        $sql        = $this->getQuery(['`id` = :id']);
        $parameters = [
            'id' => [$id, \PDO::PARAM_INT],
        ];

        return $this->read($sql, $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getByName(string $name)
    {
        $parameters = ['name' => [$name, \PDO::PARAM_STR]];
        $sql        = $this->getQuery(['`name` = :name']);

        return $this->read($sql, $parameters, self::VALUE_TYPE_ENTITY);
    }

    /**
     * @param int $customerId
     *
     * @return array|null
     */
    public function getByCustomerId(int $customerId)
    {
        $parameters = ['customer_id' => [$customerId, \PDO::PARAM_INT]];
        $sql        = $this->getQuery(['customer_id = :customer_id'], true);

        return $this->read($sql, $parameters, self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param Entity $entity
     */
    public function update($entity)
    {
        if (!$entity instanceof Entity) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Customer entity.');
        }

        $statement = $this->writeConnection->prepare(
            'UPDATE categories SET `name` = :name WHERE id = :id'
        );
        $statement->bindValues(
            [
                'name' => $entity->getName(),
                'id'   => [$entity->getId(), \PDO::PARAM_INT],
            ]
        );
        $statement->execute();
    }

    /**
     * @param array $hash
     *
     * @return Entity
     */
    protected function loadEntity(array $hash)
    {
        return new Entity(
            (int)$hash['id'],
            $hash['name']
        );
    }
}