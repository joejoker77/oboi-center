<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Entities\Site\Navigations\NavItem;
use App\Entities\Site\Navigations\Menu as Navigation;
use Staudenmeir\LaravelAdjacencyList\Eloquent\Collection;

class Menu extends Component
{

    public array|Navigation $menu     = [];
    public array|Collection $navItems = [];
    public null|string $menu_id       = null;
    public string $header_tag         = 'h4';
    public string $header_class       = 'h4';
    public string $template           = 'components.menu';
    public string $menu_class         = 'navbar-nav';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $handler, string $headerTag = null, string $headerClass = null, $menuClass = null, $template = null, $menuId = null)
    {
        $findMenu = Navigation::where('handler', $handler)->first();
        if ($findMenu) {
            $this->navItems = NavItem::treeOf(function ($query) use ($findMenu) {
                $query->whereNull('parent_id')->where('menu_id', $findMenu->id);
            })->orderBy('sort')->get()->toTree();
            $this->menu = $findMenu;
        }
        if ($headerTag) {
            $this->header_tag = $headerTag;
        }
        if ($headerClass) {
            $this->header_class = $headerClass;
        }
        if ($menuClass) {
            $this->menu_class = $menuClass;
        }
        if ($template) {
            $this->template = $template;
        }
        if ($menuId) {
            $this->menu_id = $menuId;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view($this->template, [
            'headerTag' => $this->header_tag, 'headerClass' => $this->header_class, 'menuClass' => $this->menu_class
        ]);
    }
}
