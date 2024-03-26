<?php
//C:\xampp\htdocs\movie-detail-api\routes\movies.php
use App\Controllers\MovieController;
use App\Models\Movie;
use App\Models\ResourceExists;
use App\Models\Database\DbConnect;


// Create a PDO instance for database, and gets the connection
$db = (new DbConnect())->getConn();

// Retrieve the ContainerInterface from the Slim app container
$container = $app->getContainer();

// Inject the PDO instance into the MovieModel model
$movieModel = new Movie($db, $container);
 
// Inject the PDO instance into the ResourceExists model
$resourceExistsModel = new ResourceExists($db);
 
// Inject the Movie model into the controller
$MovieController = new MovieController($movieModel, $resourceExistsModel, $container);
 
// Get all our movies from the database
$app->get('/v1/movies', [$MovieController, 'getAllMovies']);


// Get all a specific movie from the database
$app->get('/v1/movies/{id:\d+}', [$MovieController, 'getMovieById']);

// Create movies from the api to the database
$app->post('/v1/movies', [$MovieController, 'createMovie']);

// Edit/Update a movies from the api to the database, using PUT
$app->put('/v1/movies/{id:\d+}', [$MovieController, 'putMovie']);

// Edit/Update a movies from the api to the database, using PATCH
$app->patch('/v1/movies/{id:\d+}', [$MovieController, 'patchMovie']);

// Delete a movies from the api to the database
$app->delete('/v1/movies/{id:\d+}', [$MovieController, 'deleteMovie']);

// Get list of {numberPerPage} existing movies
$app->get('/v1/movies/{numberPerPage}', [$MovieController, 'listOfMovie']);

// Get list of {numberPerPage} existing movies sorted by {fieldToSort}
$app->get('/v1/movies/{numberPerPage}/sort/{fieldToSort}', [$MovieController, 'sortOfMovie']);



