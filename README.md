# Library Management System

An API-based application that allows users to register, authenticate, and manage a collection of books in a library. Users can add new books to the system and view the list of books. The system employs JWT (JSON Web Token) for secure user authentication and token-based authorization to control access to specific endpoints.

## Software and Technologies

- **PHP:** Server-side scripting language.
- **Slim Framework:** A micro-framework for building RESTful APIs.
- **JWT (JSON Web Token):** For secure user authentication and authorization.
- **MySQL:** Database for storing user and book information.
- **JSON:** For data exchange in API requests and responses.

## Main Highlights

### Dependencies and Configuration

- The project makes use of the Slim framework and Firebase JWT for authentication.
- Dependencies are managed through the `composer.json` file, and necessary classes are loaded with `vendor/autoload.php`.

### JWT Middleware

- A middleware is in place to ensure that all requests are authenticated using a valid JWT token.
- The middleware validates the token and extracts the user's information for further request processing.
- Invalid or reused tokens are denied to uphold security standards.

### Token Generation

- The `generateToken()` function creates a JWT token that expires after one hour.
- The token payload includes the user's ID and username for authorization purposes.

## Requirements

- PHP 7.x or 8.x
- Composer
- MySQL

## API Endpoints

### User Registration

- **Endpoint:** `/user/register`
- **Method:** `POST`
- **Description:** Registers a new user in the system.
- **Payload:**
    ```json
    {
        "username": "Reanzue",
        "password": "Password"
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Error message"
            }
        }
        ```

### User Authentication

- **Endpoint:** `/user/authenticate`
- **Method:** `POST`
- **Description:** Authenticates a user and provides a JWT token.
- **Payload:**
    ```json
    {
        "username": "Reanzue",
        "password": "Password"
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success",
            "token": "jwt_token"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Authentication Failed"
            }
        }
        ```

### Insert Book-Author

- **Endpoint:** `/book-author/insert`
- **Method:** `POST`
- **Headers:** 
    ```http
    Authorization: Bearer jwt_token
    ```
- **Description:** Inserts a new book and author record into the system and links them.
- **Payload:**
    ```json
    {
        "bookTitle": "Mathematics",
        "authorName": "Albert"
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success",
            "token": "new_jwt_token"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Error message"
            }
        }
        ```

### Update Book-Author

- **Endpoint:** `/book-author/update`
- **Method:** `PUT`
- **Headers:** 
    ```http
    Authorization: Bearer jwt_token
    ```
- **Description:** Updates existing book and author records in the system.
- **Payload:**
    ```json
    {
        "bookId": 1,
        "bookTitle": "English",
        "authorId": 1,
        "authorName": "Mark"
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success",
            "token": "new_jwt_token"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Error message"
            }
        }
        ```

### Delete Book-Author

- **Endpoint:** `/book-author/delete`
- **Method:** `DELETE`
- **Headers:** 
    ```http
    Authorization: Bearer jwt_token
    ```
- **Description:** Deletes a book-author link and the corresponding book and author records from the system.
- **Payload:**
    ```json
    {
        "bookId": 1,
        "authorId": 1
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success",
            "token": "new_jwt_token"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Error message"
            }
        }
        ```

### Change User Password

- **Endpoint:** `/user/change-password`
- **Method:** `POST`
- **Headers:** 
    ```http
    Authorization: Bearer jwt_token
    ```
- **Description:** Changes the password for a user and provides a new JWT token.
- **Payload:**
    ```json
    {
        "username": "Reanzue",
        "newPassword": "Rola"
    }
    ```
- **Response:**
    - On Success:
        ```json
        {
            "status": "success",
            "token": "new_jwt_token"
        }
        ```
    - On Failure:
        ```json
        {
            "status": "fail",
            "data": {
                "title": "Error message"
            }
        }
        ```

## Running the Application

Start the application using the PHP built-in server:
