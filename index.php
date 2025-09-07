<?php

// Load le fichier de configuration
$config = json_decode(file_get_contents(__DIR__ . '/config.json'));

// Vérifier l'origine de la requête
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && in_array($origin, $config->allowed, true)) {
	header("Access-Control-Allow-Origin: $origin");
	header('Vary: Origin');
} else {
	header('Access-Control-Allow-Credentials: true');
}

// Les méthodes qu'on accepte
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	http_response_code(204);
	exit;
}

// Authentification
if(!isset($_SERVER['PHP_AUTH_USER'])) {
	header('HTTP/1.0 404 Tu cherches le trouble?');
	include(__DIR__ . '/404.php');
	exit;
} elseif($_SERVER['PHP_AUTH_USER'] !== 'admin' || $_SERVER['PHP_AUTH_PW'] !== $config->KEY) {
	header('HTTP/1.0 401 Vous ne passerez pas!');
	exit;
}

// Une image est-elle présente?
if(empty($_FILES['image'])) {
	header('HTTP/1.0 400 As-tu oublié quelque chose?');
	exit;
}

// Est-ce qu'il y a eu une erreur d'upload?
if($_FILES['image']['error']) {
	header('HTTP/1.0 400 Pouvez-vous répéter la question?');
	exit;
}

// Est-ce que le fichier est vide?
if(!$_FILES['image']['size']) {
	header('HTTP/1.0 400 Ta boîte est vide.');
	exit;
}

// Est-ce que le fichier dépasse la grosseur maximale?
if($_FILES['image']['size'] > $config->maxsize) {
	header('HTTP/1.0 422 Mais elle est bien trop grosse!');
	exit;
}

// Est-ce que le fichier est un type d'image accepté?
if(!in_array($_FILES['image']['type'], $config->accepted)) {
	header('HTTP/1.0 422 Drôle de format d\'image...');
	exit;
}

// Vérifier l'accessibilité du fichier temporaire
if(!is_file($_FILE['image']['tmp_name'])) {
	header('HTTP/1.0 500 Woups j\'ai perdu le fichier...');
	exit;
}

	


print_r($_FILES);
