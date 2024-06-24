<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Pecee\SimpleRouter\SimpleRouter;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

session_start();

function logMessage($message) {
    error_log($message);
}

SimpleRouter::get('/', function () {
    // Check if the user is authenticated
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit();
    }

   include './pages/index.php';
});

SimpleRouter::post('/login', function () {
        // Validate credentials (replace with your authentication logic)
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
 
        // Replace with your authentication logic (e.g., check against a database)
        if ($email && $password) {
            // Authentication successful, create a session
            $_SESSION['user'] = $email;

            $managementToken = getManagementApiToken();
            
            $_SESSION['ticketId'] = getSessionTicket($managementToken, $email);
            
            header('Location: /');
            exit();
        } else {
            // Authentication failed, show an error (you might want to implement this part)
            echo "Invalid email or password". $email." ". $password;
        }
 });


SimpleRouter::match(['get', 'post'],'/login', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     
        // Validate credentials (replace with your authentication logic)
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
 
        // Replace with your authentication logic (e.g., check against a database)
        if ($email && $password ) {
            // Authentication successful, create a session
            $_SESSION['user'] = $email;
            $managementToken = getManagementApiToken();
            $_SESSION['ticketId'] = getSessionTicket($managementToken, $email);
            header('Location: /');
            exit();
        } else {
            // Authentication failed, show an error (you might want to implement this part)
            echo "Invalid email or password". $email." ". $password;
        }
    }
     echo file_get_contents('./pages/login.html');
     exit();
     
 });


SimpleRouter::get('/logout', function () {
    session_destroy();
    header('Location: /login');
    exit();
});



function getManagementApiToken() {
    $client = new Client();
    $headers = ['Content-Type' => 'application/json'];

    $body = json_encode([
        'clientId' => $_ENV['ASTRATO_CLIENT_ID'],
        'clientSecret' => $_ENV['ASTRATO_CLIENT_SECRET']
    ]);

    $url = $_ENV['ASTRATO_URL'] . 'auth/proxy/m2m/token';

    $request = new Request('POST', $url, $headers, $body);

    $res = $client->send($request);
    $result =  json_decode($res->getBody(), true)['access_token'];
    return $result;
}

function getSessionTicket($accessToken, $email, $groupIds = []) {
    $client = new Client();
    $headers = [
      'Content-Type' => 'application/json',
      'Authorization' => "Bearer $accessToken"
    ];
    $body = json_encode([
      "email" => $email,
      "groupIds" => $groupIds
    ]);

    $request = new Request('POST', $_ENV['ASTRATO_URL'] . 'oem/setup', $headers, $body);
    $res = $client->sendAsync($request)->wait();
    $result =  json_decode($res->getBody(), true);
    return $result['ticket'];
}


SimpleRouter::post('/external-relogin', function () {
    header('Content-Type: application/json');
    if($_SESSION['user']){
        $managementToken = getManagementApiToken();
        echo json_encode(['ticketId' => getSessionTicket($managementToken, $_SESSION['user'])]);
    }else{
        header("HTTP/1.1 401 Unauthorized");
    }

    exit();
});


// Start the routing
SimpleRouter::start();