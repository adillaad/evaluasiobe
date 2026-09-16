<?php
require_once __DIR__ . '/test_academic_breakdown.php';

use App\Models\Mahasiswa;

$nadine = Mahasiswa::where('NPM', '2217051049')->first();
$resNadine = getStudentAcademicBreakdown($nadine);
echo "=== BREAKDOWN NADINE ===\n";
echo json_encode($resNadine, JSON_PRETTY_PRINT);
