<?php
echo "<pre>";

$paths = [
  '/home/evalua4/bin/wkhtmltopdf',
  '/home/evalua4/evaluasiobe/bin/wkhtmltopdf',
  __DIR__ . '/../bin/wkhtmltopdf',
];

foreach ($paths as $p) {
  echo "PATH: $p\n";
  echo "file_exists: " . (file_exists($p) ? 'YES' : 'NO') . "\n";
  echo "is_executable: " . (is_executable($p) ? 'YES' : 'NO') . "\n";
  echo "version:\n" . shell_exec($p . " --version 2>&1") . "\n";
  echo "-----------------------\n";
}

echo "</pre>";
