<?php

namespace App\Http\Controllers;

use App\Enums\PatentStatusEnum;
use App\Enums\UtilityModelStatusEnum;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $latestPatents = \App\Models\Patent::select(['id', 'invention', 'slug', 'description', 'images'])
            ->latest('publication_date')
            ->where('status', PatentStatusEnum::APPROVED)
            ->take(3)
            ->get();
        $latestUtilityModels = \App\Models\UtilityModel::select(['id', 'title', 'slug', 'description', 'images'])
            ->latest('publication_date')
            ->where('status', UtilityModelStatusEnum::APPROVED)
            ->take(3)
            ->get();
        return Inertia::render('Home', [
            'patents' => $latestPatents,
            'utilityModels' => $latestUtilityModels,
        ]);
    }
}
