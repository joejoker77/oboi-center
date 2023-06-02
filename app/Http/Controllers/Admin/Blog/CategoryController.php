<?php

namespace App\Http\Controllers\Admin\Blog;


use Throwable;
use Illuminate\View\View;
use App\Entities\Shop\Photo;
use App\Entities\Blog\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Blog\CategoryService;
use App\Http\Requests\Admin\Blog\CategoryRequest;

class CategoryController extends Controller
{

    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $categories = Category::defaultOrder()->withDepth()->paginate(20);

        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::defaultOrder()->withDepth()->get();

        return view('admin.blog.categories.create', compact('categories'));
    }

    public function show(Category $category): View
    {
        return view('admin.blog.categories.show', compact('category'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $category = $this->service->create($request);
        } catch (\Exception|Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.blog.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        return view('admin.blog.categories.edit', compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->service->update($request, $category);
            return redirect()->route('admin.blog.categories.show', $category);
        } catch (\LogicException|\DomainException $e) {
            $message = $e->getMessage();
            if ($message == 'Cannot move node into itself.') {
                $message = 'Невозможно привязать категорию к самой себе';
            }
            return redirect()->route('admin.blog.categories.edit', $category)->with('error', $message);
        }


    }

    /**
     * @throws Throwable
     */
    public function photoUp (Category $category, Photo $photo) {
        try {
            $category->movePhotoUp($photo->id);
            return redirect()->back()->with('success', 'Фото успешно перемещено.');
        } catch (\DomainException $e) {
            return redirect()->route('admin.shop.categories.show', compact('category'))
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function photoDown (Category $category, Photo $photo) {
        try {
            $category->movePhotoDown($photo->id);
            return redirect()->back()->with('success', 'Фото успешно перемещено.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    public function photoRemove (Category $category, Photo $photo) {
        try {
            $category->removePhoto($photo->id);
            return redirect()->back()->with('success', 'Фото успешно удалено.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function first(Category $category)
    {
        if ($first = $category->siblings()->defaultOrder()->first()) {
            $category->insertBeforeNode($first);
        }

        return redirect()->route('admin.blog.categories.index');
    }

    public function up(Category $category)
    {
        $category->up();

        return redirect()->route('admin.blog.categories.index');
    }

    public function down(Category $category)
    {
        $category->down();

        return redirect()->route('admin.blog.categories.index');
    }

    public function last(Category $category)
    {
        if ($last = $category->siblings()->defaultOrder('desc')->first()) {
            $category->insertAfterNode($last);
        }

        return redirect()->route('admin.blog.categories.index');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        if ($category->isActive()) {
            $category->status = Category::STATUS_DRAFT;
        } else {
            $category->status = Category::STATUS_ACTIVE;
        }

        $category->save();

        return back()->with('success', 'Статус категории успешно изменен');
    }
}
