<?php
//C:\xampp\htdocs\movie-detail-api\app\controllers\MovieController.php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Movie;
use App\Response\CustomResponse;
use App\Models\ResourceExists;

use Respect\Validation\Validator as v;
use App\Validation\Validator;

use OpenApi\Annotations as OA;

// necessary imports for the logging functionality
use Psr\Container\ContainerInterface; 
use Laminas\Log\Logger;

class MovieController
{
    protected $movie;
    protected $resource_exists;
    protected $container;
    protected $logger;

    public function __construct(Movie $movie, ResourceExists $resource_exists, ContainerInterface $container)
    {
        $this->movie = $movie;
        $this->resource_exists = $resource_exists;
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
    }

    /**
     * @OA\Get(
     *     path="/v1/movies",
     *     summary="Get all movies",
     *     tags={"Movies"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response containing all movies",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function getAllMovies(Request $request, Response $response): Response
    {
        $all_data = $this->movie->getAll();

        if (isset($all_data['error'])) {
            $error = ['error' => $all_data['error']];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode($all_data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        
    }


     /**
     * @OA\Get(
     *     path="/v1/movies/{id}",
     *     summary="Get a movie by its ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the movie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response containing the movie",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function getMovieById(Request $request, Response $response, array $args): Response
    {
        $id = htmlspecialchars($args['id']);

        $single_data = $this->movie->getById($id); 

        if (isset($single_data['error'])) {
            $error = ['error' => $single_data['error']];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $response->getBody()->write(json_encode($single_data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * @OA\Post(
     *     path="/v1/movies",
     *     summary="Create a new movie",
     *     tags={"Movies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string", example="Die Hard"),
     *                 @OA\Property(property="year", type="string", example="1988"),
     *                 @OA\Property(property="released", type="string", example="20 Jul 1988"),
     *                 @OA\Property(property="runtime", type="string", example="132 min"),
     *                 @OA\Property(property="genre", type="string", example="Action, Thriller"),
     *                 @OA\Property(property="director", type="string", example="John McTiernan"),
     *                 @OA\Property(property="actors", type="string", example="Bruce Willis, Alan Rickman, Bonnie Bedelia"),
     *                 @OA\Property(property="country", type="string", example="United States"),
     *                 @OA\Property(property="poster", type="string", example="https://m.media-amazon.com/images/M/MV5BZjRlNDUxZjAtOGQ4OC00OTNlLTgxNmQtYTBmMDgwZmNmNjkxXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg"),
     *                 @OA\Property(property="imdb", type="string", example="8.2"),
     *                 @OA\Property(property="type", type="string", example="movie"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with movie creation status",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request or invalid JSON data",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function createMovie(Request $request, Response $response): Response
    {
        // Get the JSON content from the request body
        $jsonBody = $request->getBody();
        $data = json_decode($jsonBody, true);

        // Check if JSON decoding was successful
        if ($data === null) {
            // Invalid JSON data
            $errorResponse = array("error-message" => "Invalid JSON data");
            $response->getBody()->write(json_encode($errorResponse));

            $this->logger->info('Status 400: Invalid JSON data (Bad request).');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Get the values from the decoded JSON data and sanitize
        $title = htmlspecialchars($data['title']);
        $year = htmlspecialchars($data['year']);
        $released = htmlspecialchars($data['released']);
        $runtime = htmlspecialchars($data['runtime']);
        $genre = htmlspecialchars($data['genre']);
        $director = htmlspecialchars($data['director']);
        $actors = htmlspecialchars($data['actors']);
        $country = htmlspecialchars($data['country']);
        $poster = htmlspecialchars($data['poster']);
        $imdb = htmlspecialchars($data['imdb']);
        $type = htmlspecialchars($data['type']);

        // Lets instantiate Validator and CustomResponse classes
        $validator = new Validator();
        $customResponse = new CustomResponse();

        // It starts by validating the input data using the $validator.
        $validator->validate($request,[
            "title" => v::notEmpty(),
            "year" => v::notEmpty()->intVal(),
            "released" => v::notEmpty(),
            "runtime" => v::notEmpty(),
            "genre" => v::notEmpty(),
            "director" => v::notEmpty(),
            "actors" => v::notEmpty(),
            "country" => v::notEmpty(),
            "poster" => v::notEmpty()->url(),
            "imdb" => v::notEmpty(),
            "type" => v::notEmpty()->in(['movie', 'series'])
        ]);

        // If validation fails, the method returns a 400 error response .
        if($validator->failed())
        {
            $responseMessage = $validator->errors;

            $this->logger->info('Status 400: Failed validation (Bad request).');
            return $customResponse->is400Response($response,$responseMessage);
        }

        // Call the model's addData() method to create the post
        $isMovieAdded = $this->movie->addData($title, $year, $released, $runtime, $genre, $director, $actors, $country, $poster, $imdb, $type);

        if (!$isMovieAdded) {
            $errorResponse = array(
                "status" => 500,
                "message" => "An internal server error occurred while processing your request."
            );
            $this->logger->error('Status 500: Failed to insert new movie.');
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    
        // Prepare the response data
        $responseData = array(
            "success" => true,
            "message" => "New movie inserted successfully.",
        );

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);


    } 
   /**
     * @OA\Patch(
     *     path="/v1/movies/edit/{id}",
     *     summary="Update all or a part of a specific movies by its ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Updated Movie Name"),
     *                 @OA\Property(property="description", type="string", example="Updated Movie Description"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with movie update status",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request or invalid JSON data",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found with this ID",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function patchMovie(Request $request, Response $response, array $args): Response
    {
         // Get the id from the URL parameters
         $id = htmlspecialchars($args['id']);
         // $id = $request->getAttribute('id');
 
         // Get the JSON content from the request body
         $jsonBody = $request->getBody();
         $data = json_decode($jsonBody, true);
 
         // Check if JSON decoding was successful
         if ($data === null) {
             $errorResponse = array("error-message" => "Invalid JSON data");
 
             $this->logger->info('Status 400: Invalid JSON data (Bad request).');
             return CustomResponse::respondWithError($response, $errorResponse, 400);
         }
        
         // Check if the resource ID exists for posts
         if (!$this->resource_exists->resourceExists($id)) {
             $errorResponse = array(
                 "status" => 404,
                 "message" => "Resource not found with this ID.",
                 "resource-id" => $id
             );
 
             $this->logger->info('Status 404: Resource not found with this ID.');
             return CustomResponse::respondWithError($response, $errorResponse, 404);
         }
 
         // Update the post data using the model method
         $isDataUpdated = $this->movie->patchData($id, $data);
 
         // Prepare the response data
         $responseData = array(
             "success" => $isDataUpdated,
             "message" => $isDataUpdated ? "Data updated successfully." : "Failed to update data."
         );
 
         return CustomResponse::respondWithData($response, $responseData, $isDataUpdated ? 200 : 500);
    }

    /**
     * @OA\Put(
     *     path="/v1/movies/edit/{id}",
     *     summary="Update all data of a specific movie by its ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Movie ID",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="title", type="string", example="Updated Movie Title"),
     *                 @OA\Property(property="year", type="string", example="Updated Year"),
     *                 @OA\Property(property="released", type="string", example="Updated Released"),
     *                 @OA\Property(property="runtime", type="string", example="Updated Runtime"),
     *                 @OA\Property(property="genre", type="string", example="Updated Genre"),
     *                 @OA\Property(property="director", type="string", example="Updated Director"),
     *                 @OA\Property(property="actors", type="string", example="Updated Actors"),
     *                 @OA\Property(property="country", type="string", example="Updated Country"),
     *                 @OA\Property(property="poster", type="string", example="https://example.com/poster.jpg"),
     *                 @OA\Property(property="imdb", type="string", example="Updated IMDB"),
     *                 @OA\Property(property="type", type="string", example="Updated Type")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with movie update status",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request or invalid JSON data",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found with this ID",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function putMovie(Request $request, Response $response, array $args): Response
    {
        // Get the id from the URL parameters
        $id = htmlspecialchars($args['id']);

        // Get the JSON content from the request body
        $jsonBody = $request->getBody();
        $data = json_decode($jsonBody, true);

        // Check if JSON decoding was successful
        if ($data === null) {
            $errorResponse = array("error-message" => "Invalid JSON data");
            $this->logger->info('Status 400: Invalid JSON data (Bad request).');
            return CustomResponse::respondWithError($response, $errorResponse, 400);
        }

        // Get the values from the decoded JSON data and sanitize
        $title = htmlspecialchars($data['title']);
        $year = htmlspecialchars($data['year']);
        $released = htmlspecialchars($data['released']);
        $runtime = htmlspecialchars($data['runtime']);
        $genre = htmlspecialchars($data['genre']);
        $director = htmlspecialchars($data['director']);
        $actors = htmlspecialchars($data['actors']);
        $country = htmlspecialchars($data['country']);
        $poster = htmlspecialchars($data['poster']);
        $imdb = htmlspecialchars($data['imdb']);
        $type = htmlspecialchars($data['type']);

        // Lets instantiate Validator and CustomResponse classes
        $validator = new Validator();
        $customResponse = new CustomResponse();

        // It starts by validating the input data using the $validator.
        $validator->validate($request, [
            "title" => v::notEmpty(),
            "year" => v::notEmpty()->intVal(),
            "released" => v::notEmpty(),
            "runtime" => v::notEmpty(),
            "genre" => v::notEmpty(),
            "director" => v::notEmpty(),
            "actors" => v::notEmpty(),
            "country" => v::notEmpty(),
            "poster" => v::notEmpty()->url(),
            "imdb" => v::notEmpty(),
            "type" => v::notEmpty()->in(['movie', 'series'])
        ]);

        // If validation fails, the method returns a 400 error response .
        if ($validator->failed()) {
            $responseMessage = $validator->errors;
            $this->logger->info('Status 400: Failed validation (Bad request).');
            return $customResponse->is400Response($response, $responseMessage);
        }

        // Check if the movie exists
        if (!$this->resource_exists->resourceExists($id)) {
            $errorResponse = array(
                "status" => 404,
                "message" => "Movie not found with this ID.",
                "movie-id" => $id
            );

            $this->logger->info('Status 404: Movie not found with this ID.');
            return CustomResponse::respondWithError($response, $errorResponse, 404);
        }

        // Update the movie data using the model method
        $isDataUpdated = $this->movie->putData($id, $title, $year, $released, $runtime, $genre, $director, $actors, $country, $poster, $imdb, $type);

        // Prepare the response data
        $responseData = array(
            "success" => $isDataUpdated,
            "message" => $isDataUpdated ? "Data updated successfully." : "Failed to update movie."
        );

        return CustomResponse::respondWithData($response, $responseData, $isDataUpdated ? 200 : 500);
    }

     /**
     * @OA\Delete(
     *     path="/v1/movies/{id}",
     *     summary="Delete a specific movie by its ID",
     *     tags={"Movies"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the movie",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with delete status",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     )
     * )
     */
    public function deleteMovie(Request $request, Response $response, array $args): Response
    {
        $id = htmlspecialchars($args['id']);

        // Check if the resource exists for movies using the model method
        if (!$this->resource_exists->resourceExists($id)) {
            $errorResponse = array(
                "status" => 404,
                "message" => "Resource not found with this ID.",
                "resource-id" => $id
            );

            $this->logger->info('Status 404: Resource not found with this ID.');
            return CustomResponse::respondWithError($response, $errorResponse, 404);
        }

        // Delete the post using the model method
        $isDataDeleted = $this->movie->deleteData($id);

        // Prepare the response data
        $responseData = array(
            "success" => $isDataDeleted,
            "message" => $isDataDeleted ? "Data deleted successfully." : "Failed to delete data."
        );

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($isDataDeleted ? 200 : 500);
    }

    /**
 * @OA\Get(
 *     path="/v1/movies",
 *     summary="Get list of movies with pagination",
 *     tags={"Movies"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="Page number",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="perPage",
 *         in="query",
 *         required=false,
 *         description="Number of movies per page",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response containing the list of movies",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *     )
 * )
 */
public function listMovies(Request $request, Response $response): Response
{
    $page = $request->getQueryParams()['page'] ?? 1;
    $perPage = $request->getQueryParams()['perPage'] ?? 10; // Default per page

    // Query the database to fetch movies with pagination
    $movies = $this->movie->findByNumberPerPage($perPage, $page);

    // Check if there was an error fetching movies
    if (isset($movies['error'])) {
        $errorResponse = [
            "error" => $movies['error']
        ];
        return $response->withJson($errorResponse, 500);
    }

    // Prepare the successful response
    $responseData = [
        "movies" => $movies
    ];

    return $response->withJson($responseData, 200);
}
/**
     * @OA\Get(
     *     path="/v1/movies/{numberPerPage}/sort/{fieldToSort}",
     *     description="Get list of {numberPerPage} existing movies sorted by {fieldToSort}",
     *     @OA\Parameter(
     *         description="Number of movies to display",
     *         in="path",
     *         name="numberPerPage",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="Field to use for sorting the movies",
     *         in="path",
     *         name="fieldToSort",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movies response"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

    public function sortOfMovie(Request $request, Response $response, array $args): Response
     {
         // Get query parameters
         $params = $request->getQueryParams();
         $sortBy = isset($params['sort_by']) ? $params['sort_by'] : null;
         $order = isset($params['order']) ? $params['order'] : 'asc';
     
         // Check if sort_by parameter is provided
         if (!$sortBy) {
             $errorResponse = [
                 "error" => "Sort parameter 'sort_by' is required."
             ];
             return $response->withJson($errorResponse, 400);
         }
     
         // Validate sorting order
         if ($order !== 'asc' && $order !== 'desc') {
             $errorResponse = [
                 "error" => "Invalid sorting order. Use 'asc' or 'desc'."
             ];
             return $response->withJson($errorResponse, 400);
         }
     
         // Query the database to fetch sorted movies
         $sortedMovies = $this->movie->sortBy($sortBy, $order);
     
         // Check if there was an error fetching sorted movies
         if (isset($sortedMovies['error'])) {
             $errorResponse = [
                 "error" => $sortedMovies['error']
             ];
             return $response->withJson($errorResponse, 500);
         }
     
         // Prepare the successful response
         $responseData = [
             "sorted_movies" => $sortedMovies
         ];
     
         return $response->withJson($responseData, 200);
     }


}