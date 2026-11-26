<?php
/*
 * Enregistre une composition Infoboard dans un dossier existant du site.
 * Utilisé par infoboard-editeur.html?c=mon-dossier/ (bouton « Enregistrer »).
 *
 * Écrit dans le dossier : composition.json, composition.js et les nouvelles images (images/).
 * Les images de images/ qui ne sont plus utilisées sont supprimées ; le reste du dossier
 * (par exemple fichiers/) n'est jamais touché.
 *
 * Outil de travail local : ne publiez pas ce fichier sur un serveur accessible à d'autres personnes.
 */
declare(strict_types=1);

const IMAGE_EXT = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'avif', 'bmp'];

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function fail(int $code, string $message): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function writeAtomic(string $path, string $data): void {
    $tmp = $path . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (file_put_contents($tmp, $data) === false || !rename($tmp, $path)) {
        @unlink($tmp);
        fail(500, 'Écriture impossible : ' . basename($path) . ' (droits du dossier ?).');
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') fail(405, 'Méthode non autorisée.');
// En-tête personnalisé : une page d'un autre site ne peut pas l'envoyer sans autorisation préalable (CORS).
if (($_SERVER['HTTP_X_INFOBOARD'] ?? '') !== '1') fail(403, 'Requête refusée.');
if (!$_POST && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    fail(413, 'Envoi trop volumineux. Relancez PHP avec : php -d upload_max_filesize=64M -d post_max_size=256M -S localhost:8000');
}

// Dossier cible : existant, sous la racine du site, contenant déjà composition.json
$c = (string)($_POST['c'] ?? '');
if ($c === '' || preg_match('~[\\\\:\0]|^/|(^|/)\.\.?(/|$)~', $c)) fail(400, 'Dossier invalide.');
$root = realpath(__DIR__);
$dir = realpath($root . '/' . $c);
if ($dir === false || !is_dir($dir) || $dir === $root || !str_starts_with($dir, $root . DIRECTORY_SEPARATOR)) {
    fail(404, 'Dossier introuvable : ' . $c);
}
if (!is_file($dir . '/composition.json')) fail(404, 'Ce dossier ne contient pas de composition.json.');

$json = (string)($_POST['composition'] ?? '');
$doc = json_decode($json, true);
if (!is_array($doc) || ($doc['format'] ?? '') !== 'infoboard' || !is_array($doc['elements'] ?? null)) {
    fail(400, 'Composition invalide.');
}

$validName = fn(string $n): bool => (bool)preg_match('~^[\p{L}\p{N}][\p{L}\p{N}._ -]{0,150}$~u', $n)
    && in_array(strtolower(pathinfo($n, PATHINFO_EXTENSION)), IMAGE_EXT, true);

// Images utilisées par la composition (chemins relatifs images/xxx)
$keep = [];
foreach ($doc['elements'] as $el) {
    if (!is_array($el) || ($el['type'] ?? '') !== 'image' || !is_string($el['src'] ?? null)) continue;
    foreach ([$el['src'], rawurldecode($el['src'])] as $src) {
        if (preg_match('~^(?:\./)?images/([^/]+)$~', $src, $m)) $keep[$m[1]] = true;
    }
}

$imagesDir = $dir . '/images';
$uploads = $_FILES['images'] ?? null;
$written = 0;
if ($uploads && is_array($uploads['name'])) {
    if (!is_dir($imagesDir) && !mkdir($imagesDir, 0775) && !is_dir($imagesDir)) fail(500, 'Création de images/ impossible.');
    foreach ($uploads['name'] as $i => $name) {
        $name = basename((string)$name);
        if ($uploads['error'][$i] !== UPLOAD_ERR_OK) {
            fail(413, "Image non reçue : $name (taille limitée par upload_max_filesize ; relancez PHP avec -d upload_max_filesize=64M -d post_max_size=256M).");
        }
        if (!$validName($name)) fail(400, "Nom d'image refusé : $name");
        if (!move_uploaded_file($uploads['tmp_name'][$i], $imagesDir . '/' . $name)) fail(500, "Écriture impossible : images/$name");
        $keep[$name] = true;
        $written++;
    }
}

writeAtomic($dir . '/composition.json', $json);
writeAtomic($dir . '/composition.js', "/* Composition Infoboard (lecture sans serveur web) */\nwindow.INFOBOARD_COMPOSITION = $json;\n");

$removed = 0;
if (is_dir($imagesDir)) {
    foreach (scandir($imagesDir) ?: [] as $name) {
        $path = $imagesDir . '/' . $name;
        if (is_file($path) && !isset($keep[$name]) && $validName($name) && unlink($path)) $removed++;
    }
}

echo json_encode(['ok' => true, 'written' => $written, 'removed' => $removed]);
