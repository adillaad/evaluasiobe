<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($row[2] == null) {
            return new User([
                'name'     => $row[0],
                'email'    => $row[1],
                'password' => Hash::make('unilajaya'),
                'img' => 'User-Profile.png',
                'otoritas' => $row[3]
            ]);
        } else {
            return new User([
                'name'     => $row[0],
                'email'    => $row[1],
                'password' => Hash::make($row[2]),
                'img' => 'User-Profile.png',
                'otoritas' => $row[3]
            ]);
        }
    }
}
