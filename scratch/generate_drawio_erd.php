<?php

$entities = [
    // Induk (Putih)
    'UNIVERSITAS' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['varchar', 'nama', ''], ['text', 'img', '']], 'x' => 520, 'y' => 40, 'w' => 160],
    'FAKULTAS' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['varchar', 'nama', ''], ['int', 'id_universitas', 'FK']], 'x' => 520, 'y' => 150, 'w' => 160],
    'PRODI' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['varchar', 'nama', ''], ['varchar', 'jenjang', ''], ['tinyint', 'is_aptikom', ''], ['int', 'id_fakultas', 'FK']], 'x' => 520, 'y' => 260, 'w' => 160],
    'USERS' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['varchar', 'name', ''], ['varchar', 'email', ''], ['varchar', 'jabatan', ''], ['int', 'id_prodiUser', 'FK']], 'x' => 180, 'y' => 380, 'w' => 170],
    'USER_OTORITAS' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['int', 'user_id', 'FK'], ['varchar', 'otoritas', ''], ['tinyint', 'active', '']], 'x' => 180, 'y' => 540, 'w' => 170],
    'KURIKULUMS' => ['type' => 'white', 'fields' => [['int', 'id', 'PK'], ['int', 'tahun', ''], ['int', 'id_prodi', 'FK']], 'x' => 520, 'y' => 410, 'w' => 160],

    // Modul Pemetaan (Biru)
    'PROFIL_LULUSAN' => ['type' => 'blue', 'fields' => [['int', 'id', 'PK'], ['varchar', 'kode', ''], ['varchar', 'namaProfil', ''], ['text', 'deskripsi', ''], ['int', 'kurikulum_id', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 40, 'y' => 600, 'w' => 180],
    'CPLS' => ['type' => 'blue', 'fields' => [['int', 'id', 'PK'], ['varchar', 'kode', ''], ['varchar', 'nomor', ''], ['varchar', 'aspek', ''], ['text', 'judul', ''], ['int', 'id_kurikulum', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 280, 'y' => 600, 'w' => 180],
    'BKS' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['varchar', 'kode', ''], ['varchar', 'nama', ''], ['varchar', 'rumpun', ''], ['int', 'kurikulum_id', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 520, 'y' => 600, 'w' => 180],
    'MKS' => ['type' => 'blue', 'fields' => [['varchar', 'kode', 'PK'], ['varchar', 'nama', ''], ['varchar', 'rumpun', ''], ['int', 'semester', ''], ['int', 'bobot_teori', ''], ['int', 'bobot_praktikum', ''], ['int', 'id_kurikulum', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 760, 'y' => 600, 'w' => 190],

    // Matriks Pemetaan (Biru)
    'PROFIL_CPL' => ['type' => 'blue', 'fields' => [['int', 'id', 'PK'], ['int', 'idProfil', 'FK'], ['int', 'idCpl', 'FK'], ['decimal', 'bobot', ''], ['int', 'id_prodi', 'FK']], 'x' => 40, 'y' => 840, 'w' => 170],
    'CPMKS' => ['type' => 'blue', 'fields' => [['int', 'id', 'PK'], ['varchar', 'kode', ''], ['text', 'judul', ''], ['int', 'cpl_id', 'FK'], ['varchar', 'kode_mk', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 250, 'y' => 840, 'w' => 180],
    'BK_CPL' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['bigint', 'bk_id', 'FK'], ['int', 'cpl_id', 'FK']], 'x' => 460, 'y' => 840, 'w' => 150],
    'MK_CPL' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['int', 'cpl_id', 'FK'], ['varchar', 'mk_kode', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 640, 'y' => 840, 'w' => 160],
    'BK_MK' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['bigint', 'bk_id', 'FK'], ['varchar', 'mk_kode', 'FK']], 'x' => 830, 'y' => 840, 'w' => 150],
    'MK_KURIKULUM' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['varchar', 'mk_kode', 'FK'], ['int', 'id_kurikulum', 'FK'], ['varchar', 'semester', ''], ['int', 'id_prodi', 'FK']], 'x' => 1010, 'y' => 840, 'w' => 170],

    // Sub-CPMK & Bobot (Biru)
    'SUB_CPMK' => ['type' => 'blue', 'fields' => [['varchar', 'kode', 'PK'], ['text', 'uraian', ''], ['int', 'cpmk_id', 'FK'], ['int', 'id_prodi', 'FK']], 'x' => 160, 'y' => 1060, 'w' => 170],
    'CPMK_MK' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['int', 'cpmk_id', 'FK'], ['varchar', 'mk_kode', 'FK'], ['float', 'bobot', '']], 'x' => 380, 'y' => 1060, 'w' => 170],
    'MK_SUB_CPMK' => ['type' => 'blue', 'fields' => [['bigint', 'id', 'PK'], ['varchar', 'sub_cpmk_id', 'FK'], ['varchar', 'mk_kode', 'FK']], 'x' => 270, 'y' => 1240, 'w' => 170],
];

$relations = [
    ['UNIVERSITAS', 'FAKULTAS'],
    ['FAKULTAS', 'PRODI'],
    ['PRODI', 'USERS'],
    ['USERS', 'USER_OTORITAS'],
    ['PRODI', 'KURIKULUMS'],

    ['KURIKULUMS', 'PROFIL_LULUSAN'],
    ['PROFIL_LULUSAN', 'PROFIL_CPL'],
    ['CPLS', 'PROFIL_CPL'],

    ['KURIKULUMS', 'CPLS'],
    ['CPLS', 'BK_CPL'],
    ['BKS', 'BK_CPL'],

    ['KURIKULUMS', 'BKS'],
    ['BKS', 'BK_MK'],
    ['MKS', 'BK_MK'],

    ['KURIKULUMS', 'MKS'],
    ['MKS', 'MK_KURIKULUM'],
    ['KURIKULUMS', 'MK_KURIKULUM'],

    ['CPLS', 'MK_CPL'],
    ['MKS', 'MK_CPL'],

    ['CPLS', 'CPMKS'],
    ['MKS', 'CPMK_MK'],
    ['CPMKS', 'CPMK_MK'],

    ['CPMKS', 'SUB_CPMK'],
    ['MKS', 'MK_SUB_CPMK'],
    ['SUB_CPMK', 'MK_SUB_CPMK'],
];

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<mxfile host="app.diagrams.net" modified="' . date('c') . '" agent="Antigravity" version="21.0.0" type="device">' . "\n";
$xml .= '  <diagram id="erd_obe" name="ERD Modul Pemetaan">' . "\n";
$xml .= '    <mxGraphModel dx="1422" dy="794" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="1300" pageHeight="1450" math="0" shadow="0">' . "\n";
$xml .= '      <root>' . "\n";
$xml .= '        <mxCell id="0" />' . "\n";
$xml .= '        <mxCell id="1" parent="0" />' . "\n";

$idCounter = 10;
$entityCellIds = [];

foreach ($entities as $name => $data) {
    $tableId = $idCounter++;
    $entityCellIds[$name] = $tableId;
    $isBlue = $data['type'] === 'blue';

    $headerFill = $isBlue ? '#dae8fc' : '#ffffff';
    $headerStroke = $isBlue ? '#006199' : '#4a5568';
    $fontColor = $isBlue ? '#003366' : '#1a202c';

    $rowHeight = 26;
    $headerHeight = 32;
    $totalHeight = $headerHeight + (count($data['fields']) * $rowHeight);

    // Container Table
    $tableStyle = "swimlane;fontStyle=1;align=center;verticalAlign=top;childLayout=stackLayout;horizontal=1;startSize={$headerHeight};horizontalStack=0;resizeParent=1;resizeParentMax=0;resizeLast=0;collapsible=0;marginBottom=0;html=1;fillColor={$headerFill};strokeColor={$headerStroke};fontColor={$fontColor};fontSize=13;";
    $xml .= "        <mxCell id=\"{$tableId}\" value=\"{$name}\" style=\"{$tableStyle}\" vertex=\"1\" parent=\"1\">\n";
    $xml .= "          <mxGeometry x=\"{$data['x']}\" y=\"{$data['y']}\" width=\"{$data['w']}\" height=\"{$totalHeight}\" as=\"geometry\" />\n";
    $xml .= "        </mxCell>\n";

    // Rows
    foreach ($data['fields'] as $f) {
        $rowId = $idCounter++;
        $type = $f[0];
        $colName = $f[1];
        $key = $f[2];

        $keyDisplay = $key ? " [{$key}]" : "";
        $val = htmlspecialchars("{$type} {$colName}{$keyDisplay}");
        $rowStyle = "text;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;spacingLeft=8;spacingRight=4;overflow=hidden;rotatable=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontColor=#2d3748;fontSize=11;fontFamily=monospace;";

        $xml .= "        <mxCell id=\"{$rowId}\" value=\"{$val}\" style=\"{$rowStyle}\" vertex=\"1\" parent=\"{$tableId}\">\n";
        $xml .= "          <mxGeometry y=\"0\" width=\"{$data['w']}\" height=\"{$rowHeight}\" as=\"geometry\" />\n";
        $xml .= "        </mxCell>\n";
    }
}

// Connectors (Crow's Foot, without text)
foreach ($relations as $rel) {
    $edgeId = $idCounter++;
    $sourceName = $rel[0];
    $targetName = $rel[1];

    $sourceId = $entityCellIds[$sourceName];
    $targetId = $entityCellIds[$targetName];

    // ERone to ERmany (Crow's Foot notation)
    $edgeStyle = "edgeStyle=orthogonalEdgeStyle;rounded=0;orthogonalLoop=1;jettySize=auto;html=1;startArrow=ERone;startFill=0;endArrow=ERmany;endFill=0;strokeColor=#4a5568;strokeWidth=1.5;";

    $xml .= "        <mxCell id=\"{$edgeId}\" style=\"{$edgeStyle}\" edge=\"1\" parent=\"1\" source=\"{$sourceId}\" target=\"{$targetId}\">\n";
    $xml .= "          <mxGeometry relative=\"1\" as=\"geometry\" />\n";
    $xml .= "        </mxCell>\n";
}

$xml .= '      </root>' . "\n";
$xml .= '    </mxGraphModel>' . "\n";
$xml .= '  </diagram>' . "\n";
$xml .= '</mxfile>' . "\n";

file_put_contents('erd_pemetaan_putih_biru.drawio', $xml);
echo "File erd_pemetaan_putih_biru.drawio berhasil dibuat!\n";

