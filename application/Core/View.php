<?php

namespace Aladser\Core;

class View
{
    public function generate($template_view, $content_view, $content_css, $content_js, $pageName, $data = null)
    {
        include "application/Views/$template_view";
    }
}