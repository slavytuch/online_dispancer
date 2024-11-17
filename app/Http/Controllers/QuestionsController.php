<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Question;

class QuestionsController extends Controller
{
    public function getById($questionId)
    {
        return Question::findOrFail($questionId);
    }

    public function getList()
    {
        return Question::all();
    }

    public function update($questionId, UpdateQuestionRequest $request)
    {
        Question::findOrFail($questionId)->update($request->toArray());
    }
}
