<?php

namespace App\Http\Controllers\API\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Category::all();
        $result = CategoryResource::collection($data);

        return $this->sendResponse($result, 'Data berhasil diambil');
        // return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = new CategoryResource(Category::create($request->validated()));

        return $this->sendResponse($data, 'Successfully Stored');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //cek apakah data yang diambil ada atau tidak
        $cek = Category::find($category->id);
        if (!$cek) {
            abort(404, 'Object not found');
        }

        $category = new CategoryResource($cek);

        return $this->sendResponse($category, 'Successfully Get Category');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        $result = new CategoryResource($category);

        return $this->sendResponse($result, 'Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        $result = new CategoryResource($category);

        return $this->sendResponse($result, 'Successfully Deleted');
    }
}
