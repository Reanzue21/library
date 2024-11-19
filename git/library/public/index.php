<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require '../src/vendor/autoload.php';
$app = new \Slim\App;

$servername = "127.0.0.1";
$dbusername = "root";
$dbpassword = "";
$dbname = "library";

// Common functions for database connection, token validation, and generation
function getDB($servername, $dbusername, $dbpassword, $dbname)
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function generateToken($userid)
{
    $key = 'server_hack';
    $iat = time();
    $payload = [
        'iss' => 'http://library.org',
        'aud' => 'http://library.com',
        'iat' => $iat,
        'exp' => $iat + 3600,
        'data' => ['userid' => $userid]
    ];
    return JWT::encode($payload, $key, 'HS256');
}

function handleToken($conn, $userid, $newToken)
{
    $conn->prepare("DELETE FROM user_tokens WHERE userid = :userid")->execute(['userid' => $userid]);
    $conn->prepare("INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)")
        ->execute(['userid' => $userid, 'token' => $newToken]);
}

function checkToken(Request $request, $conn)
{
    $token = str_replace('Bearer ', '', $request->getHeader('Authorization')[0] ?? '');
    $stmt = $conn->prepare("SELECT userid FROM user_tokens WHERE token = :token");
    $stmt->execute(['token' => $token]);
    if ($stmt->rowCount() === 0)
        return false;

    $decoded = JWT::decode($token, new Key('server_hack', 'HS256'));
    return $decoded->data->userid;
}

// User registration
$app->post('/user/register', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());

    try {
        $conn = getDB($servername, $dbusername, $dbpassword, $dbname);

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :usr");
        $stmt->execute(['usr' => $data->username]);
        if ($stmt->rowCount() > 0) {
            return $response->withStatus(409)->write(json_encode(["status" => "fail", "data" => ["title" => "Username already exists."]]));
        }

        // Proceed to insert the new user
        $conn->prepare("INSERT INTO users (username, password) VALUES (:usr, :pass)")
            ->execute(['usr' => $data->username, 'pass' => password_hash($data->password, PASSWORD_DEFAULT)]);

        $response->getBody()->write(json_encode(["status" => "success"]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});


// User authentication
$app->post('/user/authenticate', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    try {
        $conn = getDB($servername, $dbusername, $dbpassword, $dbname);
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=:usr");
        $stmt->execute(['usr' => $data->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data->password, $user['password'])) {
            $userid = $user['userid'];
            $newToken = generateToken($userid);
            handleToken($conn, $userid, $newToken);
            $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
        } else {
            $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => "Authentication Failed"]]));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }
    return $response;
});

$app->post('/book-author/insert', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $conn = getDB($servername, $dbusername, $dbpassword, $dbname);


    $userId = checkToken($request, $conn);
    if (!$userId)
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "data" => ["title" => "Invalid Token"]]));

    try {
        $conn->beginTransaction();


        $stmt = $conn->prepare("INSERT INTO books (bookTitle) VALUES (:bookTitle)");
        $stmt->execute(['bookTitle' => $data->bookTitle]);
        $bookId = $conn->lastInsertId();

 
        $stmt = $conn->prepare("INSERT INTO authors (authorName) VALUES (:authorName)");
        $stmt->execute(['authorName' => $data->authorName]);
        $authorId = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO book_authors (bookId, authorId, userId) VALUES (:bookId, :authorId, :userId)");
        $stmt->execute(['bookId' => $bookId, 'authorId' => $authorId, 'userId' => $userId]);

        $conn->commit();

        $newToken = generateToken($userId);
        handleToken($conn, $userId, $newToken);

        $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
    } catch (PDOException $e) {
        $conn->rollBack();
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }
    return $response;
});


// Book-Author update
$app->put('/book-author/update', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $conn = getDB($servername, $dbusername, $dbpassword, $dbname);
    $userid = checkToken($request, $conn);
    if (!$userid)
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "data" => ["title" => "Invalid Token"]]));

    try {
        $conn->prepare("UPDATE books SET bookTitle = :bookTitle WHERE bookId = :bookId")
            ->execute(['bookTitle' => $data->bookTitle, 'bookId' => $data->bookId]);
        $conn->prepare("UPDATE authors SET authorName = :authorName WHERE authorId = :authorId")
            ->execute(['authorName' => $data->authorName, 'authorId' => $data->authorId]);

        $newToken = generateToken($userid);
        handleToken($conn, $userid, $newToken);
        $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }
    return $response;
});

// Book-Author delete
$app->delete('/book-author/delete', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $conn = getDB($servername, $dbusername, $dbpassword, $dbname);
    $userid = checkToken($request, $conn);
    if (!$userid)
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "data" => ["title" => "Invalid Token"]]));

    try {
        $conn->beginTransaction();
        $conn->prepare("DELETE FROM book_authors WHERE bookid = :bookid AND authorid = :authorid")
            ->execute(['bookid' => $data->bookId, 'authorid' => $data->authorId]);
        $conn->prepare("DELETE FROM books WHERE bookId = :bookid")->execute(['bookid' => $data->bookId]);
        $conn->prepare("DELETE FROM authors WHERE authorId = :authorid")->execute(['authorid' => $data->authorId]);

        $conn->commit();

        $newToken = generateToken($userid);
        handleToken($conn, $userid, $newToken);
        $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
    } catch (PDOException $e) {
        $conn->rollBack();
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }
    return $response;
});

// Change user password
$app->post('/user/change-password', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $conn = getDB($servername, $dbusername, $dbpassword, $dbname);

    $userid = checkToken($request, $conn);
    if (!$userid) {
        return $response->withStatus(401)->write(json_encode(["status" => "fail", "data" => ["title" => "Invalid Token"]]));
    }

    try {
        // Validate the new password and username
        if (empty($data->newPassword) || empty($data->username)) {
            return $response->withStatus(400)->write(json_encode(["status" => "fail", "data" => ["title" => "Username and new password cannot be empty."]]));
        }

        // Retrieve the user ID based on the username
        $stmt = $conn->prepare("SELECT userid FROM users WHERE username = :username");
        $stmt->execute(['username' => $data->username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return $response->withStatus(404)->write(json_encode(["status" => "fail", "data" => ["title" => "User not found."]]));
        }

        // Update the user's password
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE userid = :userid");
        $stmt->execute(['password' => password_hash($data->newPassword, PASSWORD_DEFAULT), 'userid' => $user['userid']]);

        // Token rotation
        $newToken = generateToken($user['userid']);
        handleToken($conn, $user['userid'], $newToken); // Handle the new token storage
        $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});


$app->run();

    ?>