<?php

namespace Modules\Ievent\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Ievent\Entities\Category;
use Modules\Ievent\Http\Requests\CreateCategoryRequest;
use Modules\Ievent\Http\Requests\UpdateCategoryRequest;
use Modules\Ievent\Repositories\CategoryRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;


class CategoryController extends AdminBaseController
{
    /**
     * @var CategoryRepository
     */
    private $category;

    public function __construct(CategoryRepository $category)
    {
        parent::__construct();

        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $categories = $this->category->all();
        return view('ievent::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->category->all();
        return view('ievent::admin.categories.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCategoryRequest $request
     * @return Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $this->category->create($request->all());

        return redirect()->route('admin.ievent.category.index')
            ->withSuccess(trans('core::core.messages.resource created', ['name' => trans('ievent::categories.title.categories')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return Response
     */
    public function edit($categoryId)
    {
       
        $category = $this->category->find($categoryId);
        $categories = $this->category->all();
        return view('ievent::admin.categories.edit', compact('category','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Category $category
     * @param  UpdateCategoryRequest $request
     * @return Response
     */
    public function update($categoryId, UpdateCategoryRequest $request)
    {

        $category = $this->category->find($categoryId);

        $this->category->update($category, $request->all());

        return redirect()->route('admin.ievent.category.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('ievent::categories.title.categories')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category $category
     * @return Response
     */
    public function destroy($categoryId)
    {

        $category = $this->category->find($categoryId);

        try {
            $this->category->destroy($category);

            return redirect()->route('admin.ievent.category.index')
                ->withSuccess(trans('core::core.messages.resource deleted', ['name' => trans('ievent::categories.title.categories')]));

         } catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()
                ->withError(trans('core::core.messages.resource error', ['name' => trans('ievent::category.title.categories')]));

        }
    }
}
