<?php

namespace App\Imports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\ToModel;
class ActivitiesImport implements ToModel {
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        $ind = explode(";",$row[2]);
        $ind[0] .= " ".$row[3];
        $ind[1] .= " ".$row[3];
        $indikator = "<ul> <li> ".$ind[0]." </li> <li> ".$ind[1]." </li> </ul>";
            return new Activity([
                'minggu'     => $row[0],
                'sub_cpmk'    => $row[1],
                'indikator' => $indikator,
                'materi' => $row[3],
                'bobot' => $row[4],
                'id_rps' => $row[5]
            ]);
    }
}
