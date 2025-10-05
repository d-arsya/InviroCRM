<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\ApiResponder;
use App\Traits\Spreadsheet;
use Illuminate\Support\Facades\DB;

class SpreadsheetController extends Controller
{
    use ApiResponder, Spreadsheet;

    public function coba()
    {
        $spreadsheetId = DB::table('config')->whereKey('spreadsheet_id')->first()->value;
        $sheetName = $this->getSheetName($spreadsheetId, 1);
        $data = $this->getByName($spreadsheetId, $sheetName);
        $customers = [];

        foreach ($data as $item) {
            $customers[] = Customer::create($item);
        }

        return $this->success($customers);
    }
}
