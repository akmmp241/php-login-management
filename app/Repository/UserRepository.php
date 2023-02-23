<?php

namespace Akmalmp\BelajarPhpMvc\Repository;

use Akmalmp\BelajarPhpMvc\Domain\User;
use PDO;

class UserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statment = $this->connection->prepare("INSERT INTO users (id, name, password) VALUES (?, ?, ?)");
        $statment->execute([
            $user->getId(),
            $user->getName(),
            $user->getPassword()
        ]);
        return $user;
    }

    public function update(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
        $statement->execute([
            $user->getName(),
            $user->getPassword(),
            $user->getId()
        ]);
        return $user;


    }


    public function findByID(string $id): ?User
    {
        $statment = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $statment->execute([$id]);
        try {
            if ($row = $statment->fetch()) {
                $user = new User();
                $user->setId($row['id']);
                $user->setName($row['name']);
                $user->setPassword($row['password']);
                return $user;
            } else {
                return null;
            }
        } finally {
            $statment->closeCursor();
        }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM users");
    }
}