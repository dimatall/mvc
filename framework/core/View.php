<?php

namespace framework\core;


class View
{

    /**
     * Render view
     * @param $controllerId
     * @param $viewId
     * @param $options
     * @return string
     */
    public function render($controllerId, $viewId, $options)
    {
        $layout = $this->getViewFile('layout', 'main');
        $options['_body'] = $this->getViewFile($controllerId, $viewId);

        ob_start();

        extract($options, EXTR_OVERWRITE);

        require $layout;

        return ob_get_clean();
    }

    /**
     * Get file path
     * @param $folder
     * @param $file
     * @return string
     */
    public function getViewFile($folder, $file)
    {
        return __DIR__ . '/../../app/Views/' . $folder . '/' . $file . '.php';
    }
}