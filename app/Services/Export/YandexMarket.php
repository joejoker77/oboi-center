<?php

namespace App\Services\Export;

use App\Entities\Shop\Category;
use App\Entities\Shop\Photo;
use App\Entities\Shop\Product;
use Illuminate\Support\Facades\Storage;

class YandexMarket
{

    public function generate()
    {
        ob_start();
        $writer   = new \XMLWriter();
        $doc      = new \DOMDocument();

        $doc->load(Storage::path('/import/surgaz/daily.xml'));
        $xpath    = new \DOMXPath($doc);

        $writer->openUri('php://output');
        $writer->startDocument('1.0', 'UTF-8');
        $writer->startDtd('yml_catalog SYSTEM "shops.dtd"');
        $writer->endDtd();

        $writer->startElement('yml_catalog');
        $writer->writeAttribute('date', date('Y-m-d H:i'));

        $writer->startElement('shop');

        $writer->startElement('categories');

        /** @var Category $category */
        foreach (Category::defaultOrder()->withDepth()->get() as $category) {
            $writer->startElement('category');
            $writer->writeAttribute('id', $category->id);
            if ($category->parent && $category->parent->id !== null) {
                $writer->writeAttribute('parentId', $category->parent->id);
            }
            $writer->writeRaw(htmlspecialchars((string)$category->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true));
            $writer->endElement();
        }
        $writer->endElement();

        $writer->startElement('offers');
        /** @var Product $product */
        foreach (Product::active()->orderBy('id')->cursor() as $product) {
            /** @var Photo $image */
            if ($image = $product->photos()->first()) {

                $query     = '//Экспорт/Товары/Товар[@ИД="'.$product->import_id.'"]/Картинки/КартинкиРесайз600/Картинка';
                $imgObject = $xpath->query($query);

                $writer->startElement('offer');

                $writer->writeAttribute('id', $product->id);
                $writer->writeAttribute('type', 'vendor.model');

                $writer->writeElement('typePrefix', 'Товар');
                $writer->writeElement('vendor', $product->brand->name);
                $writer->writeElement('model', $product->sku);
                $writer->writeElement('price', $product->price);
                $writer->writeElement('currencyId', 'RUR');
                $writer->writeElement('categoryId', $product->category->id);
                $writer->writeElement('picture', $imgObject[0]->nodeValue);
                $writer->writeElement('description', 'Обои '. $product->brand->name. ' ' . $product->category->name. ' '. $product->sku);

                $writer->endElement();
            }
        }
        $writer->endElement();
        $writer->fullEndElement();
        $writer->fullEndElement();
        $writer->endDocument();
        return ob_get_clean();
    }
}
