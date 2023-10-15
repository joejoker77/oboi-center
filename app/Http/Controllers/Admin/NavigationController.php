<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Entities\Site\Navigations\Menu;
use App\Entities\Site\Navigations\NavItem;
use App\UseCases\Admin\Navigation\NavigationService;

class NavigationController extends Controller
{
    private NavigationService $service;

    public function __construct(NavigationService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $menus = Menu::orderBy('id')->get();

        return view('admin.navigations.index', compact('menus'));
    }

    public function getFormMenu(Request $request):View
    {
        $menu = Menu::find($request->get('menu_id'));
        return view('admin.navigations.partials.form-menu', compact('menu'));
    }

    public function getFormMenuItems(Request $request):View
    {
        $menu = Menu::find($request->get('menu_id'));
        return view('admin.navigations.partials.form-menu-items', compact('menu'));
    }

    public function find(Request $request): JsonResponse
    {
        $query    = $request->get('query');
        $response = '';

        if ($query) {
            $response = $this->service->find($query);
        }
        return response()->json($response);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $menu = Menu::find($id);
            $this->service->update($menu, $request);

            return redirect()->route('admin.navigations.index', ['menuActive' => true, 'menu' => $menu])
                ->with('success', 'Меню успешно обновлено');
        } catch (Exception|Throwable $exception) {
            return redirect()->route('admin.navigations.index')->with('error', $exception->getMessage());
        }
    }

    public function store(Request $request): RedirectResponse
    {
        Menu::create([
            'title' => $request->get('title'),
            'handler' => $request->get('handler'),
            'show_title' => (integer)$request->get('show_title')
        ]);
        return redirect()->route('admin.navigations.index')->with('success', 'Меню успешно создано');
    }

    public function destroyItem($id)
    {
        $item = NavItem::find($id);
        $menu = $item->menu;
        $this->service->deleteItem($id);
        return redirect()->route('admin.navigations.index', ['menuActive' => true, 'menu' => $menu])
            ->with('success', 'Пункт меню успешно удален');
    }

    public function destroy($id): RedirectResponse
    {
        $menu = Menu::find($id);
        $menu->delete();
        return redirect()->route('admin.navigations.index')->with('success', 'Меню успешно удалено');
    }

    public function deleteImage($id)
    {
        $item        = NavItem::find($id);
        $item->image = null;
        $item->save();
        return redirect()->route('admin.navigations.index', ['menuActive' => true, 'menu' => $item->menu])
            ->with('success', 'Изображение удалено');
    }
}
