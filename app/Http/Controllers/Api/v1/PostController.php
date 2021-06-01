<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\DbErrorException;
use App\Exceptions\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;


class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('userAccessCheck')->only(['store']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $posts = Post::all();
        } catch (QueryException $exception) {
            throw new DbErrorException($exception->errorInfo);
        }
        return json_encode([
            'status' => true,
            'result' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->json()->all();
        $title =  $request->json('title');
        $description = $request->json('description');
        $validator = Validator::make($request->json()->all(), [
            'title' => [
                'required', 'string', 'min:10', 'max:50',
                Rule::unique('posts', 'title')->where(function ($query) use ($description) {
                    return $query->where('description', $description);
                })
            ],
            'description' => ['required', 'string', 'min:50', 'max:1000']
        ]);

        if ($validator->fails()) {
            throw new ValidationErrorException($validator->errors()->messages());
        }

        try {
            $insert_id = Post::insertGetId([
                'title' => $data['title'],
                'description' => $data['description']
            ]);
        } catch (QueryException $exception) {
            throw new DbErrorException($exception->errorInfo);
        }

        if ($insert_id) {
            return  new JsonResponse([
                'status' => true,
                'result' => $insert_id
            ], Response::HTTP_OK);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $post = Post::find($id);
        } catch (QueryException $exception) {
            throw new DbErrorException($exception->errorInfo);
        }
        return json_encode([
            'status' => true,
            'result' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $post = Post::find($id);
        $data = $request->json()->all();
        $description = $request->json('description');
        $validator = Validator::make($request->json()->all(), [
            'title' => [
                'string', 'min:10', 'max:50',
                Rule::unique('posts', 'title')->where(function ($query) use ($description, $id) {
                    return $query->where('description', $description)
                        ->where('id', '<>', $id);
                })
            ],
            'description' => ['string', 'min:50', 'max:1000']
        ]);

        if ($validator->fails()) {
            throw new ValidationErrorException($validator->errors()->messages());
        }


        try {
            $post->update($data);
        } catch (QueryException $exception) {
            throw new DbErrorException($exception->errorInfo);
        }


        return  new JsonResponse([
            'status' => true,
            'result' => "post id {$id} Updated!"
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
        $post = Post::find($id);
        } catch (QueryException $exception) {
            throw new DbErrorException($exception->errorInfo);
        }

        if(! empty($post))
        {
            try{
                $post->delete();
            }
            catch (QueryException $exception) {
                throw new DbErrorException($exception->errorInfo);
            }

            return  new JsonResponse([
                'status' => true,
                'result' => "post id {$id} deleted!"
            ], Response::HTTP_OK);
        }
        else
        {
            return  new JsonResponse([
                'status' => false,
                'result' => "post id {$id} not found!"
            ], Response::HTTP_OK);
        }




    }
}
