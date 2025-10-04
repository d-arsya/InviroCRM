<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponder;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    use ApiResponder;

    /**
     * Update configuration of message sending
     */
    #[Group('Config')]
    public function update(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:10',
            'interval' => 'required|integer|min:5|max:60',
        ]);
        DB::table('config')->whereKey('days_after')->update(['value' => $request->days]);
        DB::table('config')->whereKey('send_interval')->update(['value' => $request->interval]);

        return $this->success([
            'days' => $request->days,
            'interval' => $request->interval,
        ]);
    }
}
