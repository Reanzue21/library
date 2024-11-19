# Library Management System

A library management system using Slim framework, JWT for authentication, and a MySQL database.

## Requirements

- PHP 7.x or 8.x
- Composer
- MySQL

## Installation

1. Clone the repository:
    ```sh
    git clone <repository_url>
    cd <repository_directory>
    ```

2. Install dependencies:
    ```sh
    composer install
    ```

3. Set up the MySQL database:
    ```sql
    CREATE DATABASE library;
    ```

4. Update the database credentials in the PHP code:
    ```php
    $servername = "127.0.0.1";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "library";
    ```

## API Endpoints

### User Registration

- **Endpoint:** `/user/register`
- **Method:** `POST`
- **Description:** Registers a new user in the system.
- **Payload:**
    ```json
    {
        "username": "exampleUser",
        "password": "examplePassword"
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
        "username": "exampleUser",
        "password": "examplePassword"
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
        "bookTitle": "Book Title",
        "authorName": "Author Name"
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
        "bookTitle": "Updated Book Title",
        "authorId": 1,
        "authorName": "Updated Author Name"
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
        "username": "exampleUser",
        "newPassword": "newExamplePassword"
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
```sh
php -S localhost:8000 -t public
