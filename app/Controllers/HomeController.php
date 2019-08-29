<?php

namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller
{
    
    public function index($request, $response)
    {
        return $this->view->render($this->response, 'home.twig', $this->data);

    }

}