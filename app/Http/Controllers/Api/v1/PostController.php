<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\ValidationErrorException;
use App\Http\Controllers\Controller;
use App\Models\Post;
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
        //
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

        $insert_id = Post::insertGetId([
            'title' => $data['title'],
            'description' => $data['description']
        ]);

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
