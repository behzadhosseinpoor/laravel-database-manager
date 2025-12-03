<?php
/** @noinspection PhpUnused */

namespace BehzadHosseinPoor\DatabaseManager\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return Factory|View
     */
    public function index(): Factory|View
    {
        return view('database-manager::layout', [
            'isDownForMaintenance' => App::isDownForMaintenance(),
        ]);
    }
}