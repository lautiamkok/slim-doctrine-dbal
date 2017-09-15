<?php
// Tell the container how to construct the db.
// http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
// https://gist.github.com/ziadoz/3875409#file-dbal-php
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

$container->add('Doctrine\DBAL\Connection', function() {
    $dbconfig = require './config/database.php';
    $connection = DriverManager::getConnection(array(
        'dbname' => $dbconfig['name'],
        'user' => $dbconfig['username'],
        'password' => $dbconfig['password'],
        'host' => $dbconfig['host'],
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
        'driverOptions' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    ), $config = new Configuration);
    return $connection;
});
