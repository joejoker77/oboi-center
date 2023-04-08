<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Entities\Shop\Photo;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\UseCases\Admin\Shop\VariantService;

class PhotoController extends Controller
{
    private VariantService $service;

    public function __construct(VariantService $service)
    {
        $this->service = $service;
    }

    public function getPhotos(Request $request): JsonResponse|View
    {
        /** @var Photo $photo */
        try {
            $object = match ($request['owner']) {
                'category' => Category::find($request['category_id']),
                "product" => Product::find($request['product_id']),
                default => null
            };
            $photos = $object->photos;

            if (!$photos) {
                return response()->json(['error' => 'Изображения не найдены']);
            }

            return view('admin.partials.photos', compact('photos'), ['photo_id' => $request['id']]);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'При обработке запроса произошла ошибка: '. $exception->getMessage()]);
        }

    }

    public function getVariantPhotos(Request $request):JsonResponse|View
    {
        try {
            $variant = Product::find($request['id']);
            return view('admin.partials.variant-photos', compact('variant'));
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Во время выполнения запроса произошла ошибка: '.$exception->getMessage()]);
        }
    }

    public function updateVariantPhoto(Request $request):JsonResponse
    {
        try {
            $this->service->updateVariant($request);
            Session::flash('success', 'Вариант успешно обновлен');
            return response()->json(['success' => 'Вариант успешно обновлен']);
        } catch (\DomainException $exception) {
            return response()->json(['error' => 'Во время выполнения запроса произошла ошибка: '. $exception->getMessage()]);
        }
    }

    public function updatePhoto(Request $request): JsonResponse
    {
        try {
            $photo = Photo::find($request['id']);
            $photo->description = $request['description'];
            $photo->alt_tag = $request['alt_tag'];
            $photo->save();
            return response()->json(['success' => 'Объект Photo успешно обновлен']);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Не удалось обновить объект Photo. '.$exception->getMessage()]);
        }
    }
}
