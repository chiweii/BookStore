<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Resources\TypeCollection;
use App\Http\Resources\TypeResource;

class TypeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api',['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = Type::select('id','name','sort')->get();

        return new TypeCollection($types);
        // return response([
        //     'data' => $types
        // ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => [
                'required',
                'max:50',
                Rule::unique('types','name')
            ],
            'sort' => 'nullable|integer',
        ]);
        
        if(!isset($request->sort)){
            $max = Type::max('sort');
            $request['sort'] = $max+1;
        }
        
        $type = Type::create($request->all());

        return new TypeResource($type);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
        return new TypeResource($type);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
        $this->validate($request,[
            'name' => [
                'max:50',
                Rule::unique('types','name')->ignore($type->name,'name')
            ],
            'sort' => 'nullable|integer',
        ]);

        $type->update($request->all());

        return new TypeResource($type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
        $type->delete();

        return response(null,Response::HTTP_NO_CONTENT);
    }
}
