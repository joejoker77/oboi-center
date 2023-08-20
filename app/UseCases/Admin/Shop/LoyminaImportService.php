<?php

namespace App\UseCases\Admin\Shop;


use App\Entities\Shop\Tag;
use App\Http\Controllers\Admin\Shop\ProductController;
use App\Http\Requests\Admin\Shop\ProductRequest;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use App\Entities\Shop\Brand;
use Illuminate\Http\Request;
use App\Entities\Shop\Product;
use App\Entities\Shop\Category;
use App\Entities\Shop\Attribute;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\OnEachRow;
use App\Http\Requests\Admin\Shop\CategoryRequest;
use App\Http\Controllers\Admin\Shop\BrandController;
use App\Http\Controllers\Admin\Shop\CategoryController;
use function Symfony\Component\Translation\t;

/**
 *
 */
class LoyminaImportService implements OnEachRow
{

    /**
     * @var Attribute|null
     */
    private Attribute|null $attrMaterialBase    = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrMaterialCover   = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrTypeCover       = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrColor           = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrLength          = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrWidth           = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrRepeat          = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrTexture         = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrResistanceLight = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrDrawingDocking  = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrPictureSize     = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrRoom            = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrStyle           = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrForChildren     = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrWaterResistance = null;
    /**
     * @var Attribute|null
     */
    private Attribute|null $attrForPainting     = null;


    /**
     *
     */
    public function __construct()
    {
        $this->attrMaterialBase    = Attribute::where('name', 'Материал основы')->first();
        $this->attrMaterialCover   = Attribute::where('name', 'Материал покрытия')->first();
        $this->attrTypeCover       = Attribute::where('name', 'Тип обоев')->first();
        $this->attrColor           = Attribute::where('name', 'Цвет')->first();
        $this->attrLength          = Attribute::where('name', 'Длина рулона')->first();
        $this->attrWidth           = Attribute::where('name', 'Ширина рулона')->first();
        $this->attrRepeat          = Attribute::where('name', 'Повтор рисунка')->first();
        $this->attrTexture         = Attribute::where('name', 'Фактура обоев')->first();
        $this->attrResistanceLight = Attribute::where('name', 'Устойчивость к свету')->first();
        $this->attrDrawingDocking  = Attribute::where('name', 'Стыковка рисунка')->first();
        $this->attrPictureSize     = Attribute::where('name', 'Размер рисунка')->first();
        $this->attrRoom            = Attribute::where('name', 'Помещение')->first();
        $this->attrStyle           = Attribute::where('name', 'Стиль')->first();
        $this->attrForChildren     = Attribute::where('name', 'Для детской')->first();
        $this->attrWaterResistance = Attribute::where('name', 'Водостойкость')->first();
        $this->attrForPainting     = Attribute::where('name', 'Под покраску')->first();
    }

    /**
     * @param Row $row
     * @return void|null
     */
    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $row      = $row->toArray();

        if ($rowIndex == 1) {
            return null;
        }

        $brand      = $row[0];
        $category   = $row[1];
        $sku        = $row[2];
        $colorTags  = array_map('trim', explode(',', $row[6]));
        $price      = $row[20];
        $attributes = [];

        $colors = array_map('trim', explode(',', $row[7]));
        $rooms  = array_map('trim', explode(',', $row[15]));
        $styles = array_map('trim', explode(',', $row[16]));
        $child  = array_map('trim', explode(',', $row[17]));

        foreach ($colors as $color) {
            if (in_array(mb_convert_case($color, MB_CASE_TITLE, 'UTF-8'), $this->attrColor->variants)) {
                $attributes[$this->attrColor->id][] = mb_convert_case($color, MB_CASE_TITLE, 'UTF-8');
            }
        }

        foreach ($rooms as $room) {
            if (in_array(mb_convert_case($room, MB_CASE_TITLE, 'UTF-8'), $this->attrRoom->variants)) {
                $attributes[$this->attrRoom->id][] = mb_convert_case($room, MB_CASE_TITLE, 'UTF-8');
            }
        }

        foreach ($styles as $style) {
            if (in_array(mb_convert_case($style, MB_CASE_TITLE, 'UTF-8'), $this->attrColor->variants)) {
                $attributes[$this->attrStyle->id][] = mb_convert_case($style, MB_CASE_TITLE, 'UTF-8');
            }
        }

        foreach ($child as $ch) {
            if (in_array(mb_convert_case($ch, MB_CASE_TITLE, 'UTF-8'), $this->attrForChildren->variants)) {
                $attributes[$this->attrForChildren->id][] = mb_convert_case($ch, MB_CASE_TITLE, 'UTF-8');
            }
        }
        $attributes[$this->attrMaterialBase->id]    = $row[3];
        $attributes[$this->attrMaterialCover->id]   = $row[4];
        $attributes[$this->attrTypeCover->id]       = $row[5];
        $attributes[$this->attrLength->id]          = (float)$row[8];
        $attributes[$this->attrWidth->id]           = $row[9];
        $attributes[$this->attrRepeat->id]          = $row[10];
        $attributes[$this->attrTexture->id]         = $row[11];
        $attributes[$this->attrResistanceLight->id] = $row[12];
        $attributes[$this->attrDrawingDocking->id]  = $row[13];
        $attributes[$this->attrPictureSize->id]     = $row[14];
        $attributes[$this->attrWaterResistance->id] = $row[18];
        $attributes[$this->attrForPainting->id]     = $row[19] !== 'Нет' ? $row[19] : null;

        if (!$dbBrand = Brand::where('name', $brand)->first()) {
            $dbBrand = $this->createBrand($brand);
            echo 'Бренд '. $brand. ' успешно создан'.PHP_EOL;
        }

        if (!$brandCategory = Category::where('name', $brand)->first()) {
            $brandCategory = $this->createBrandCategory($brand);
            echo 'Категория бренда '. $brand . ' Успешно создана'.PHP_EOL;
        }

        if (!$dbCategory = Category::where('slug', Str::slug($category))->first()) {
            $dbCategory = $this->createCategory($category, $brandCategory);
            echo 'Категория коллекции ' . $category . ' успешно создана'.PHP_EOL;
        }

        if (!$dbProduct = Product::where('sku', $sku)->first()) {
            $dbProduct = $this->createProduct($dbBrand, $dbCategory, $sku, $colorTags, $price, $attributes);
            echo 'Продукт '. $dbCategory->name . ' ' . $sku . ' успешно создан'.PHP_EOL;
        }

    }

    private function createProduct($brand, $category, $sku, $colorTags, $price, $attributes): Product
    {
        $imageName = str_replace('/','_', $sku);
        $imagePath = realpath('./storage/app/public/import/loymina/src/products/'.$imageName.'.webp');
        $request   = new ProductRequest([
            'supplier'          => 'Loymina',
            'brand_id'          => $brand->id,
            'category_id'       => $category->id,
            'sku'               => $sku,
            'price'             => $price,
            'country'           => "Россия",
            'order_variants'    => "Заказ",
            'quantity'          => 0,
            'name'              => $category->name. ' ' . $sku,
        ]);
        if (!empty($colorTags)) {
            $tags = [];
            foreach ($colorTags as $color) {
                if ($savedTag = Tag::where('name', mb_convert_case($color, MB_CASE_TITLE, 'UTF-8'))->first()) {
                    $tags[] = $savedTag->id;
                }
            }
            if (!empty($tags)) {
                $request->query->set('product_tags', $tags);
            }
        }
        $request->query->set('product_attributes', $attributes);

        if (file_exists($imagePath)) {
            $request->files->set('photo', [new UploadedFile($imagePath, $imageName.'.webp')]);
        }

        $title = 'Обои '.$category->name. ' ' . $sku.' от компании "Обои Центр"';
        $description = 'Купить или заказать обои '.$category->name. ' ' . $sku.' в компании "Обои Центр"';

        $request->query->set('meta', ['title' => $title, 'description' => $description]);
        return app(ProductController::class)->store($request);
    }

    /**
     * @param $category
     * @param $brandCategory
     * @return Category
     */
    private function createCategory($category, $brandCategory): Category
    {
        $imagesPath = realpath('./storage/app/public/import/loymina/src/categories/'.$category.'/');
        $filesPath  = realpath('./storage/app/public/import/loymina/src/files/');
        $request    = new CategoryRequest([
            'parent_id'   => $brandCategory->id,
            'name'        => $category,
            'supplier'    => 'oboi-center',
            'published'   => 0,
            'description' => null,
            'meta'        => [
                'title' => 'Коллекция обоев «' . $category . '»',
                'description' => 'Коллекция обоев «' . $category . '» фабрики '. $category . ', от компании Обои-центр'
            ]
        ]);

        if (file_exists($filesPath.'/'.$category.'.pdf')) {
            $request->files->set('files', [new UploadedFile($filesPath.'/'.$category.'.pdf', $category.'.pdf')]);
        }

        if (trim($imagesPath) !== '' && !$this->dir_is_empty($imagesPath)) {
            $upImages = [];
            $f = scandir($imagesPath);
            foreach ($f as $file) {
                if (preg_match('/\.(webp)/', $file)) {
                    $info       = pathinfo($file);
                    $upImages[] = new UploadedFile($imagesPath.'/'.$file, $info['basename']);
                }
            }

            if (!empty($upImages)) {
                $request->files->set('photo', $upImages);
            }
        }

        return app(CategoryController::class)->store($request);
    }

    /**
     * @param string $brand
     * @return Category
     */
    private function createBrandCategory(string $brand): Category
    {
        $pathLogos = realpath('./storage/app/public/import/loymina/src/logos/');

        $mainCategory = Category::where('name', 'Обои')->first();
        $seoText      = $brand == 'Loymina'
            ? '<p>Флизелиновые обои и панно от компании Loymina Group — это эксклюзивный дизайн, построенный на сотрудничестве с мировыми тренд-агентствами, премиальное качество и творческий подход к работе, переносящий отделку стен из прагматической области в сферу высокого искусства. Регулярное пополнение линеек новыми коллекциями обоев позволяет оставаться на волне интерьерной моды, отвечая вкусам самых искушенных поклонников интерьерного дизайна.</p><p>Компания Loymina Group производит флизелиновые обои и цифровые панно абсолютной экологической чистоты, гипоаллергенные и безопасные для здоровья и окружающей среды, поскольку в составе готовых изделий полностью отсутствуют ПВХ и пластификаторы, а при печати применяются только безвредные краски на водной основе.</p><p>Loymina — линейка высококачественных флизелиновых обоев и цифровых панно премиум-класса. Коллекции этого бренда разрабатываются совместно с европейскими и российскими дизайнерами и художниками в соответствии с самыми актуальными трендами мирового интерьерного дизайна.</p><p>Все паттерны обоев Loymina наносятся по текстильным технологиям, что обеспечивает высокую яркость красок и детализацию рисунков на полотне, а также тактильно приятную рельефную фактуру. Безопасный экологичный материал премиального качества обладает высочайшими эксплуатационными характеристиками, такими как воздухопроницаемость, исключающая появление грибка и плесени, повышенная светоустойчивость и износостойкость.</p>'
            : '<p>Milassa — линейка флизелиновых обоев и цифровых панно middle-сегмента, сочетающая высокое качество, экологичность и эксклюзивный дизайн с доступной стоимостью.</p><p>Коллекции Milassa отличает современный лаконичный и универсальный дизайн в духе актуальных тенденций минимализма. Разнообразие фоновых обоев, представленных в каталогах линейки, делает их великолепной альтернативой интерьерной краске, а богатство цветовой палитры позволяет подобрать идеальное колористическое решение для интерьеров любого стиля и назначения.</p>';

        $request = new CategoryRequest([
            'parent_id'   => $mainCategory->id,
            'name'        => $brand,
            'supplier'    => 'Обои-центр',
            'title'       => 'Обои фабрики - ' . $brand,
            'published'   => 0,
            'description' => $seoText,
            'meta'        => [
                'title'       => 'Обои фабрики: "'.$brand.'", от компании Обои-центр',
                'description' => 'В данном разделе, компания Обои-центр, представляет обои для стен, бренда: "' . $brand . '"'
            ]
        ]);

        if (file_exists($pathLogos.'/'.$brand.'.webp')) {
            $request->files->set('photo', [new UploadedFile($pathLogos.'/'.$brand.'.webp', $brand.'.webp')]);
        }

        return app(CategoryController::class)->store($request);
    }

    /**
     * @param string $brand
     * @return Brand
     */
    private function createBrand(string $brand):Brand
    {
        $pathLogos    = realpath('./storage/app/public/import/loymina/src/logos/');
        $seoText      = $brand == 'Loymina'
            ? '<p>Флизелиновые обои и панно от компании Loymina Group — это эксклюзивный дизайн, построенный на сотрудничестве с мировыми тренд-агентствами, премиальное качество и творческий подход к работе, переносящий отделку стен из прагматической области в сферу высокого искусства. Регулярное пополнение линеек новыми коллекциями обоев позволяет оставаться на волне интерьерной моды, отвечая вкусам самых искушенных поклонников интерьерного дизайна.</p><p>Компания Loymina Group производит флизелиновые обои и цифровые панно абсолютной экологической чистоты, гипоаллергенные и безопасные для здоровья и окружающей среды, поскольку в составе готовых изделий полностью отсутствуют ПВХ и пластификаторы, а при печати применяются только безвредные краски на водной основе.</p><p>Loymina — линейка высококачественных флизелиновых обоев и цифровых панно премиум-класса. Коллекции этого бренда разрабатываются совместно с европейскими и российскими дизайнерами и художниками в соответствии с самыми актуальными трендами мирового интерьерного дизайна.</p><p>Все паттерны обоев Loymina наносятся по текстильным технологиям, что обеспечивает высокую яркость красок и детализацию рисунков на полотне, а также тактильно приятную рельефную фактуру. Безопасный экологичный материал премиального качества обладает высочайшими эксплуатационными характеристиками, такими как воздухопроницаемость, исключающая появление грибка и плесени, повышенная светоустойчивость и износостойкость.</p>'
            : '<p>Milassa — линейка флизелиновых обоев и цифровых панно middle-сегмента, сочетающая высокое качество, экологичность и эксклюзивный дизайн с доступной стоимостью.</p><p>Коллекции Milassa отличает современный лаконичный и универсальный дизайн в духе актуальных тенденций минимализма. Разнообразие фоновых обоев, представленных в каталогах линейки, делает их великолепной альтернативой интерьерной краске, а богатство цветовой палитры позволяет подобрать идеальное колористическое решение для интерьеров любого стиля и назначения.</p>';

        $brandRequest = new Request([
            'name' => $brand,
            'supplier' => 'oboi-center',
            'seo_text' => $seoText,
            'meta' => [
                'title' => 'Бренд: «' . $brand . '»',
                'description' => 'На данной странице представлена продукция бренда: «' . $brand . '»',
            ]
        ]);
        if (file_exists($pathLogos.'/'.$brand.'.webp')) {
            $brandRequest->files->set('photo', [new UploadedFile($pathLogos.'/'.$brand.'.webp', $brand.'.webp')]);
        }
        return app(BrandController::class)->store($brandRequest);
    }

    private function dir_is_empty($dir) : bool
    {
        $handle = opendir($dir);
        if (!$handle) return false;
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                closedir($handle);
                return false;
            }
        }
        closedir($handle);
        return true;
    }
}
