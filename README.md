# Movie Detail API

## Table of Contents
- **Getting Started**
  - Prerequisites
  - Installation
- **Usage**
  - Creating a Movie
  - Reading Movies
  - Updating a Movie
  - Deleting a Movie
  - Managing Movies
- **Thumbnail Handling**
- **API Documentation**
- **Contributing**

## Getting Started

### Prerequisites
- PHP (>= 7.4)
- Composer (for dependency management)
- MySQL or compatible database

### Installation

1. Make sure you have PHP installed on your system.
2. Start the phpMyAdmin Apache and MySQL server 
3. Clone the repository to your local machine, inside your xampp/htdocs directory:
   ```
   git clone https://github.com/onniep/movie-detail-api.git
   cd movie-detail-api
   ```
4. Open the repository with your VS Code IDE (or any other IDEs).
5. Open a browser and go to URL http://localhost/phpmyadmin/
6. Then inside your phpMyAdmin, click on the "Databases" tab.
7. Create a database naming “movie_detail_api” and then click on the "Import" tab.
8. Click on "browse file" and select the “movie.sql” file which is inside this project repository, specifically inside the "db" directory.
9. Click on "Go". 
10. Return back to your IDE and inside your terminal install dependencies by running this command:
   ```
   composer update
   ```

## Usage

### Starting the Local Server

To start the local server, navigate to the `public` directory and run the following command:
```
cd public
php -S localhost:200
```
NB: Please use this exact default recommended local server URL provided above, for simplicity sake of this my README content.

### API Testing with Postman

The API can be tested using Postman. 

### API Testing with Swagger

The API can be tested using Swagger UI also. In your browser, by default you can visit this url which I believe is still your current running local server and exact port number: http://localhost:200/docs/


### Creating a Movie
Use the following endpoint to create a new movie:
```
POST /movies
```

Sample JSON request body:
```json
{
    "id": "1",
    "title": "Die Hard",
    "year": "1988",
    "released": "20 Jul 1988",
    "runtime": "132 min",
    "genre": "Action, Thriller",
    "director": "John McTiernan",
    "actors": "Bruce Willis, Alan Rickman, Bonnie Bedelia",
    "country": "United States",
    "poster": "https://m.media-amazon.com/images/M/MV5BZjRlNDUxZjAtOGQ4OC00OTNlLTgxNmQtYTBmMDgwZmNmNjkxXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg",
    "imdb": "8.2",
    "type": "movie"
}

```

### Reading Movies
- Retrieve all movies:
  ```
  GET /movies
  ```

- Retrieve a post by ID:
  ```
  GET /movies/{id}
  ```

Sample JSON pretty-read output:

```json
{
    "uid": "1",
    "title": "Die Hard",
    "year": "1988",
    "released": "20 Jul 1988",
    "runtime": "132 min",
    "genre": "Action, Thriller",
    "director": "John McTiernan",
    "actors": "Bruce Willis, Alan Rickman, Bonnie Bedelia",
    "country": "United States",
    "poster": "https://m.media-amazon.com/images/M/MV5BZjRlNDUxZjAtOGQ4OC00OTNlLTgxNmQtYTBmMDgwZmNmNjkxXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_SX300.jpg",
    "imdb": "8.2",
    "type": "movie"
}
```

- Retrieve a post by id:
  ```
  GET /v1/movies
  ```

### Create new Post
Use the following endpoint to update a movie post:
```
POST /v1/movies
```

### Updating a Post
Use the following endpoint to update a movie post:
```
PATCH /v1/movies/{id}
```
```
PUT /v1/movies/{id}
```

### Deleting a Post
Use the following endpoint to delete a movie:
```
DELETE /v1/movies/{id}
```

### List of Movies
Use the following endpoint to list numbers of movies:
```
GET /v1/movies/{numberPerPage}
```
```
GET /v1/movies/{numberPerPage}/sort/{fieldToSort}
```

## API Documentation
The API endpoints and their usage are documented using Swagger and can be accessed by running the server and visiting `http://localhost:200/docs/`.

## Contributing
Contributions are welcome! Please follow the CONTRIBUTING guidelines.

# movie-detail-api
