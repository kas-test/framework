<?php

/**
 * 
 * 
 * @copyright KASalgado 2011 - 2015
 * @author Kleber Salgado
 * @version 1.0
 */
class Loader {
    
    private $smarty;
    
    private $vars;
    
    public function __construct()
    {
        $smartyMachine = new SmartyMachine();
        $this->smarty = $smartyMachine->loadSmarty();
        $this->vars = new Vars();
    }

    public function get() {
        $this->smarty->assign('basename', APPLICATION_BASENAME);
        $this->setLangVars();
        
        if ($this->vars->get('ctr') && $this->vars->get('ctr') == 'ajaxCommander') {
            $template = $this->getAjaxTemplate();
            
            if ($template['response'] == 'json') {
                print($template['data']);
            } else {
                $this->smarty->display($template['data']);
            }
        } else {
            $this->loadResources();
            $this->smarty->display($this->getTemplate());
        }
    }
    
    private function getTemplate()
    {
        $data = array('ctr' => 'startIndex');

        if ($gets = $this->vars->isGet()) {
            foreach ($gets as $key => $value) {
                $data[$key] = $value;
            }
        }

        if ($posts = $this->vars->isPost()) {
            foreach ($posts as $key => $value) {
                $data[$key] = $value;
            }
        }

        $dispatcher = new Dispatcher();
        $controller = $dispatcher->setController($data);

        // Create variables to assign CSS and JS resources
        $this->smarty->assign('jsFile', $controller['js']);
        $this->smarty->assign('cssFile', $controller['css']);

        // Create variable to assign the template
        $this->smarty->assign('template', $dispatcher->loadTemplate());

        // Assign variables which are passed as parameters
        if ($controller['vars']) {
            foreach ($controller['vars'] as $key => $value) {
                $this->smarty->assign($key, $value);
            }
        }
        
        return 'index.tpl';
    }
    
    private function getAjaxTemplate()
    {
        foreach ($this->vars->isPost() as $key => $value) {
            $data[$key] = $value;
        }

        $dispatcher = new Dispatcher();
        $controller = $dispatcher->setController($data);

        // Assign variables for the template
        if ($controller['vars']) {
            foreach ($controller['vars'] as $key => $value) {
                $this->smarty->assign($key, $value);
            }
        }
        
        if (isset($data['response'])) {
            if ($data['response'] == 'html') {
                return array(
                    'response' => 'html',
                    'data' => $dispatcher->loadTemplate(),
                );
            } elseif ($data['response'] == 'json') {
                return array(
                    'response' => 'json',
                    'data' => json_encode($controller['vars']),
                );
            } else {
                throw new Exception('Ajax dataType must be html or json');
            }
        } else {
            throw new Exception('response data must be given');
        }
    }
    
    private function loadResources()
    {
        $loadResources = new LoadResourceFiles();
        $this->smarty->assign('cssFiles', $loadResources->loadCssFiles());
        $this->smarty->assign('jsFiles', $loadResources->loadJsFiles());
    }
    
    private function setLangVars()
    {
        $languages = new Languages();
        $lang = $languages->prepare();

        foreach ($lang as $key => $value) {
            $this->smarty->assign('lang_' . $key, $value);
        }
    }
}
