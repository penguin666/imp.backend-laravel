<?php

namespace App\Http\Controllers;

use App\Models\Model\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:191|string',
            'description' => 'nullable|max:191|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errMsgs = '';
            foreach ($errors->all() as $message) {
                $errMsgs .= ' '.$message;
            }

            $message = [
                'status' => 'fail',
                'data' => [
                    'message' => $errMsgs
                ]
            ];
            return response()->json($message);
        }

        DB::beginTransaction();
        try{
            $post = new Post();
            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();

            DB::commit();

            $message = [
                'status' => 'success',
                'data' => $post
            ];

            return response()->json($message);
        }catch (Exception $e){
            DB::rollBack();
            $message = [
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage()
                ]
            ];
            return response()->json($message);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:191|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errMsgs = '';
            foreach ($errors->all() as $message) {
                $errMsgs .= ' '.$message;
            }

            $message = [
                'status' => 'fail',
                'data' => [
                    'message' => $errMsgs
                ]
            ];
            return response()->json($message);
        }

        DB::beginTransaction();
        try{
            $post = Post::findOrFail($id);
            $post->title = $request->title;
            $post->description = $request->description;
            $post->save();

            DB::commit();

            $message = [
                'status' => 'success',
                'data' => $post
            ];

            return response()->json($message);
        }catch (Exception $e){
            DB::rollBack();
            $message = [
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage()
                ]
            ];
            return response()->json($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $post = Post::find($id);

            if (!$post){
                throw new \Exception('Data tidak ditemukan');
            }

            $post->delete();

            DB::commit();

            $message = [
                'status' => 'success',
                'data' => [
                    'message' => 'Data dihapus'
                ]
            ];

            return response()->json($message);
        }catch (Exception $e){
            DB::rollBack();
            $message = [
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage()
                ]
            ];
            return response()->json($message);
        }
    }

    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errMsgs = '';
            foreach ($errors->all() as $message) {
                $errMsgs .= ' '.$message;
            }

            $message = [
                'status' => 'fail',
                'data' => [
                    'message' => $errMsgs
                ]
            ];
            return response()->json($message);
        }

        try {
            $post = Post::select('*')->paginate($request->size);

            if ($post->isNotEmpty()) {

                $post->appends(['size' => $request->size]);

                return response()->json([
                    'status' => 'success',
                    'data' => $post
                ]);
            }

            return response()->json([
                'status' => 'fail',
                'data' => [
                    'message' => 'Data tidak ditemukan',
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage(),
                ]
            ]);
        }

    }

    public function getById($random_id)
    {
        try {
            $post = Post::find($random_id);

            if (!$post){
                return response()->json([
                    'status' => 'fail',
                    'data' => [
                        'message' => "Data tidak ditemukan",
                    ]
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $post
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage(),
                ]
            ]);
        }
    }
}
