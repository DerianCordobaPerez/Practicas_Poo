<?php

include_once 'database/Connection.php';

class Student {
    public function __construct(
        public string $name,
        public string $email,
        public string $license,
        public int $age,
        public int $course,
        public string $photo,
        private PDO|null $connection = null
    )
    {
        $this->name = $name;
        $this->email = $email;
        $this->license = $license;
        $this->age = $age;
        $this->course = $course;
        $this->photo = $photo;
        $this->connection = Connection::connect();
    }

    /**
     * It takes the values of the properties of the object and inserts them into the database
     */
    public function store(): void
    {
        $query = 'INSERT INTO 
            students (name, email, license, age, course, photo) 
            VALUES (:name, :email, :license, :age, :course, :photo)
        ';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':name' => $this->name,
                ':email' => $this->email,
                ':license' => $this->license,
                ':age' => $this->age,
                ':course' => $this->course,
                ':photo' => $this->photo
            ]);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * It updates the student's information in the database
     */
    public function update(): void
    {
        $query = 'UPDATE students SET 
            name = :name,
            email = :email,
            age = :age,
            course = :course,
            photo = :photo
            WHERE license = :license
        ';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':name' => $this->name,
                ':email' => $this->email,
                ':age' => $this->age,
                ':course' => $this->course,
                ':photo' => $this->photo,
            ]);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * It deletes the student from the database
     */
    public function destroy(): void
    {
        $query = 'DELETE FROM students WHERE license = :license';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':license' => $this->license
            ]);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * It receives a license as a parameter, and returns a Student object if the license exists in the
     * database, or null if it doesn't
     * 
     * @param string license The license of the student you want to find.
     * 
     * @return ?Student The student object
     */
    public static function find(string $license): ?Student
    {
        $query = 'SELECT * FROM students WHERE license = :license';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':license' => $license
            ]);

            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return new Student(
                    $result['name'],
                    $result['email'],
                    $result['license'],
                    $result['age'],
                    $result['course'],
                    $result['photo']
                );
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return null;
    }

    /**
     * It returns an array of all the students in the database
     * 
     * @return array An array of all the students in the database.
     */
    public static function all(): array
    {
        $query = 'SELECT * FROM students';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return [];
    }

    /**
     * It takes a string as an argument, and returns an array of students that match the query
     * 
     * @param string query The query to be executed.
     * 
     * @return array An array of associative arrays.
     */
    public static function search(string $query): array
    {
        $query = 'SELECT * FROM
            students WHERE 
            name LIKE :query
            OR
            email LIKE :query
            OR
            license LIKE :query
        ';

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':query' => '%' . $query . '%'
            ]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        return [];
    }
}