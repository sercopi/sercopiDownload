<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mangapark;

class MangaparkScrapperController extends Controller
{
    public function info($mangaName)
    {
        $mangapark = new Mangapark();
        return $mangapark->getSeriesInfo($mangaName);
    }
}
