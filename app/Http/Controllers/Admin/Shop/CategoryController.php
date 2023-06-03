<?php

namespace App\Http\Controllers\Admin\Shop;


use Throwable;
use App\Entities\Shop\Photo;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\UseCases\Admin\Shop\CategoryService;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Admin\Shop\CategoryRequest;

/**
 *
 */
class CategoryController extends Controller
{

    /**
     * @var CategoryService
     */
    private CategoryService $service;

    /**
     * @param CategoryService $service
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $categories = Category::defaultOrder()->withDepth()->paginate(20);

        return view('admin.shop.categories.index', compact('categories'));
    }

    /**
     * @return View
     */
    public function create(): View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $attributes = Attribute::orderBy('sort')->get();

        return view('admin.shop.categories.create', compact('categories', 'attributes'));
    }

    /**
     * @param Category $category
     * @return View
     */
    public function show(Category $category): View
    {
        $parentAttributes = $category->parentAttributes();
        $attributes = $category->attributes()->doesntHave('categories')->orderBy('sort')->get();
        return view('admin.shop.categories.show', compact('category', 'attributes', 'parentAttributes'));
    }

    /**
     * @param CategoryRequest $request
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $category = $this->service->create($request);
        }catch (\DomainException|ValidationException|\Exception|\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.shop.categories.show', compact('category'));
    }

    /**
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.shop.categories.index');
    }

    /**
     * @param Category $category
     * @return View
     */
    public function edit(Category $category): View
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        $attributes = Attribute::orderBy('sort')->get();;
        return view('admin.shop.categories.edit', compact('category', 'categories', 'attributes'));
    }

    /**
     * @param Category $category
     * @return RedirectResponse
     */
    public function togglePublished(Category $category): RedirectResponse
    {
        try {
            $this->service->togglePublished($category);
        } catch (\DomainException|Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Статус видимости успешно изменен');
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->service->update($request, $category);
            return redirect()->route('admin.shop.categories.show', $category);
        } catch (\LogicException|\DomainException $e) {
            $message = $e->getMessage();
            if ($message == 'Cannot move node into itself.') {
                $message = 'Невозможно привязать категорию к самой себе';
            }
            return redirect()->route('admin.shop.categories.edit', $category)->with('error', $message);
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

        return redirect()->route('admin.shop.categories.index');
    }

    public function up(Category $category)
    {
        $category->up();

        return redirect()->route('admin.shop.categories.index');
    }

    public function down(Category $category)
    {
        $category->down();

        return redirect()->route('admin.shop.categories.index');
    }

    public function last(Category $category)
    {
        if ($last = $category->siblings()->defaultOrder('desc')->first()) {
            $category->insertAfterNode($last);
        }

        return redirect()->route('admin.shop.categories.index');
    }
}
