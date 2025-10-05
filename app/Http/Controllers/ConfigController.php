<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Traits\ApiResponder;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    use ApiResponder;

    /**
     * Get spreadsheet configuration data
     */
    #[Group('Config')]
    public function spreadsheet(Request $request)
    {
        $spreadsheet = 'https://docs.google.com/spreadsheets/d/'.DB::table('config')->whereKey('spreadsheet_id')->first()->value;
        $sync = DB::table('config')->whereKey('sync_time')->first()->value;
        $sync = explode(':', $sync);

        return $this->success([
            'spreadsheet' => $spreadsheet,
            'hour' => (int) $sync[0],
            'minute' => (int) $sync[1],
        ]);
    }

    /**
     * Get sending configuration data
     */
    #[Group('Config')]
    public function getSending()
    {
        $days = (int) DB::table('config')->whereKey('days_after')->first()->value;
        $interval = (int) DB::table('config')->whereKey('send_interval')->first()->value;

        return $this->success([
            'days' => $days,
            'interval' => $interval,
        ]);
    }

    /**
     * Update configuration of message sending
     */
    #[Group('Config')]
    public function sending(Request $request)
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

    /**
     * Update configuration of spreadsheet link
     */
    #[Group('Config')]
    public function link(Request $request)
    {
        $request->validate([
            'link' => 'required|string',
        ]);
        $link = explode('/', $request->link);
        $id = $link[array_search('d', $link) + 1];
        DB::table('config')->whereKey('spreadsheet_id')->update(['value' => $id]);

        return $this->success([
            'link' => 'https://docs.google.com/spreadsheets/d/'.$id,
        ]);
    }

    /**
     * Update configuration of spreadsheet sync time
     */
    #[Group('Config')]
    public function sync(Request $request)
    {
        $request->validate([
            'hour' => 'required|integer|min:16|max:23',
            'minute' => 'required|integer|max:59',
        ]);
        DB::table('config')->whereKey('sync_time')->update(['value' => $request->hour.':'.$request->minute]);

        return $this->success([
            'hour' => $request->hour,
            'minute' => $request->minute,
        ]);
    }

    /**
     * Get default config
     */
    #[Group('Config')]
    public function default()
    {
        $title = Message::whereDefault(true)->first()->title;
        $days = (int) DB::table('config')->whereKey('days_after')->first()->value;

        return $this->success(['days' => $days, 'title' => $title]);
    }
}
