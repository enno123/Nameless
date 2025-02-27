<?php
/**
 * Handles querying an underlying PDO connection.
 * Contains all methods that are used to interact with the database.
 *
 * @package NamelessMC\Database
 * @see PDO
 * @author Aberdeener
 * @version 2.0.0-pr13
 * @license MIT
 */
trait InteractsWithDatabase {

    protected PDO $_pdo;
    private PDOStatement $_query;
    private bool $_error = false;
    private array $_results;
    private string $_prefix = '';
    private int $_count = 0;
    protected QueryRecorder $_query_recorder;

    public function getPDO(): PDO {
        return $this->_pdo;
    }

    /**
     * @deprecated Use selectQuery function to select data from DB, or createQuery function to modify data in DB
     * @return static
     */
    public function query(string $sql, array $params = [], int $fetch_method = PDO::FETCH_OBJ) {
        return $this->selectQuery(...func_get_args());
    }

    /**
     * @return static
     */
    public function selectQuery(string $sql, array $params = [], int $fetch_method = PDO::FETCH_OBJ) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param, is_int($param)
                        ? PDO::PARAM_INT
                        : PDO::PARAM_STR
                    );
                    $x++;
                }
            }

            $this->_query_recorder->pushQuery($sql, $params);

            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll($fetch_method);
                $this->_count = $this->_query->rowCount();
            } else {
                print_r($this->_pdo->errorInfo());
                $this->_error = true;
            }

        }

        return $this;
    }

    public function createTable(string $name, string $table_data, string $other) {
        $name = $this->_prefix . $name;
        $sql = "CREATE TABLE `{$name}` ({$table_data}) {$other}";

        if (!$this->createQuery($sql)->error()) {
            return $this;
        }

        return false;
    }

    public function error(): bool {
        return $this->_error;
    }

    /**
     * @return static
     */
    public function createQuery(string $sql, array $params = []) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (count($params)) {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            $this->_query_recorder->pushQuery($sql, $params);

            if ($this->_query->execute()) {
                $this->_count = $this->_query->rowCount();
            } else {
                print_r($this->_pdo->errorInfo());
                $this->_error = true;
            }
        }

        return $this;
    }

    public function get(string $table, array $where) {
        return $this->action('SELECT *', $table, $where);
    }

    public function action(string $action, string $table, array $where = []) {
        if (count($where) === 3) {
            $operators = ['=', '>', '<', '>=', '<=', '<>'];

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            $table = $this->_prefix . $table;

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if (!$this->selectQuery($sql, [$value])->error()) {
                    return $this;
                }
            }
        }

        return false;
    }

    public function like(string $table, string $column, string $like) {
        $table = $this->_prefix . $table;
        $sql = "SELECT * FROM {$table} WHERE {$column} LIKE '{$like}'";

        if (!$this->selectQuery($sql)->error()) {
            return $this;
        }

        return false;
    }

    public function delete(string $table, array $where) {
        return $this->deleteAction('DELETE', $table, $where);
    }

    public function deleteAction(string $action, string $table, array $where = []) {
        if (count($where) === 3) {
            $operators = ['=', '>', '<', '>=', '<=', '<>'];

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            $table = $this->_prefix . $table;

            if (in_array($operator, $operators)) {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if (!$this->createQuery($sql, [$value])->error()) {
                    return $this;
                }
            }
        }

        return false;
    }

    public function insert(string $table, array $fields = []): bool {
        $keys = array_keys($fields);
        $values = '';
        $x = 1;

        foreach ($fields as $field) {
            $values .= '?';
            if ($x < count($fields)) {
                $values .= ', ';
            }
            $x++;
        }

        $table = $this->_prefix . $table;
        $sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`) VALUES ({$values})";

        return (!$this->createQuery($sql, $fields)->error());
    }

    public function update(string $table, int $id, array $fields): bool {
        $set = '';
        $x = 1;

        foreach ($fields as $name => $value) {
            $set .= "{$name} = ?";

            if ($x < count($fields)) {
                $set .= ', ';
            }
            $x++;
        }
        $table = $this->_prefix . $table;
        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        return (!$this->createQuery($sql, $fields)->error());
    }

    public function increment(string $table, int $id, string $field): bool {
        $table = $this->_prefix . $table;
        $sql = "UPDATE {$table} SET {$field} = {$field} + 1 WHERE id = ?";

        return (!$this->createQuery($sql, [$id])->error());
    }

    public function decrement(string $table, int $id, string $field): bool {
        $table = $this->_prefix . $table;
        $sql = "UPDATE {$table} SET {$field} = {$field} - 1 WHERE id = ?";

        return (!$this->createQuery($sql, [$id])->error());
    }

    public function first(): ?object {
        $results = $this->results();

        return $results[0] ?? null;
    }

    public function results(): array {
        return $this->_results;
    }

    public function count(): int {
        return $this->_count;
    }

    /**
     * Get the last inserted ID
     *
     * @return string|false ID of the last inserted row or false on failure
     */
    public function lastId() {
        return $this->_pdo->lastInsertId();
    }

    public function alterTable(string $name, string $column, string $attributes) {
        $name = $this->_prefix . $name;
        $sql = "ALTER TABLE `{$name}` ADD {$column} {$attributes}";

        if (!$this->createQuery($sql)->error()) {
            return $this;
        }
        return false;
    }

    public function orderAll(string $table, string $order, string $sort) {
        $table = $this->_prefix . $table;
        $sql = "SELECT * FROM {$table} ORDER BY {$order} {$sort}";

        if (!$this->selectQuery($sql)->error()) {
            return $this;
        }

        return false;
    }

    public function orderWhere(string $table, string $where, string $order, string $sort) {
        $table = $this->_prefix . $table;
        $sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY {$order} {$sort}";

        if (!$this->selectQuery($sql)->error()) {
            return $this;
        }

        return false;
    }

    public function showTables(string $showTable) {
        $showTable = $this->_prefix . $showTable;
        $sql = "SHOW TABLES LIKE '{$showTable}'";

        if (!$this->selectQuery($sql)->error()) {
            return $this->_query->rowCount();
        }

        return false;
    }

}
