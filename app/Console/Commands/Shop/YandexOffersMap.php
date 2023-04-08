<?php

namespace App\Console\Commands\Shop;


use Exception;
use Throwable;
use Illuminate\Console\Command;
use App\Services\Export\YandexMarket;
use Illuminate\Support\Facades\Storage;

class YandexOffersMap extends Command
{
    protected $signature = 'export:yandex';

    private YandexMarket $service;

    private $pathFileExport = '/export/';

    public function __construct(YandexMarket $service)
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
        if (Storage::exists($this->pathFileExport.'yandex-map.xml')) {
            Storage::disk()->delete($this->pathFileExport.'yandex-map.xml');
        }

        $yml = $this->service->generate();

        Storage::disk()->put($this->pathFileExport.'yandex-map.xml', $yml);

        echo 'Экспорт завершен.'.PHP_EOL;

        return true;
    }
}
