<?php

namespace App\Http\Controllers;

use App\Models\HarmfulWord;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function listPosts(Request $request)
    {
        $posts = Post::query();

        if(isset($request->is_content_harmful)) {
            $posts->where('is_content_harmful', $request->is_content_harmful);
        }

        $posts = $posts->get();

        return response()->json([
            'response' => ['status' => true, 'message' => 'Posts.'],
            'result' => [
                'status' => 200,
                'posts' => $posts
            ]
        ]);
    }

    public function createOrUpdatePost(Request $request)
    {
        $user = $request->user();

        $rules = [
            'post_content' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        if (empty($request->id))
            $Post = new Post();
        else
            $Post = Post::find($request->id);

        $Post->user_id = $user->id;
        $Post->is_content_harmful = 0;

        $HarmfulWord = new HarmfulWord();
        $harmfulWords = $HarmfulWord->all()->pluck('word')->toArray();
        // $harmfulWords = [
        //     'hate',
        //     'violence',
        //     'threat',
        //     'explicit',
        //     'bullying',
        //     'harassment',
        //     'swear words',
        //     'vulgar language',
        //     'offensive slurs',
        //     'kill',
        //     'attack',
        //     'harm',
        //     'violence',
        //     'abuse',
        //     'racial slurs',
        //     'homophobic terms',
        //     'sexist remarks',
        //     'sexual',
        //     'pornography',
        //     'adult content',
        //     'drugs',
        //     'illegal substances',
        //     'illegal activities',
        //     'bully',
        //     'harass',
        //     'cyberbully',
        //     'intimidation',
        //     'suicide',
        //     'self-harm',
        //     'eating disorders',
        //     'mental health issues',
        //     'fake news',
        //     'misinformation',
        //     'conspiracy theories'
        // ];

        foreach ($harmfulWords as $harmfulWord) {
            if (strpos($request->post_content, $harmfulWord) !== false) {
                $Post->is_content_harmful = 1;
            }
        }

        $Post->post_content = $request->post_content;
        $Post->save();

        $msg = empty($request->id) ? 'Post has been created successfully.' : 'Post has been updated successfully.';

        return response()->json([
            'response' => ['status' => true, 'message' => $msg]
        ]);
    }

    public function deletePost(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        Post::where('id', $request->id)->delete();

        return response()->json([
            'response' => ['status' => true, 'message' => 'Post has been deleted successfully.']
        ]);
    }

    public function reportPost(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return response()->json(['response' => ['status' => false, 'message' => $validator->errors()->all()]]);

        Post::where('id', $request->id)->update(['is_content_harmful' => 1]);

        return response()->json([
            'response' => ['status' => true, 'message' => 'Post has been reported successfully.']
        ]);
    }
}
