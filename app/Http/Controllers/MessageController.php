<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAndUpdateTemplate;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Traits\ApiResponder;
use Dedoc\Scramble\Attributes\Group;

class MessageController extends Controller
{
    use ApiResponder;

    /**
     * Get all templates
     */
    #[Group('Message Template')]
    public function index()
    {
        $templates = Message::all();

        return $this->success(MessageResource::collection($templates));
    }

    /**
     * Create new template
     */
    #[Group('Message Template')]
    public function store(CreateAndUpdateTemplate $request)
    {
        $template = Message::create($request->all());

        return $this->created(new MessageResource($template));
    }

    /**
     * Get detail of template
     */
    #[Group('Message Template')]
    public function show(Message $template)
    {
        return $this->success(new MessageResource($template));
    }

    /**
     * Update a template data
     */
    #[Group('Message Template')]
    public function update(CreateAndUpdateTemplate $request, Message $template)
    {
        $template->update($request->all());

        return $this->success(new MessageResource($template));
    }

    /**
     * Delete a template
     *
     * Template default tidak bisa dihapus
     */
    #[Group('Message Template')]
    public function destroy(Message $template)
    {
        try {
            $template->delete();

            return $this->deleted();
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, null);
        }
    }
}
