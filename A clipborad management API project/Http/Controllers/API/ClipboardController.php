<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClipboardRequest;
use App\Http\Resources\ClipboardCollectionResource;
use App\Http\Resources\ClipboardSingleResource;
use App\Models\Clipboard;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClipboardController extends Controller
{
    public function index()
    {
        return new ClipboardCollectionResource(Clipboard::paginate());
    }

    public function store(ClipboardRequest $request)
    {
        $clipboard = auth()->user()->clipboards()->create($request->validated());
        return (new ClipboardSingleResource($clipboard))
            ->response()
            ->setStatusCode(201);
    }


    public function show(Clipboard $clipboard)
    {
        return new ClipboardSingleResource($clipboard);
    }


    public function update(ClipboardRequest $request, Clipboard $clipboard)
    {
        $clipboard->update($request->validated());
        return (new ClipboardSingleResource($clipboard))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Clipboard $clipboard)
    {
        $clipboard->delete();
        return (new ClipboardSingleResource([]))
                ->response()
                ->setStatusCode(204);
    }
}
