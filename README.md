# Library Management API

This project provides a RESTful API for managing a library system. It includes features such as user authentication, book and author management, and token-based security using JWT (JSON Web Tokens).

## Features
- **User Registration**: Register new users with unique usernames.
- **User Authentication**: Authenticate users and generate JWT tokens for session management.
- **Book and Author Management**: Create, update, and delete books and authors, as well as link them together.
- **Password Management**: Update user passwords with secure hashing.
- **Token Security**: Token-based authentication and rotation for secure access to endpoints.

---

## Technology Stack
- **Language**: PHP
- **Framework**: Slim Framework
- **Database**: MySQL
- **JWT**: Firebase JWT Library for token management
- **HTTP**: PSR-7 HTTP Message Interfaces

---

## API Endpoints

### User Endpoints

#### **1. Register User**
- **URL**: `/user/register`
- **Method**: `POST`
- **Description**: Registers a new user with a unique username.
- **Request Body**:
  ```json
  {
    "username": "example_user",
    "password": "example_password"
  }
Response:
200 OK:
json
Copy code
{
  "status": "success"
}
409 Conflict:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Username already exists."
  }
}
2. Authenticate User
URL: /user/authenticate
Method: POST
Description: Authenticates a user and provides a JWT token for session management.
Request Body:
json
Copy code
{
  "username": "example_user",
  "password": "example_password"
}
Response:
200 OK:
json
Copy code
{
  "status": "success",
  "token": "jwt_token_here"
}
401 Unauthorized:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Authentication Failed"
  }
}
3. Change Password
URL: /user/change-password
Method: POST
Headers: Authorization: Bearer <token>
Description: Updates the user's password securely.
Request Body:
json
Copy code
{
  "username": "example_user",
  "newPassword": "new_password"
}
Response:
200 OK:
json
Copy code
{
  "status": "success",
  "token": "new_jwt_token_here"
}
401 Unauthorized:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Invalid Token"
  }
}
Book and Author Endpoints
4. Insert Book and Author
URL: /book-author/insert
Method: POST
Headers: Authorization: Bearer <token>
Description: Inserts a new book and author into the system and links them.
Request Body:
json
Copy code
{
  "bookTitle": "Book Title",
  "authorName": "Author Name"
}
Response:
200 OK:
json
Copy code
{
  "status": "success",
  "token": "new_jwt_token_here"
}
401 Unauthorized:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Invalid Token"
  }
}
5. Update Book and Author
URL: /book-author/update
Method: PUT
Headers: Authorization: Bearer <token>
Description: Updates the details of an existing book and author.
Request Body:
json
Copy code
{
  "bookId": 1,
  "authorId": 2,
  "bookTitle": "Updated Book Title",
  "authorName": "Updated Author Name"
}
Response:
200 OK:
json
Copy code
{
  "status": "success",
  "token": "new_jwt_token_here"
}
401 Unauthorized:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Invalid Token"
  }
}
6. Delete Book and Author
URL: /book-author/delete
Method: DELETE
Headers: Authorization: Bearer <token>
Description: Deletes a book and author record from the system.
Request Body:
json
Copy code
{
  "bookId": 1,
  "authorId": 2
}
Response:
200 OK:
json
Copy code
{
  "status": "success",
  "token": "new_jwt_token_here"
}
401 Unauthorized:
json
Copy code
{
  "status": "fail",
  "data": {
    "title": "Invalid Token"
  }
}
Security
JWT Token:
Tokens are signed using the HS256 algorithm.
Tokens expire after 1 hour.
Tokens are rotated upon sensitive operations (e.g., password change).
Password Hashing:
Passwords are hashed using PHP's password_hash function for secure storage.
