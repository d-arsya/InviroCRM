<?php

namespace App\Http\Controllers;

use App\Http\Resources\WhatsappTokenResource;
use App\Models\WhatsappToken;
use App\Traits\ApiResponder;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

class WhatsappTokenController extends Controller
{
    use ApiResponder;

    /**
     * Get all token
     */
    #[Group('WhatsApp Token')]
    public function index()
    {
        $tokens = WhatsappToken::all();

        return $this->success(WhatsappTokenResource::collection($tokens));
    }

    /**
     * Get detail of token
     */
    #[Group('WhatsApp Token')]
    public function show(WhatsappToken $token)
    {
        return $this->success(new WhatsappTokenResource($token));
    }

    /**
     * Update token value
     */
    #[Group('WhatsApp Token')]
    public function update(Request $request, WhatsappToken $token)
    {
        $request->validate(['token' => 'required|string']);
        $token->update($request->all());

        return $this->success(new WhatsappTokenResource($token));
    }
}
