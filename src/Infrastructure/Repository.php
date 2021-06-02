<?php

namespace Infrastructure;

use Application\Entities\Product;

class Repository
implements
    \Application\Interfaces\ProductRepository,
    \Application\Interfaces\UserRepository
{
    private $server;
    private $userName;
    private $password;
    private $database;

    public function __construct(string $server, string $userName, string $password, string $database)
    {
        $this->server = $server;
        $this->userName = $userName;
        $this->password = $password;
        $this->database = $database;
    }

    // === private helper methods ===

    private function getConnection()
    {
        $con = new \mysqli($this->server, $this->userName, $this->password, $this->database);
        if (!$con) {
            die('Unable to connect to database. Error: ' . mysqli_connect_error());
        }
        return $con;
    }

    private function executeQuery($connection, $query)
    {
        $result = $connection->query($query);
        if (!$result) {
            die("Error in query '$query': " . $connection->error);
        }
        return $result;
    }

    private function executeStatement($connection, $query, $bindFunc)
    {
        $statement = $connection->prepare($query);
        if (!$statement) {
            die("Error in prepared statement '$query': " . $connection->error);
        }
        $bindFunc($statement);
        if (!$statement->execute()) {
            die("Error executing prepared statement '$query': " . $statement->error);
        }
        return $statement;
    }

    // === public methods ===
    public function getProductsForName(string $name): array
    {
        $name = "%$name%";
        $products = [];
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM products WHERE name LIKE ?',
            function ($s) use ($name) {
                $s->bind_param('s', $name);
            }
        );

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        }
        $stat->close();
        $con->close();

        return $products;
    }

    public function getProductsForManufacturer(string $manufacturer): array
    {
        $manufacturer = "%$manufacturer%";
        $products = [];
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM products WHERE manufacturer LIKE ?',
            function ($s) use ($manufacturer) {
                $s->bind_param('s', $manufacturer);
            }
        );

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        }
        $stat->close();
        $con->close();

        return $products;
    }

    public function getProductsFromNameAndManufacturer(string $name, string $manufacturer): array
    {
        $name = "%$name%";
        $manufacturer = "%$manufacturer%";
        $products = [];
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM products WHERE name LIKE ? && manufacturer LIKE ?',
            function ($s) use ($name, $manufacturer) {
                $s->bind_param('ss', $name, $manufacturer);
            }
        );

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        }
        $stat->close();
        $con->close();

        return $products;
    }


    public function getAllProducts(): array
    {
        $products = [];
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM products',
            function ($s) {

            }
        );

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating);
        }
        $stat->close();
        $con->close();

        return $products;
    }

    public function addProduct(string $name, string $manufacturer, string $creator): void
    {
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'INSERT INTO products (name, manufacturer, username)
            VALUES (?, ?, ?)',
            function ($s) use ($name, $manufacturer, $creator) {
                $s->bind_param('sss', $name, $manufacturer, $creator);
            }
        );
        $stat->close();
        $con->commit();
        $con->close();
    }


    public function getUser(string $userName): ?\Application\Entities\User
    {
        $user = null;
        $con = $this->getConnection();
        $stat = $this->executeStatement(
          $con,
          'SELECT userName FROM users WHERE userName = ?',
          function($s) use ($userName) {
              $s->bind_param('s', $userName);
          }
        );
        $stat->bind_result($userNameQueryResult);
        if ($stat->fetch()) {
            $user = new \Application\Entities\User($userNameQueryResult);
        }
        $stat->close();
        $con->close();

        return $user;
    }

    public function getUserForUserNameAndPassword(string $userName, string $password): ?\Application\Entities\User
    {
        $user = null;
        $con = $this->getConnection();
        $stat= $this->executeStatement(
            $con,
            'SELECT passwordHash FROM users WHERE userName = ?',
            function ($s) use ($userName) {
                $s->bind_param('s', $userName);
            }
        );
        $stat->bind_result($passwordHash);
        if ($stat->fetch() && password_verify($password, $passwordHash)) {
            $user = new \Application\Entities\User($userName);
        }
        $stat->close();
        $con->close();

        return $user;
    }

    public function registerUser(string $userName, string $password): void {
        $con = $this->getConnection();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stat = $this->executeStatement(
            $con,
            'INSERT INTO users VALUES (?, ?)',
            function ($s) use ($userName, $passwordHash) {
                $s->bind_param('ss', $userName, $passwordHash);
            }
        );

        $stat->close();
        $con->commit();
        $con->close();
    }
}
