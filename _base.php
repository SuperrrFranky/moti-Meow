<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(0);
    session_start();
}

// Is GET request?
if (!function_exists('is_get')) {
    function is_get()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
// Is POST request?
if (!function_exists('is_post')) {
    function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}

// Obtain GET parameter
if (!function_exists('get')) {
    function get($key, $value = null)
    {
        $value = $_GET[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }
}

// Obtain POST parameter
if (!function_exists('post')) {
    function post($key, $value = null)
    {
        $value = $_POST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }
}

// Obtain REQUEST (GET and POST) parameter
if (!function_exists('req')) {
    function req($key, $value = null)
    {
        $value = $_REQUEST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }
}

// Redirect to URL
if (!function_exists('redirect')) {
    function redirect($url = null, $delay = 0)
    {
        $url ??= $_SERVER['REQUEST_URI'];

        if ($delay > 0) {
            // Use JavaScript for delayed redirection
            echo "<script>
                setTimeout(function() {
                    window.location.href = '" . htmlspecialchars($url, ENT_QUOTES) . "';
                }, " . ($delay * 1000) . ");
              </script>";
            exit();
        } else {
            // Immediate redirection using header
            header("Location: $url");
            exit();
        }
    }
}
//DataBase Connection
$_db = new PDO(
    'mysql:host=' . ($_ENV['DB_HOST'] ?? '127.0.0.1') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'motimeow'),
    $_ENV['DB_USER'] ?? 'root',
    $_ENV['DB_PASS'] ?? '',
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
//azure open ai function
if (!function_exists('callAzureOpenAI')) {
    function callAzureOpenAI($messages, $max_tokens = 100)
    {
        $azureEndpoint = $_ENV['AZURE_ENDPOINT'] ?? '';
        $apiKey = $_ENV['AZURE_API_KEY'] ?? '';
        
        if (!$azureEndpoint || !$apiKey) {
            return "Error: Azure credentials not configured.";
        }

        $headers = [
            "Content-Type: application/json",
            "api-key: $apiKey"
        ];

        $data = [
            "messages" => $messages,
            "max_tokens" => $max_tokens,
            "temperature" => 0.7,
            "top_p" => 1.0
        ];
        
        $ch = curl_init($azureEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return "Error contacting Azure: " . curl_error($ch);
        }

        curl_close($ch);

        $decoded = json_decode($response, true);
        return $decoded['choices'][0]['message']['content'] ?? "No response from Azure.";
    }
}


// Generate <span class='err'>
if (!function_exists('err')) {
    function err($key)
    {
        global $_err;
        if ($_err[$key] ?? false) {
            echo "<span class='err'>$_err[$key]</span>";
        } else {
            echo '<span></span>';
        }
    }
}

if (!function_exists('temp')) {
    function temp($key, $value = null)
    {
        if ($value !== null) {
            $_SESSION["temp_$key"] = $value;
        } else {
            $value = $_SESSION["temp_$key"] ?? null;
            unset($_SESSION["temp_$key"]);
            return $value;
        }
    }
}

// Generate <input type='file'>
if(!function_exists('html_file')){
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}
}

// Obtain uploaded file --> cast to object
if(!function_exists('get_file')){
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}
}

// Crop, resize and save photo
if(!function_exists('save_photo')){
function save_photo($f, $folder, $width = 200, $height = 200)
{
    $photo = uniqid() . '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}
}

// Encode HTML special characters
if(!function_exists('encode')){
function encode($value)
{
    // TODO
    return htmlentities($value);
}
}

// Generate <input type='text'>
if(!function_exists('html_textarea')){
function html_textarea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='textarea' id='$key' name='$key' value='$value' $attr>";
}
}
