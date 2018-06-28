<?php

namespace framework\core;

/**
 * Class Controller
 * @package framework
 */
class Controller
{
    protected $id;
    protected $view;

    /**
     * Controller constructor.
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->view = new View();
    }

    public function render($view, $options = [])
    {
        return $this->view->render($this->id, $view, $options);
    }
}