<?php

namespace App\UseCases\Admin\Shop;


use Throwable;
use App\Entities\Shop\Tag;
use App\Entities\Shop\File;
use Illuminate\Support\Str;
use App\Entities\Shop\Photo;
use Illuminate\Http\Request;
use App\Entities\Shop\Brand;
use App\Entities\Shop\Variant;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Illuminate\Http\UploadedFile;
use Orchestra\Parser\Xml\Document;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Shop\ProductRequest;
use App\Http\Requests\Admin\Shop\CategoryRequest;
use App\Http\Requests\Admin\Shop\AttributeRequest;
use App\Http\Controllers\Admin\Shop\TagController;
use App\Http\Controllers\Admin\Shop\BrandController;
use App\Http\Controllers\Admin\Shop\ProductController;
use App\Http\Controllers\Admin\Shop\CategoryController;
use App\Http\Controllers\Admin\Shop\AttributeController;

class DailyImportService
{

    private function file_get_contents_utf8($fn): string
    {
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1, Windows-1251', true));
    }

    /**
     * @throws Throwable
     */
    public function update(Document $xml):void
    {
        $document = $xml->getContent();
        $brands = $categories = $options = $products = $tags = [];
        foreach ($document->Фабрики->Фабрика as $value) {
            $brand = [
                'import_id' => (string)$value->ИД,
                'name' => (string)$value->Наименование,
                'supplier' => 'Surgaz',
                'meta' => [
                    'title' => 'Фабрика: «' . (string)$value->Наименование . '»',
                    'description' => 'На данной странице представлена продукция компании: «' . (string)$value->Наименование . '»',
                ]
            ];
            if ($value->Файлы->Файл) {
                foreach ($value->Файлы->Файл as $file) {
                    $attributes = $file->attributes();
                    if ((string)$attributes["Тип"] === 'описание') {
                        $brand['seo_text'] = $this->file_get_contents_utf8((string)$file);
                    }
                }
            }
            if ($value->Картинки->КартинкиОригинал->Картинка) {
                $images = [];
                foreach ($value->Картинки->КартинкиОригинал->Картинка as $imageUrl) {
                    $images[] = (string)$imageUrl;
                }
                $brand['images'] = $images;
            }
            $brands[] = $brand;
            echo "Бренд: ". $value->Наименование . ' прочитан'.PHP_EOL;
        }

        foreach ($document->Коллекции->Коллекция as $collection) {
            $category = [
                'import_id' => (string)$collection->ИД,
                'name' => (string)$collection->Наименование,
                'brandImportId' => (string)$collection->Фабрика->attributes()->ФабрикаИД,
                'brand' => (string)$collection->Фабрика
            ];

            if ($collection->Файлы->Файл) {
                foreach ($collection->Файлы->Файл as $file) {
                    $attributes = $file->attributes();
                    if ($attributes['Тип'] == 'описание') {
                        $category['description'] = trim(preg_replace("/^[\pZ\pC]+|[\pZ\pC]+$/u", "", $this->file_get_contents_utf8((string)$file)));
                    } else {
                        $category['files'][] = (string)$file;
                    }
                }
            }

            if ($collection->Картинки->КартинкиОригинал->Картинка) {
                foreach ($collection->Картинки->КартинкиОригинал->Картинка as $image) {
                    $category['images'][] = (string)$image;
                }
            }

            $categories[] = $category;
            echo 'Категория: '. $collection->Наименование . ' прочитана'.PHP_EOL;
        }

        foreach ($document->Товары->Товар as $product) {
            $images = [];
            $offer = [
                'import_id'         => (string)$product->ИД,
                'brandImportId'     => (string)$product->Фабрика->attributes()["ФабрикаИД"],
                'categoryImportId'  => (string)$product->Коллекция->attributes()["КоллекцияИД"],
                'sku'               => (string)$product->Артикул,
                'weight'            => (string)$product->Вес,
                'volume'            => (string)$product->Объем,
                'packaging'         => (string)$product->Упаковка,
                'unit'              => (string)$product->Единица,
                'amount_in_package' => (string)$product->КоличествоВУпаковке,
                'country'           => (string)$product->СтранаПроисхождения,
                'price'             => (string)$product->ЦенаРРЦ,
                'order_variants'    => (string)$product->ЖизненныйЦикл,
                'quantity'          => (string)$product->ОстатокНаСкладе
            ];

            if ($product->СмежныеКоллекции->СмежнаяКоллекция) {
                foreach ($product->СмежныеКоллекции->СмежнаяКоллекция as $cat){
                    $offer['categoriesImportIds'][] = (string)$cat->attributes()->КоллекцияИД;
                }
            }

            if ($product->Компаньоны->Компаньон) {
                foreach ($product->Компаньоны->Компаньон as $related) {
                    $offer['relatedImportIds'][] = (string)$related->attributes()->ТоварИД;
                }
            }

            if ($product->ВДругихЦветах->ВДругомЦвете) {
                foreach ($product->ВДругихЦветах->ВДругомЦвете as $variant) {
                    $offer['variantsImportIds'][] = (string)$variant->attributes()->ТоварИД;
                }
            }

            if ($product->Картинки->КартинкиОригинал->Картинка) {
                foreach ($product->Картинки->КартинкиОригинал->Картинка as $img) {
                    $images[] = (string)$img;
                }
                $offer['images'] = $images;
            }

            foreach ($product->Свойства->Свойство as $attribute) {
                $attrName = (string)$attribute->attributes()->Имя;
                $mode     = Attribute::MODE_SIMPLE;

                if ((string)$attribute->attributes()->Множественное === "Да") {
                    $mode = Attribute::MODE_MULTIPLE;
                }

                if (!array_key_exists($attrName, $options) && $attrName !== 'Цвет для фильтра') {
                    $options[$attrName] = ['mode' => $mode, 'values' => []];
                }

                if ($attribute->Значение) {
                    foreach ($attribute->Значение as $value) {
                        if (!empty(trim((string)$value))) {
                            if ($attrName == 'Цвет для фильтра' || $attrName == 'Особые свойства') {
                                $offer['product_tags'][] = (string)$value;
                                if (!in_array(trim((string)$value), $tags)) {
                                    $tags[] = trim((string)$value);
                                }
                            }
                            $offer['attributes'][$attrName][] = trim((string)$value);
                        }
                        if (!empty($options[$attrName]) && !in_array((string)$value, $options[$attrName]['values'])) {
                            $options[$attrName]['values'][] = trim((string)$value);
                        }
                    }
                }
            }
            $products[] = $offer;
            echo 'Товар: "' . (string)$product->Наименование . '" прочитан'.PHP_EOL;
        }

        $savedBrands = $this->saveBrands($brands);
        if ($savedBrands == 'Done') {
            echo 'Бренды сохранены'.PHP_EOL;
            $savedAttributes = $this->saveAttributes($options);
            if ($savedAttributes == 'Done') {
                echo 'Атрибуты сохранены'.PHP_EOL;
                $savedCategories = $this->saveCategories($categories, $brands);
                if($savedCategories == 'Done') {
                    echo 'Категории сохранены'.PHP_EOL;
                    $savedTags = $this->saveTags($tags);
                    if ($savedTags == 'Done') {
                        echo 'Теги сохранены'.PHP_EOL;
                        $savedProducts = $this->saveProducts($products);
                        if ($savedProducts == 'Done') {
                            echo 'Товары сохранены'.PHP_EOL;
                            $savedRelations = $this->updateRelations($products);
                            if ($savedRelations == 'Done') {
                                echo 'Связи обновлены'.PHP_EOL;
                            } else {
                                echo $savedRelations;
                            }
                        } else {
                            echo $savedProducts;
                        }
                    } else {
                        echo $savedTags;
                    }
                } else {
                    echo $savedCategories;
                }
            } else {
                echo $savedAttributes;
            }
        } else {
            echo $savedBrands;
        }
    }

    private function saveTags($tags):string
    {
        foreach ($tags as $tag) {
            if (!Tag::where('name', $tag)->first()) {
                $request = new Request([
                    'name' => $tag,
                ]);
                app(TagController::class)->store($request);
            }
            echo 'Тег '.$tag. ' записан в базу данных'.PHP_EOL;
        }
        return 'Done';
    }


    /**
     * @throws Throwable
     */
    private function saveBrands($brands): string
    {
        foreach ($brands as $brand) {
            try {
                $request = new Request($brand);
                $upLogos = [];
                foreach ($brand['images'] as $urlImage) {
                    $info = pathinfo($urlImage);
                    if (Storage::exists(Brand::IMAGE_PATH.$brand['name']. '/small_'. $info['filename'] . '.webp')) {
                        continue;
                    }
                    $localFile = '/tmp/'.$info['basename'];
                    copy($urlImage, $localFile);
                    $upLogos[] = new UploadedFile($localFile, $info['basename']);
                }
                if (!empty($upLogos)) {
                    if (count($upLogos) == 1) {
                        $request->files->set('photo', $upLogos[0]);
                    } else {
                        $request->files->set('photo', $upLogos);
                    }
                }
                if ($br = Brand::where('import_id', $brand['import_id'])->first()) {
                    app(BrandController::class)->update($request, $br);
                } else {
                    app(BrandController::class)->store($request);
                }
                echo 'Бренд: "' . $brand['name'] . '" записан в базу данных.'.PHP_EOL;
            } catch (\Exception $exception) {
                echo $exception->getMessage().PHP_EOL;
            }
        }
        return 'Done';
    }

    private function saveAttributes($attributes):string
    {
        foreach ($attributes as $name => $options) {
            try {
                $unit      = null;
                $float     = false;
                $attribute = ['name' => $name, 'type' => Attribute::TYPE_STRING];

                foreach ($options['values'] as $key => $value) {
                    if (preg_match('/\sсм$/u', $value)) {
                        $value = str_replace(' см', '', $value);
                        $unit  = 'см';
                    }
                    if (preg_match('/\sм$/u', $value)) {
                        $value = str_replace(' м', '', $value);
                        $unit = 'м';
                    }

                    if (is_numeric($value) && strpos($value, '.')) {
                        $float = true;
                    } else if (is_numeric($value)) {
                        $attribute['type'] = Attribute::TYPE_INTEGER;
                    }

                    $options['values'][$key] = $value;
                }

                if ($float) {
                    $attribute['type'] = Attribute::TYPE_FLOAT;
                }

                $attribute['unit']     = $unit;
                $attribute['variants'] = implode("\r\n", $options['values']);
                $attribute['mode']     = $options['mode'];

                $request = new AttributeRequest($attribute);
                if ($attr = Attribute::where('name', $name)->first()) {
                    app(AttributeController::class)->update($request, $attr);
                } else {
                    app(AttributeController::class)->store($request);
                }
                echo 'Атрибут: "' . $attribute['name'] . '" сохранен в базу данных'.PHP_EOL;

            } catch (\Exception $exception) {
                echo $exception->getMessage().PHP_EOL;
            }

        }
        return 'Done';
    }

    private function saveCategories($categories, $brands):string
    {
        if (!$mainCategory = Category::where('slug', '=', 'oboi')->first()) {
            $request = new CategoryRequest([
                'name'     => 'Обои',
                'supplier' => 'Обои-центр',
                'title'    => 'Обои для стен',
                'published' => 0,
                'meta'     => [
                    'title' => 'Обои для стен, от компании Обои-центр',
                    'description' => 'Компания Обои-центр, предоставляет широкий выбор обоев, от мировых лидеров производства.'
                ],
            ]);
            app(CategoryController::class)->store($request);
            $mainCategory = Category::where('slug', '=', 'oboi')->first();
            echo "Создана корневая категория".PHP_EOL;
        }

        foreach ($brands as $brand) {
            if (!Category::where('name', $brand['name'])->first()) {
                $request = new CategoryRequest([
                    'parent_id'   => $mainCategory->id,
                    'name'        => $brand['name'],
                    'supplier'    => 'Обои-центр',
                    'title'       => 'Обои фабрики - ' . $brand['name'],
                    'published'   => 0,
                    'description' => $brand['seo_text'] ?? null,
                    'meta'        => [
                        'title'       => 'Обои фабрики: "'.$brand['name'].'", от компании Обои-центр',
                        'description' => 'В данном разделе, компания Обои-центр, представляет обои для стен, фабрики: "' . $brand['name'] . '"'
                    ]
                ]);
                foreach ($brand['images'] as $urlImage) {
                    $info     = pathinfo($urlImage);
                    $contents = file_get_contents($urlImage);
                    $file     = '/tmp/'.$info['basename'];
                    file_put_contents($file, $contents);
                    $request->files->set('photo', [new UploadedFile($file, $info['basename'])]);
                }
                app(CategoryController::class)->store($request);
                echo "Создана брендовая категория ".$brand['name'].PHP_EOL;
            }
        }

        foreach ($categories as $category) {
            $upFiles      = $upImages = [];
            $mainCategory = Category::where('name', $category['brand'])->first();
            $request      = new CategoryRequest([
                'import_id'   => $category['import_id'],
                'parent_id'   => $mainCategory->id,
                'name'        => $category['name'],
                'supplier'    => 'Surgaz',
                'published'   => 0,
                'description' => $category['description'] ?? null,
                'meta'        => [
                    'title' => $category['name'] == 'Клей' ? 'Клей для обоев':'Коллекция обоев «' . $category['name'] . '»',
                    'description' => $category['name'] == 'Клей' ? 'Только качественный клей для обоев, от компании «Обои-центр»':'Коллекция обоев «' . $category['name'] . '» фабрики '. $category['brand'] . ', от компании Обои-центр'
                ]
            ]);
            if (!empty($category['files'])) {
                foreach ($category['files'] as $remoteFile) {
                    $info = pathinfo($remoteFile);
                    $type = $info['extension'] == 'mp4' || $info['extension'] == 'mov' || $info['extension'] == 'm4v' ? File::TYPE_VIDEO : File::TYPE_DOCUMENT;
                    if (Storage::exists(Category::IMAGE_PATH . Str::slug($category['name']) . '/' . $type . '/' . $info['basename'])) {
                        continue;
                    }
                    $localFile  = '/tmp/'.$info['basename'];
                    copy($remoteFile, $localFile);
                    $upFiles[] = new UploadedFile($localFile, $info['basename']);
                }
                $request->files->set('files', $upFiles);
            }
            if (!empty($category['images'])) {
                foreach ($category['images'] as $urlImage) {
                    $info         = pathinfo($urlImage);
                    $storageImage = Category::IMAGE_PATH . Str::slug($category['name']) . '/images/'. 'small_' .str_replace('.'.$info['extension'], '', $info['basename']).'.webp';
                    if (Storage::exists($storageImage) || $info['extension'] == 'tif') {
                        continue;
                    }
                    $localImg = '/tmp/'.$info['basename'];
                    copy($urlImage, $localImg);
                    $upImages[] = new UploadedFile($localImg, $info['basename']);
                }
                if (!empty($upImages)) {
                    $request->files->set('photo', $upImages);
                }
            }
            if(!$currentCategory = Category::where('import_id', $category['import_id'])->first()) {
                app(CategoryController::class)->store($request);
            } else {
                $request->query->remove('description');
                $request->query->remove('meta');
                $request->query->set('published', $currentCategory->published);
                app(CategoryController::class)->update($request, $currentCategory);
            }
            echo "Категория ".$category["name"]." сохранена в базе данных".PHP_EOL;
        }
        return 'Done';
    }

    private function saveProducts($products):string
    {
        foreach ($products as $product) {
            /** @var Category $mainCategory */
            if ($mainCategory = Category::where('import_id', $product['categoryImportId'])->first()) {
                /** @var Brand $brand */
                $brand      = Brand::where('import_id', $product['brandImportId'])->first();
                if (!empty($product['categoriesImportIds'])) {
                    $categories = Category::whereIn('import_id', $product['categoriesImportIds'])->get();
                    $otherCategories = [];
                    if (!$categories->isEmpty()) {
                        foreach ($categories as $cat) {
                            $otherCategories[] = $cat->id;
                        }
                    }
                }
                $request = new ProductRequest([
                    'import_id'         => $product['import_id'],
                    'supplier'          => 'Surgaz',
                    'brand_id'          => $brand->id,
                    'category_id'       => $mainCategory->id,
                    'sku'               => $product['sku'],
                    'price'             => $product['price'],
                    'weight'            => $product['weight'],
                    'volume'            => $product['volume'],
                    'unit'              => $product['unit'],
                    'country'           => $product['country'],
                    'order_variants'    => $product['order_variants'],
                    'quantity'          => $product['quantity'],
                    'name'              => $mainCategory->name. ' ' . $product['sku'],
                ]);

                if (!empty(trim($product['packaging']))) {
                    $request->query->set('packaging', trim($product['packaging']));
                }

                if (!empty(trim($product['amount_in_package']))) {
                    $request->query->set('amount_in_package', trim($product['amount_in_package']));
                }

                if (!empty($otherCategories)) {
                    $request->query->set('product_categories', $otherCategories);
                }

                if (!empty($product['product_tags'])) {
                    $tags = [];
                    foreach ($product['product_tags'] as $tag) {
                        if ($savedTag = Tag::where('name', $tag)->first()) {
                            $tags[] = $savedTag->id;
                        }
                    }
                    if (!empty($tags)) {
                        $request->query->set('product_tags', $tags);
                    }
                }

                if (!empty($product['attributes'])) {
                    $attributes = [];

                    foreach ($product['attributes'] as $name => $values) {
                        /** @var Attribute $attribute */
                        if ($attribute = Attribute::where('name', $name)->first()) {
                            $attributes[$attribute->id] = $values;
                        }
                    }

                    if (!empty($attributes)) {
                        $request->query->set('product_attributes', $attributes);
                    }
                }

                $upImages = [];
                if ($currentProduct = Product::where('import_id', $product['import_id'])->first()) {
                    if (!empty($product['images'])) {
                        foreach ($product['images'] as $i => $urlImage) {
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037972/7723b3c3-5ea5-4496-b963-c8de38f2de2e.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037972/7723b3c3-5ea5-4496-b963-c8de38f2de2e.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037004/2409aa40-a349-4e28-a401-28801a2f7fe4.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037004/2409aa40-a349-4e28-a401-28801a2f7fe4.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037005/1cc17e61-c72e-428e-a0d7-5364103ef28e.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037005/1cc17e61-c72e-428e-a0d7-5364103ef28e.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037025/667858b5-d03f-44e8-ade6-d62c2294e433.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037025/667858b5-d03f-44e8-ade6-d62c2294e433.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037416/24b8d664-4146-4e22-ac4d-06087eba5076.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037416/24b8d664-4146-4e22-ac4d-06087eba5076.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000036794/0ccec6f4-46bb-11ec-9963-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000036794/0ccec6f4-46bb-11ec-9963-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035291/a4dfc61d-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035291/a4dfc61d-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035292/a4dfc620-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035292/a4dfc620-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035293/a4dfc623-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035293/a4dfc623-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035294/a4dfc626-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035294/a4dfc626-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035295/a4dfc629-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035295/a4dfc629-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035296/a4dfc62c-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035296/a4dfc62c-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035297/a4dfc62f-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035297/a4dfc62f-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035298/a4dfc632-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035298/a4dfc632-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035299/a4dfc635-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035299/a4dfc635-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035300/a4dfc638-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035300/a4dfc638-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035301/58298897-03fb-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035301/58298897-03fb-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035302/5829889a-03fb-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035302/5829889a-03fb-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041783/fc5e55de-f5e9-43c8-a573-cd838c8417e0.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041783/fc5e55de-f5e9-43c8-a573-cd838c8417e0.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041784/b96e763b-279b-4949-bbeb-392c0f0e1968.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041784/b96e763b-279b-4949-bbeb-392c0f0e1968.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041785/9e283722-747f-4f1f-9b10-2349f3a5b997.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041785/9e283722-747f-4f1f-9b10-2349f3a5b997.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041786/b842fc05-add2-4b2a-9ea2-ee0b851870e5.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041786/b842fc05-add2-4b2a-9ea2-ee0b851870e5.jpg';
                            }
                            $info = pathinfo($urlImage);
                            $storageImage = Product::IMAGE_PATH . $currentProduct->id . '/images/small_' .str_replace('.'.$info['extension'], '', $info['basename']).'.webp';
                            if (Storage::exists($storageImage)) {
                                if (!Photo::where('name', '=', str_replace('.'.$info['extension'], '', $info['basename']).'.webp')->first()) {
                                    $currentProduct->photos()->create([
                                        "name" => str_replace('.'.$info['extension'], '', $info['basename']) . '.webp',
                                        "sort" => $i,
                                        "path" => Product::getImageParams()['path'] . $currentProduct->id . '/images/'
                                    ]);
                                }
                                continue;
                            } else if ($info['extension'] == 'tif') {
                                continue;
                            }
                            $localImg = '/tmp/'.$info['basename'];
                            copy($urlImage, $localImg);
                            $upImages[] = new UploadedFile($localImg, $info['basename']);
                        }
                        if (!empty($upImages)) {
                            $request->files->set('photo', $upImages);
                        }
                    }

                    if (!empty($currentProduct->description)) {
                        $request->query->remove('description');
                    }
                    app(ProductController::class)->update($request, $currentProduct);
                } else {
                    if (!empty($product['images'])) {
                        foreach ($product['images'] as $urlImage) {
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037972/7723b3c3-5ea5-4496-b963-c8de38f2de2e.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037972/7723b3c3-5ea5-4496-b963-c8de38f2de2e.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037004/2409aa40-a349-4e28-a401-28801a2f7fe4.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037004/2409aa40-a349-4e28-a401-28801a2f7fe4.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037005/1cc17e61-c72e-428e-a0d7-5364103ef28e.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037005/1cc17e61-c72e-428e-a0d7-5364103ef28e.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037025/667858b5-d03f-44e8-ade6-d62c2294e433.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037025/667858b5-d03f-44e8-ade6-d62c2294e433.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000037416/24b8d664-4146-4e22-ac4d-06087eba5076.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000037416/24b8d664-4146-4e22-ac4d-06087eba5076.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000036794/0ccec6f4-46bb-11ec-9963-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000036794/0ccec6f4-46bb-11ec-9963-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035291/a4dfc61d-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035291/a4dfc61d-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035292/a4dfc620-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035292/a4dfc620-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035293/a4dfc623-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035293/a4dfc623-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035294/a4dfc626-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035294/a4dfc626-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035295/a4dfc629-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035295/a4dfc629-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035296/a4dfc62c-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035296/a4dfc62c-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035297/a4dfc62f-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035297/a4dfc62f-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035298/a4dfc632-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035298/a4dfc632-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035299/a4dfc635-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035299/a4dfc635-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035300/a4dfc638-03f8-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035300/a4dfc638-03f8-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035301/58298897-03fb-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035301/58298897-03fb-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000035302/5829889a-03fb-11ec-9962-00155d4d0504.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000035302/5829889a-03fb-11ec-9962-00155d4d0504.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041783/fc5e55de-f5e9-43c8-a573-cd838c8417e0.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041783/fc5e55de-f5e9-43c8-a573-cd838c8417e0.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041784/b96e763b-279b-4949-bbeb-392c0f0e1968.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041784/b96e763b-279b-4949-bbeb-392c0f0e1968.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041785/9e283722-747f-4f1f-9b10-2349f3a5b997.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041785/9e283722-747f-4f1f-9b10-2349f3a5b997.jpg';
                            }
                            if ($urlImage == 'https://st.surgaz.ru/img/product/orig/ut000041786/b842fc05-add2-4b2a-9ea2-ee0b851870e5.jpg') {
                                $urlImage = 'https://st.surgaz.ru/img/product/resize1200/ut000041786/b842fc05-add2-4b2a-9ea2-ee0b851870e5.jpg';
                            }
                            $info = pathinfo($urlImage);
                            if ($info['extension'] == 'tif') {
                                continue;
                            }
                            $localImg = '/tmp/'.$info['basename'];
                            copy($urlImage, $localImg);
                            $upImages[] = new UploadedFile($localImg, $info['basename']);
                        }
                        if (!empty($upImages)) {
                            $request->files->set('photo', $upImages);
                        }
                    }
                    app(ProductController::class)->store($request);
                }

                if (!empty($upImages)) {
                    foreach ($upImages as $image) {
                        if (file_exists($image->getRealPath())) {
                            unlink($image->getRealPath());
                        }
                    }
                }

                echo 'Товар: '. $mainCategory->name . ' ' . $product['sku'] . ' сохранен в базу данных'.PHP_EOL;
            }
        }
        return 'Done';
    }

    private function updateRelations($products): string
    {
        foreach ($products as $product) {
            if (!$dbProduct = Product::where('import_id', $product['import_id'])->first()) {
                continue;
            }
            $related = [];
            /** @var Product $dbProduct */
            if (!empty($product['relatedImportIds'])) {
                foreach ($product['relatedImportIds'] as $importId) {
                    if (!$dbRelated = Product::where('import_id', $importId)->first()) {
                        continue;
                    }
                    if ($dbProduct->related()->where('related_id', '=', $dbRelated->id)->count() == 0) {
                        $related[] = $dbRelated->id;
                    }
                }
                if (!empty($related)) {
                    $dbProduct->related()->attach($related);
                }
            }

            $variant = [];
            if (!empty($product['variantsImportIds'])) {
                foreach ($product['variantsImportIds'] as $importId) {
                    if (!$dbVariant = Product::where('import_id', $importId)->first()) {
                        continue;
                    }
                    if ($dbProduct->variants()->where('variant_id', '=', $dbVariant->id)->count() == 0) {
                        $variant[] = $dbVariant->id;
                    }
                }
                if (!empty($variant)) {
                    $dbProduct->variants()->attach($variant);
                }
            }
        }
        return 'Done';
    }
}
