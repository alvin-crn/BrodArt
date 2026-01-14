<?php

namespace App\Service;

use App\Entity\Size;
use App\Entity\Color;
use App\Entity\Category;

class SearchService
{
    /**
    * @var string
    */
    public $string = "";
    
    /**
    * @var Category[]
    */
    public $categories = [];

    /**
    * @var Color[]
    */
    public $couleurs = [];

    /**
    * @var Size[]
    */
    public $tailles = [];
}