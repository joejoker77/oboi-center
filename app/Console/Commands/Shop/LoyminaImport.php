<?php

namespace App\Console\Commands\Shop;

use Exception;
use Throwable;
use Illuminate\Console\Command;
use App\UseCases\Admin\Shop\LoyminaImportService;
use Maatwebsite\Excel\Facades\Excel;

class LoyminaImport extends Command
{
    protected $signature = 'shop:loymina-import';

    private $pathFileImport = '/import/loymina/';


    /**
     * @throws Exception
     * @throws Throwable
     */
    public function handle(): bool
    {
        Excel::import(new LoyminaImportService, $this->pathFileImport.'ImportMilassaLoymina.xlsx');

        return true;
    }
}
