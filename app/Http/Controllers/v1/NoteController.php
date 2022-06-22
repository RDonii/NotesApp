<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Http\Requests\v1\StoreNoteRequest;
use App\Http\Requests\v1\UpdateNoteRequest;
use App\Http\Resources\v1\NoteResource;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function public()
    {
        return NoteResource::collection(Note::where(['is_public'=>true])->paginate(4));
    }

    public function owned(Request $request)
    {   
        $user = auth()->user();
        return NoteResource::collection(Note::where(['owner'=>$user->id])->paginate(4));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreNoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNoteRequest $request)
    {
        $user_id = auth()->user()->id;
        // return $user_id;
        $note = Note::create([
            'title'=>$request->title,
            'subtitle'=>$request->subtitle,
            'body'=>$request->body,
            'owner'=>$user_id,
            'is_public'=>($request->is_public??false),
        ]);
        return $note;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note, Request $request)
    {
        $user = auth()->user();
        if (!$note->is_public && $note->owner!=$user->id){
            return abort(403, 'Bu qaydlar mahfiy va u sizga tegishli emas!');
        }

        return $note;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateNoteRequest  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->title = $request->get('title', $note->title);
        $note->subtitle = $request->get('subtitle', $note->subtitle);
        $note->body = $request->get('body', $note->body);
        $note->is_public = $request->get('is_public', $note->is_public);
        $note->save();
        return $note;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {   
        if ($note->owner!=auth()->user()->id){
            return abort(403, "Brovga tegishli qaydlarni o'chirishga uyalmaysizmiya...");
        }
        $title = $note->title;
        $note->delete();
        return response(['message'=>"O'chirildi", 'title'=>$title]);
    }
}
