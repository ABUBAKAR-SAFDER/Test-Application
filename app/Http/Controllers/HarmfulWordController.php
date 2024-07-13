<?php

namespace App\Http\Controllers;

use App\Models\HarmfulWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HarmfulWordController extends Controller
{
    public function listWords()
    {
        $harmfulWords = HarmfulWord::all();

        return response()->json([
            'response' => ['status' => true, 'message' => 'Harmful Words.'],
            'result' => [
                'status' => 200,
                'words' => $harmfulWords
            ]
        ]);
    }

    public function createOrUpdateWord(Request $request)
    {
        $rules = [
            'word' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        if (empty($request->id))
            $HarmfulWord = new HarmfulWord();
        else
            $HarmfulWord = HarmfulWord::find($request->id);

        $HarmfulWord->word = $request->word;
        $HarmfulWord->save();

        $msg = empty($request->id) ? 'Word has been created successfully.' : 'Word has been updated successfully.';

        return response()->json([
            'response' => ['status' => true, 'message' => $msg]
        ]);
    }

    public function deleteWord(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        HarmfulWord::where('id', $request->id)->delete();

        return response()->json([
            'response' => ['status' => true, 'message' => 'Word has been deleted successfully.']
        ]);
    }
}
