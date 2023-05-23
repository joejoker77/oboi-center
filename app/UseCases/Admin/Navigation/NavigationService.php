<?php

namespace App\UseCases\Admin\Navigation;

use Throwable;
use App\Entities\Shop\Tag;
use App\Entities\Shop\Brand;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use Illuminate\Support\Facades\DB;
use App\Entities\Site\Navigations\Menu;
use App\Entities\Site\Navigations\NavItem;
use Illuminate\Contracts\Container\BindingResolutionException;

class NavigationService
{

    private array $models = [
        Category::class => ['name' => 'Категория', 'type' => 'category'],
        Tag::class      => ['name' => 'Тэг', 'type' => 'tag'],
        Brand::class    => ['name' => 'Бренд', 'type' => 'brand'],
        Product::class  => ['name' => 'Продукт', 'type' => 'product']
    ];


    public function find(string $query): array
    {
        if (!$query) {
            abort(400);
        }
        $searchableData = [];
        foreach ($this->models as $model => $modelArray) {
            $q      = $model::query();
            $fields = $model::$searchable;

            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', '%'.$query.'%');
            }
            $results = $q->take(10)->get();

            foreach ($results as $result) {
                $parsedData = $result->only($fields);
                $parsedData['model']    = $modelArray['name'];
                $parsedData['type']     = $modelArray['type'];
                $parsedData['model_id'] = $result->id;
                $parsedData['fields']   = $fields;
                $formattedFields        = [];
                foreach ($fields as $field) {
                    $formattedFields[$field] = \Str::title($field);
                }
                $parsedData['fields_formatted'] = $formattedFields;
                $searchableData[] = $parsedData;
            }
        }
        return $searchableData;
    }

    /**
     * @throws Throwable
     */
    public function update(Menu $menu, Request $request)
    {
        return DB::transaction(function () use ($menu, $request) {
            $menu->update([
                'title' => $request->get('title'),
                'handler' => $request->get('handler'),
                'show_title' => $request->get('show_title') ?? 0
            ]);

            if ($request->get('items')) {
                $items = $request->get('items');

                if (!$menu->navItems->isEmpty()) {
                    foreach ($menu->navItems as $navItem) {
                        $navItem->delete();
                    }
                }

                if (empty($items)) {
                    throw new \DomainException('Пункты меню пусты!');
                }

                foreach ($items as $item) {

                    $route = $item['type'] == 'category' || $item['type'] == 'product' ? 'catalog.index' : 'catalog.'.$item['type'];
                    $title = $item['title'];
                    $path  = $item['type'] == 'separator' || $item['type'] == 'external' ?
                        $item['title'] : $this->getPath($item['type'], $route, $item['item_id']);

                    $menuItem = NavItem::make([
                        'title'        => $title,
                        'link_text'    => !empty($item['link_text']) ? $item['link_text'] : null,
                        'route_name'   => $route,
                        'item_path'    => $path,
                        'sort'         => $item['sort'],
                        'front_id'     => $item['id'],
                        'front_parent' => $item['parent'],
                        'entity_id'    => $item['item_id'],
                        'entity_type'  => $item['type'],
                        'image'        => $item['image'] ?? null
                    ]);

                    $menuItem->menu()->associate($menu);

                    $menuItem->saveOrFail();

                    if ((int)$item['parent'] > 0) {
                        $parentItem = NavItem::where('front_id', $item['parent'])
                            ->where('menu_id', $menu->id)->first();
                        $menuItem->parent_id = $parentItem->id;
                        $menuItem->save();
                    }
                }
            }
        });
    }

    public function deleteItem($id) :void
    {
        $menuItem = NavItem::find($id);
        $menuItem->delete();

        $children = NavItem::where('parent_id', $id)->get();
        if ($children->count() > 0) {
            foreach ($children as $menuItem) {
                $this->deleteItem($menuItem->id);
            }
        }
    }

    /**
     * @throws BindingResolutionException
     */
    private function getPath(string $type, string $route, $itemId):string
    {
        return match ($type) {
            'category' => route($route, ['product_path' => product_path(Category::find($itemId), null)], false),
            'product'  => route($route,['product_path' => product_path(Product::find($itemId)->category, Product::find($itemId))], false),
            'tag'      => route($route, Tag::find($itemId)),
            'brand'    => route($route, Brand::find($itemId)),
        };
    }

}
