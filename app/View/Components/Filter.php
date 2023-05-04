<?php

namespace App\View\Components;

use App\Entities\Shop\Tag;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use Illuminate\View\Component;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use App\Entities\Shop\Filter as FilterEntity;

class Filter extends Component
{

    private FilterEntity|array $filter = [];

    private array $formFilterData = [];
    private array $restAttributes = [];
    private array $restTags       = [];
    private array $restCategories = [];
    public function __construct(string $position, Request $request, $restAttributes = null, $restTags = null, $restCategories = null)
    {
        if ($restAttributes) {
            $this->restAttributes = $restAttributes;
        }

        if ($restTags) {
            $this->restTags = $restTags;
        }

        if ($restCategories) {
            $this->restCategories = $restCategories;
        }

        $this->filter         = FilterEntity::where('position', $position)->with('groups')->first() ?? [];
        $this->formFilterData = $this->getFormData($request);
    }

    public function render()
    {
        return $this->view('components.filter', ['filter' => $this->formFilterData, 'restAttributes' => $this->restAttributes, 'restTags' => $this->restTags, 'restCategories' => $this->restCategories]);
    }

    private function getFormData(Request $request):array
    {
        $result = [];
        $inputs = $request->input();

        if (empty($this->filter)) {
            return $result;
        }

        $pricesDB = array_unique(Product::whereIn('category_id', $this->filter->allCategories()->pluck('id')->toArray())->pluck('price')->toArray());
        sort($pricesDB);
        $prices = array_map(function ($val) {
            return $val.' ₽';
        }, $pricesDB);
        $result['Цена']['prices']        = $prices;
        $result['Цена']['displayHeader'] = true;

        foreach ($this->filter->groups as $filterGroup) {
            $result[$filterGroup->name]['displayHeader'] = $filterGroup->display_header;
            $result[$filterGroup->name]['id'] = $filterGroup->id;

            if ($filterGroup->categories) {
                foreach ($filterGroup->categories as $categoryId) {
                    /** @var Category $category */
                    $category = Category::findOrFail($categoryId);
                    $result[$filterGroup->name]['categories'][] = $category;
                }
            }
            if ($filterGroup->tags) {
                foreach ($filterGroup->tags as $tagId) {
                    /** @var Tag $tag */
                    $tag = Tag::findOrFail($tagId);

                    $result[$filterGroup->name]['tags'][] = $tag;

                }
            }
            if ($filterGroup->attributes) {
                foreach ($filterGroup->attributes as $attributeId) {
                    /** @var $attribute Attribute */
                    $attribute = Attribute::findOrFail($attributeId);

                    if ($attribute->type === Attribute::TYPE_FLOAT || $attribute->type === Attribute::TYPE_INTEGER) {
                        $values    = $attribute->variants;
                        $newValues = $newEqualValues = [];
                        foreach ($values as $value) {
                            if (preg_match('/[А-я]/', $value) === 0) {
                                $newValues[] = (float)$value;
                            } else {
                                $newEqualValues[] = (string)$value;
                            }
                        }
                        sort($newValues);
                        $strNewValues = array_map(function ($val) use ($attribute) {
                            $res =  $attribute->unit ? '"'.$val.' '.$attribute->unit.'"' : '"'.$val.'"';
                            return trim($res);
                        },$newValues);
                        $attribute->variants = $strNewValues;

                        if (!empty($newEqualValues)) {
                            $attribute->newEquals = $newEqualValues;
                        }
                    }

                    if (!empty($inputs['attributes'])) {
                        foreach ($inputs['attributes'] as $attrId => $reqValues) {
                            if ($attrId == $attribute->id) {
                                $attribute->selected = $reqValues;
                            }
                        }
                    }

                    $result[$filterGroup->name]['attributes'][] = $attribute;
                }
            }
        }


        return $result;
    }
}
