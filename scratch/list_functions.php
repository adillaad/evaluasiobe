<?php
$content = file_get_contents('d:/laragon/www/evaluasiobe (1)/evaluasiobe/app/Http/Controllers/PenjaminMutu/VisualisasiController.php');
preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/', $content, $matches, PREG_OFFSET_CAPTURE);
foreach ($matches[1] as $match) {
    $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
    echo "Line {$line}: {$match[0]}\n";
}
