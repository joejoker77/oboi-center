<?php

namespace App\Console\Commands\Shop;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\UseCases\Admin\Shop\DailyImportService;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Throwable;

class DailyImport extends Command
{
    protected $signature = 'shop:daily-import';

    private DailyImportService $service;

    private $pathFileImport = '/import/surgaz/';

    public function __construct(DailyImportService $service)
    {
        parent::__construct();
        $this->service = $service;
    }


    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle(): bool
    {
        $url = 'https://'.config('app.surgaz_daily');
        $zip = new \ZipArchive;
        if (Storage::exists($this->pathFileImport.'surgaz_daily.zip')) {
            $time = Storage::lastModified($this->pathFileImport.'surgaz_daily.zip');
            if (Carbon::createFromTimestamp($time)->addHours(24) <= Carbon::now()) {
                Storage::put($this->pathFileImport.'surgaz_daily.zip', file_get_contents($url));
                echo "Файл обновлен с сервера.".PHP_EOL;
            }
        } else {
            Storage::put($this->pathFileImport.'surgaz_daily.zip', file_get_contents($url));
            echo 'Файл скачан с сервера.'.PHP_EOL;
        }

        $status = $zip->open(Storage::path($this->pathFileImport.'surgaz_daily.zip'));
        if (!$status) {
            throw new Exception('Не могу открыть архив');
        } else {
            $zip->extractTo(Storage::path($this->pathFileImport));
            $zip->close();
            echo 'Файл извлечен из архива.'.PHP_EOL;
        }

        $xml = XmlParser::load(Storage::path($this->pathFileImport.'daily.xml'));

        $this->service->update($xml);

        echo 'Импорт завершен.'.PHP_EOL;

        return true;
    }
}
