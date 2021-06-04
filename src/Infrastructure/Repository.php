<?php

namespace Infrastructure;

use Application\Entities\Product;

class Repository
implements
    \Application\Interfaces\ProductRepository,
    \Application\Interfaces\UserRepository,
    \Application\Interfaces\RatingRepository
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

    private function updateAverageRating(\mysqli $con, int $productID) {
        $statAvg = $this->executeStatement(
            $con,
            'SELECT AVG(grade) FROM ratings WHERE product = ?',
            function ($s) use ($productID) {
                $s->bind_param('i', $productID);
            }
        );
        $statAvg->bind_result($avgGrade);
        if ($statAvg->fetch()) {
            $avg = $avgGrade;
        }
        $statAvg->close();
        $stat = $this->executeStatement(
            $con,
            'UPDATE products SET averageRating = ? WHERE id = ?',
            function ($s) use ($avg, $productID) {
                $s->bind_param('di', $avg, $productID);
            }
        );

        $stat->close();
    }

    private function updateProductOnRemovedRating(\mysqli $con, int $productID) {
        $stat = $this->executeStatement(
            $con,
            'UPDATE products SET ratingCount = ratingCount - 1 WHERE id = ?',
            function ($s) use ($productID) {
                $s->bind_param('i', $productID);
            }
        );
        $stat->close();
        $this->updateAverageRating($con, $productID);
    }

    private function updateProductOnAddedRating(\mysqli $con, int $productID) {
        $stat = $this->executeStatement(
            $con,
            'UPDATE products SET ratingCount = ratingCount + 1 WHERE id = ?',
            function ($s) use ($productID) {
                $s->bind_param('i', $productID);
            }
        );
        $stat->close();
        $this->updateAverageRating($con, $productID);
    }

    // === public methods ===
    public function getProductFromID(string $id): ?\Application\Entities\Product
    {
        $intID = (int)$id;
        $result = null;

        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM products WHERE id = ?',
            function ($s) use ($intID) {
                $s->bind_param('i', $intID);
            }
        );

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        if ($stat->fetch()) {
            $result = new \Application\Entities\Product(
                $id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description
            );
        }
        $stat->close();
        $con->close();

        return $result;
    }

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

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
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

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
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

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
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

        $stat->bind_result($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        while ($stat->fetch()) {
            $products[] = new Product($id, $name, $manufacturer, $creator, $ratingCount, $averageRating, $description);
        }
        $stat->close();
        $con->close();

        return $products;
    }

    public function addProduct(string $name, string $manufacturer, string $creator, $description): void
    {
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'INSERT INTO products (name, manufacturer, username, description)
            VALUES (?, ?, ?, ?)',
            function ($s) use ($name, $manufacturer, $creator, $description) {
                $s->bind_param('ssss', $name, $manufacturer, $creator, $description);
            }
        );
        $stat->close();
        $con->commit();
        $con->close();
    }

    public function updateProduct(string $id, string $name, string $manufacturer, string $description): void
    {
        $con = $this->getConnection();
        $intID = (int) $id;
        $stat = $this->executeStatement(
            $con,
            'UPDATE products SET name = ?, manufacturer = ?, description = ? WHERE id = ?',
            function ($s) use ($intID, $name, $manufacturer, $description) {
                $s->bind_param('sssi', $name, $manufacturer, $description, $intID);
            }
        );

        $stat->close();
        $con->commit();
        $con->close();
    }

    public function removeProduct(string $id): void
    {
        $con = $this->getConnection();
        $intID = (int) $id;
        $stat = $this->executeStatement(
            $con,
            'DELETE FROM products WHERE id = ?',
            function ($s) use ($intID) {
                $s->bind_param('i', $intID);
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

    public function addRating(string $userName, int $productID, int $grade, string $comment): void {
        $avg = 0.0;
        $con = $this->getConnection();
        $date = date('Y-m-d H:i:s');
        $stat = $this->executeStatement(
            $con,
            'INSERT INTO ratings (username, product, createDate, grade, comment)
            VALUES (?, ?, ?, ?, ?)',
            function ($s) use ($userName, $productID, $date, $grade, $comment) {
                $s->bind_param('sisis', $userName, $productID, $date, $grade, $comment);
            }
        );
        $stat->close();

        $this->updateProductOnAddedRating($con, $productID);

        $con->commit();
        $con->close();
    }

    public function getRatingsForProduct(int $productID): array
    {
        $con = $this->getConnection();
        $ratings = [];
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM ratings WHERE product = ? ORDER BY createDate DESC',
            function ($s) use ($productID) {
                $s->bind_param('i', $productID);
            }
        );

        $stat->bind_result($id, $username, $productID, $date, $grade, $comment);
        while ($stat->fetch()) {
            $ratings[] = new \Application\Entities\Rating($id, $username, $productID, $date, $grade, $comment);
        }
        $stat->close();
        $con->close();
        return $ratings;
    }

    public function getRating(int $ratingID): ?\Application\Entities\Rating
    {
        $con = $this->getConnection();
        $rating = null;
        $stat = $this->executeStatement(
            $con,
            'SELECT * FROM ratings WHERE id = ?',
            function ($s) use ($ratingID) {
                $s->bind_param('i', $ratingID);
            }
        );

        $stat->bind_result($id, $username, $productID, $date, $grade, $comment);
        if ($stat->fetch()) {
            $rating = new \Application\Entities\Rating($id, $username, $productID, $date, $grade, $comment);
        }
        $stat->close();
        $con->close();
        return $rating;
    }

    public function updateRating(int $ratingID, int $productID, string $comment, int $grade): void
    {
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'UPDATE ratings SET comment = ?, grade = ? WHERE id = ?',
            function ($s) use ($ratingID, $comment, $grade) {
                $s->bind_param('sii', $comment, $grade, $ratingID);
            }
        );
        $stat->close();

        $this->updateAverageRating($con, $productID);

        $con->commit();
        $con->close();
    }

    public function removeRating(int $ratingID, int $productID): void
    {
        $con = $this->getConnection();
        $stat = $this->executeStatement(
            $con,
            'DELETE FROM ratings WHERE id = ?',
            function ($s) use ($ratingID) {
                $s->bind_param('i', $ratingID);
            }
        );
        $stat->close();

        $this->updateProductOnRemovedRating($con, $productID);

        $con->commit();
        $con->close();
    }


}
