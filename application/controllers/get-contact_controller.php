<?php

namespace Aladser\Controllers;

use Aladser\Core\Controller;

class GetContactController extends Controller
{
    public function action_index()
    {
        $this->model->run();
    }
}
