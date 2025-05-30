<?php
/**
 * Vérifie que les fichiers PHP dans src/ ont un namespace cohérent avec leur emplacement.
 */

$baseDir = __DIR__ . '/src';
$baseNamespace = 'App';

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($baseDir));
$errors = [];

foreach ($rii as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $filePath = $file->getRealPath();
    $relativePath = substr($filePath, strlen($baseDir) + 1); // Chemin relatif à src/
    $relativePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
    $relativePath = str_replace('.php', '', $relativePath);

    $expectedNamespace = $baseNamespace . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

    // Lire la déclaration de namespace
    $contents = file_get_contents($filePath);
    if (preg_match('/namespace\s+([^;]+);/', $contents, $matches)) {
        $declaredNamespace = trim($matches[1]);
        $expectedPath = dirname($expectedNamespace);
        $declaredPath = dirname($declaredNamespace . '\\dummy');

        if ($declaredPath !== $expectedPath) {
            $errors[] = [
                'file' => $filePath,
                'expected' => $expectedPath,
                'found' => $declaredPath,
            ];
        }
    } else {
        $errors[] = [
            'file' => $filePath,
            'expected' => '(un namespace)',
            'found' => '(aucun trouvé)',
        ];
    }
}

if (empty($errors)) {
    echo "✅ Tous les namespaces sont corrects.\n";
} else {
    echo "❌ Incohérences de namespace détectées :\n\n";
    foreach ($errors as $error) {
        echo "- Fichier : {$error['file']}\n";
        echo "  Attendu : {$error['expected']}\n";
        echo "  Trouvé  : {$error['found']}\n\n";
    }
    exit(1);
}
