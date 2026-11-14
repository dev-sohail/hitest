<?php
declare(strict_types=1);

// Load environment variables (simple .env loader, no composer needed)
function load_env(string $path): void {
    if (!is_file($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key !== '' && getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

load_env(__DIR__ . '/.env');

$appName = getenv('APP_NAME') ?: 'hitest';
$uploadDir = __DIR__ . '/uploads';

// Handle file upload
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $safeName = basename($file['name']);
        $target = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $message = "Uploaded: " . htmlspecialchars($safeName, ENT_QUOTES, 'UTF-8');
        } else {
            $message = "Upload failed.";
        }
    } else {
        $message = "Upload error code: " . $file['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>

    <h1>This text is entered around 11:16PM Nov14</h1>
    <h1><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if ($message !== ''): ?>
        <p><strong><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Uploaded files</h2>
    <ul id="file-list">
        <?php
        if (is_dir($uploadDir)) {
            foreach (array_diff(scandir($uploadDir), ['.', '..', '.gitkeep']) as $f) {
                echo '<li>' . htmlspecialchars($f, ENT_QUOTES, 'UTF-8') . '</li>';
            }
        }
        ?>
    </ul>

    <script src="script.js"></script>
</body>
</html>
