<?php
    class RegController extends Controller { 
        function action_index() { 
            $this->view->generate('template_view.php', 'reg_view.php', 'reg.css', 'reg.js', 'Регистрация'); 
        } 
    }
?>