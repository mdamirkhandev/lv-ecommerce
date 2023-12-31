<?php

use App\Models\Category;

function getCategories()
{
    return Category::orderBy('name', 'ASC')
        ->with('subCategory')
        ->where('Status', 1)
        ->where('ShowMenu', 'Yes')
        ->get();
}
