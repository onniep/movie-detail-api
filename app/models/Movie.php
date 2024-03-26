<?php
// C:\xampp\htdocs\movie-detail-api\app\models\Movie.php
namespace App\Models;

use PDO;
use PDOException;

// Necessary imports for the logging functionality
use Psr\Container\ContainerInterface;
use Laminas\Log\Logger;

class Movie
{
    protected $db;

    protected $container;
    protected $logger;

    public function __construct(PDO $db, ContainerInterface $container)
    {
        $this->db = $db;

        $this->container = $container;
        $this->logger = $container->get(Logger::class);
    }

    /**
     * Gets all movies from the database
     */
    public function getAll()
    {
        $sql = "SELECT * FROM movies";

        try {
            $stmt = $this->db->query($sql);
            $movies = $stmt->fetchAll(PDO::FETCH_OBJ);

            return $movies;

        } catch (PDOException $e) {
            $this->logger->error('Error retrieving all movies: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Gets specific movie by id from the database
     */
    public function getById($id)
    {
        $sql = "SELECT * 
                FROM movies 
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                // Handle the case of no matching post
                $errorResponse = array(
                    "status" => 404,
                    "message" => "Resource not found with this ID.",
                    "resource-id" => $id
                );
                return $errorResponse;
            }

            return $result;

        } catch (PDOException $e) {
            $this->logger->error('Error retrieving all movies: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a new movie in the database
     */
    public function addData($title, $year, $released, $runtime, $genre, $director, $actors, $country, $poster, $imdb, $type)
    {
        $sql = "INSERT INTO movies (title, year, released, runtime, genre, director, actors, country, poster, imdb, type) 
                VALUES (:title, :year, :released, :runtime, :genre, :director, :actors, :country, :poster, :imdb, :type)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':released', $released, PDO::PARAM_STR);
            $stmt->bindParam(':runtime', $runtime, PDO::PARAM_STR);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindParam(':director', $director, PDO::PARAM_STR);
            $stmt->bindParam(':actors', $actors, PDO::PARAM_STR);
            $stmt->bindParam(':country', $country, PDO::PARAM_STR);
            $stmt->bindParam(':poster', $poster, PDO::PARAM_STR);
            $stmt->bindParam(':imdb', $imdb, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);

            $isDataInserted = $stmt->execute();

            if (empty($isDataInserted)) {
                // Handle the case of failed insertion
                $errorResponse = array(
                    "success" => $isDataInserted,
                    "message" => 'Failed to insert new movie.'
                );
                return $errorResponse; // Return the error response directly
            }

            return $isDataInserted;

        } catch (PDOException $e) {
            $this->logger->error('Error inserting new movie: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Edit movie in the database using PATCH method
     */
    public function patchData($id, $data)
    {
        $sql = "UPDATE movies SET ";
        $params = array();

        // Build the SET clause and parameter bindings for the update
        foreach ($data as $field => $value) {
            $sql .= "$field = :$field, ";
            $params[$field] = $value;
        }

        // Remove the trailing comma and space
        $sql = rtrim($sql, ", ");

        // Add the WHERE condition
        $sql .= " WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);

            // Bind parameter values dynamically, depending on the available fields to be edited
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($params as $field => &$value) {
                $stmt->bindParam(":$field", $value);
            }

            $isDataUpdated = $stmt->execute();

            if (empty($isDataUpdated)) {
                // Handle the case of failed update
                $errorResponse = array(
                    "success" => $isDataUpdated,
                    "message" => 'Failed to update movie.'
                );
                return $errorResponse;
            }

            return $isDataUpdated;

        } catch (PDOException $e) {
            $this->logger->error('Error updating movie: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Edit movie in the database using PUT method
     */
    public function putData($id, $title, $year, $released, $runtime, $genre, $director, $actors, $country, $poster, $imdb, $type)
    {
        $sql = "UPDATE movies 
        SET title = :title, 
            year = :year, 
            released = :released, 
            runtime = :runtime, 
            genre = :genre, 
            director = :director, 
            actors = :actors, 
            country = :country, 
            poster = :poster, 
            imdb = :imdb, 
            type = :type
        WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->bindParam(':released', $released);
            $stmt->bindParam(':runtime', $runtime, PDO::PARAM_STR);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindParam(':director', $director, PDO::PARAM_STR);
            $stmt->bindParam(':actors', $actors, PDO::PARAM_STR);
            $stmt->bindParam(':country', $country, PDO::PARAM_STR);
            $stmt->bindParam(':poster', $poster, PDO::PARAM_STR);
            $stmt->bindParam(':imdb', $imdb, PDO::PARAM_STR);
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);

            $isDataUpdated = $stmt->execute();

            return $isDataUpdated;

        } catch (PDOException $e) {
            $this->logger->error('Error updating movie: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete movie from the database
     */
    public function deleteData($id)
    {
        $sql = "DELETE FROM movies WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $isDataDeleted = $stmt->execute();

            return $isDataDeleted;

        } catch (PDOException $e) {
            $this->logger->error('Error deleting movie: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
 * Retrieve the count of movies in the database
 */
public function countMovies()
{
    $sql = "SELECT COUNT(*) AS movie_count FROM movies";

    try {
        $stmt = $this->db->query($sql);
        $movieCount = $stmt->fetch(PDO::FETCH_ASSOC);

        return $movieCount['movie_count']; // Return only the count

    } catch (PDOException $e) {
        $this->logger->error('Error retrieving movie count: ' . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

/**
 * Retrieve a subset of movies from the database based on pagination
 */
public function findByNumberPerPage($numberPerPage, $page)
{
    // Calculate the offset based on the page number and number of movies per page
    $offset = ($page - 1) * $numberPerPage;

    $sql = "SELECT * FROM movies LIMIT :limit OFFSET :offset";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $numberPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $movies;
    } catch (PDOException $e) {
        $this->logger->error('Error retrieving movies by number per page: ' . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}
/**
 * Retrieve a subset of movies from the database based on pagination and sorting
 */
public function findByNumberPerPageAndSort($numberPerPage, $page, $sortBy, $order)
{
    // Calculate the offset based on the page number and number of movies per page
    $offset = ($page - 1) * $numberPerPage;

    // Validate sorting order
    if ($order !== 'asc' && $order !== 'desc') {
        return ['error' => 'Invalid sorting order. Use \'asc\' or \'desc\'.'];
    }

    // Validate sorting field (optional: you may have predefined fields to sort by)
    // List of allowed fields for sorting
    $allowedFields = ['title', 'year', 'released', 'runtime', 'genre', 'director', 'actors', 'country', 'poster', 'imdb', 'type'];
    
    // Check if the provided field is allowed
    if (!in_array($sortBy, $allowedFields)) {
        return ['error' => 'Invalid field to sort by.'];
    }

    $sql = "SELECT * FROM movies ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $numberPerPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $movies;
    } catch (PDOException $e) {
        $this->logger->error('Error retrieving sorted movies by number per page: ' . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}



}