<?php

namespace App\UseCases\Admin\Shop;

use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use App\Entities\Shop\Filter;
use Illuminate\Support\Facades\DB;
use App\Entities\Shop\FilterGroup;

class FilterService
{

    public function create(Request $request):Filter
    {
        try {
            DB::beginTransaction();

            /** @var Filter $filter */
            $filter = Filter::create([
                'name'     => $request->get('name'),
                'position' => $request->get('position')
            ]);

            $filter->categories()->attach($request->get('categories'));

            FilterGroup::create([
                'filter_id'      => $filter->id,
                'name'           => 'Пустая группа по умолчанию',
                'display_header' => false
            ]);

            DB::commit();
            return $filter;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new \DomainException($exception->getMessage());
        }
    }

    public function update(Request $request, Filter $filter):Filter
    {

        try {
            DB::beginTransaction();

            $filter->update([
                'name'     => $request->get('name'),
                'position' => $request->get('position')
            ]);

            $filter->categories()->detach();
            $filter->categories()->attach($request->get('categories'));

            $groups = $newGroups = [];

            if ($groupName = $request->get('group_name')) {
                foreach ($groupName as $id => $value) {
                    if (str_starts_with($id, 'new-')) {
                        $newGroups[$id]['name'] = $value;
                    } else {
                        $groups[$id]['name'] = $value;
                    }
                }
            }

            if ($groupTags = $request->get('tags')) {
                foreach ($groupTags as $id => $value) {
                    if (str_starts_with($id, 'new-')) {
                        $newGroups[$id]['tags'] = $value;
                    } else {
                        $groups[$id]['tags'] = $value;
                    }
                }
            }

            if ($groupCategories = $request->get('group_categories')) {
                foreach ($groupCategories as $id => $value) {
                    if (str_starts_with($id, 'new-')) {
                        $newGroups[$id]['categories'] = $value;
                    } else {
                        $groups[$id]['categories'] = $value;
                    }
                }
            }

            if ($displayHead = $request->get('display_head')) {
                foreach ($displayHead as $id => $value) {
                    if (str_starts_with($id, 'new-')) {
                        $newGroups[$id]['display_head'] = true;
                    } else {
                        $groups[$id]['display_head'] = true;
                    }
                }
            }

            if ($groupAttributes = $request->get('attributes')) {
                foreach ($groupAttributes as $id => $value) {
                    if (str_starts_with($id, 'new-')) {
                        $newGroups[$id]['attributes'] = $value;
                    } else {
                        $groups[$id]['attributes'] = $value;
                    }
                }
            }

            if (!empty($groups)) {
                foreach ($filter->groups as $group) {
                    foreach ($groups as $id => $requestGroup) {
                        if ($group->id == $id) {
                            $group->update([
                                'name' => $requestGroup['name'],
                                'attributes'     => $requestGroup['attributes'] ?? null,
                                'tags'           => $requestGroup['tags'] ?? null,
                                'categories'     => $requestGroup['categories'] ?? null,
                                'display_header' => isset($requestGroup['display_head']) ? 1 : 0
                            ]);
                        }
                    }
                }
            }

            if (!empty($newGroups)) {
                foreach ($newGroups as $requestGroup) {
                    FilterGroup::create([
                        'filter_id'      => $filter->id,
                        'name'           => $requestGroup['name'],
                        'attributes'     => $requestGroup['attributes'] ?? null,
                        'tags'           => $requestGroup['tags'] ?? null,
                        'categories'     => $requestGroup['group_categories'] ?? null,
                        'display_header' => isset($requestGroup['display_head']) ? 1 : 0
                    ]);
                }
            }

            DB::commit();
            return $filter;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new \DomainException($exception->getMessage());
        }
    }

}
