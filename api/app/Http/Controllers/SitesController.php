<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SitesController extends Controller
{
    /**
     * Return list of sites.
     */
    public function index()
    {
        $sites = [
            'fo1.altius.finance',
            'fo2.altius.finance'
        ];

        return json_encode($sites);
    }

}
