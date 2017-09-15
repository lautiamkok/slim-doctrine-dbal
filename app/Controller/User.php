<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Doctrine\DBAL\Connection;
use \Monolog\Logger;
use \Ramsey\Uuid\Uuid;

class User
{
    protected $connection;
    protected $builder;

    public function __construct(
        Connection $connection,
        Logger $logger
    ) {
        $this->connection = $connection;

        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#sql-query-builder
        $this->builder = $connection->createQueryBuilder();

        // Log anything.
        $logger->addInfo("Logged from user controller");
    }

    public function fetchUsers(Request $request)
    {
        // Columns to select.
        $columns = [
            'uuid',
            'name',
            'created_on',
            'updated_on',
        ];

        // Get user(s).
        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#building-a-query
        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html#fetchall
        $collection = $this->builder
            ->select(implode(",", $columns))
            ->from('user')
            ->execute()
            ->fetchAll()
            ;

        // Return the result.
        return $collection;
    }

    public function fetchUser(Request $request, array $args)
    {
        // Columns to select.
        $columns = [
            'uuid',
            'name',
            'created_on',
            'updated_on',
        ];

        // Get user.
        $data = $this->builder
            ->select(implode(",", $columns))
            ->from('user')
            ->where('name = ?')
            ->setParameter(0, $args['name'])
            ->execute()
            ->fetch()
            ;

        // Throw error if no result found.
        // https://laravel.com/docs/5.5/collections#method-count
        if (!$data) {
            throw new \Exception('No user found', 400);
        }

        // Return the result.
        return $data;
    }

    public function insertUser(Request $request)
    {
        // Get params and validate them here.
        $name = $request->getParam('name');
        $email = $request->getParam('email');

        // Throw if empty.
        if (!$name) {
            throw new \Exception('$name is empty', 400);
        }

        // Throw if empty.
        if (!$email) {
            throw new \Exception('$email is empty', 400);
        }

        // Create a timestamp.
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        // Or:
        // $timestamp = time();

        // Generate a version 1 (time-based) UUID object.
        // https://github.com/ramsey/uuid
        $uuid3 = Uuid::uuid1();
        $uuid = $uuid3->toString();

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;
        $model->name = $name;
        $model->email = $email;
        $model->created_on = $timestamp;

        // Insert user.
        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html#insert
        $result = $this->connection
            ->insert('user', [
                'uuid' => $model->uuid,
                'name' => $model->name,
                'email' => $model->email,
                'created_on' => $model->created_on
            ]);

        // Throw if it fails.
        if (!$result === 1) {
            throw new \Exception('Insert row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }

    public function updateUser(Request $request)
    {
        // Get params and validate them here.
        $uuid = $request->getParam('uuid');
        $name = $request->getParam('name');
        $email = $request->getParam('email');

        // Throw if empty.
        if (!$uuid) {
            throw new \Exception('$uuid is empty', 400);
        }

        // Throw if empty.
        if (!$name) {
            throw new \Exception('$name is empty', 400);
        }

        // Throw if empty.
        if (!$email) {
            throw new \Exception('$email is empty', 400);
        }

        // Create a timestamp.
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;
        $model->name = $name;
        $model->email = $email;
        $model->updated_on = $timestamp;

        // Update user.
        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html#update
        $result = $this->connection
            ->update('user', [
                'name' => $model->name,
                'email' => $model->email,
                'updated_on' => $model->updated_on,
            ], [
                'uuid' => $model->uuid
            ]);

        // Throw if it fails.
        if ($result === 0) {
            throw new \Exception('Update row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }

    public function deleteUser(Request $request)
    {
        // Get params and validate them here.
        $uuid = $request->getParam('uuid');

        // Throw if empty.
        if (!$uuid) {
            throw new \Exception('$uuid is empty', 400);
        }

        // Assuming this is a model in a more complex app system.
        $model = new \stdClass;
        $model->uuid = $uuid;

        // Delete user.
        // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html#delete
        $result = $this->connection
            ->delete('user',[
                'uuid' => $model->uuid
            ]);

        // Throw if it fails.
        if ($result === 0) {
            throw new \Exception('Delete row failed', 400);
        }

        // Return the model if it is OK.
        return $model;
    }
}
