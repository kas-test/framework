<?php

/**
 * This class tries to hide the elementare server variables to manage sessions
 * through simple functions.
 * 
 * @copyright KASalgado 2011 - 2015
 * @author Kleber Salgado
 * @revision KAS-Test
 * @version 1.0
 */
class Session
{
    /**
     * 
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set($type, $data = '')
    {
        if (is_array($type)) {
            foreach ($type as $key => $value) {
                $_SESSION[$key] = $value;
            }
        } else {
            $_SESSION[$type] = $data;
        }
    }
    
    public function get($type)
    {
        if (is_array($type)) {
            $result = array();
            
            foreach ($type as $key) {
                if (isset($_SESSION[$key])) {
                    $result[$key] = $_SESSION[$key];
                }
            }
            return $result;
        } else {
            return $_SESSION[$type];
        }
    }
    
    public function exists($name)
    {
        return isset($_SESSION[$name]) ? true : false;
    }
    
    public function delete($type)
    {
        if (is_array($type)) {
            foreach ($type as $key) {
                unset($_SESSION[$key]);
            }
        } else {
            unset($_SESSION[$type]);
        }
    }
    
    public function deleteAll()
    {
        session_unset();
    }
    
    public function destroy()
    {
        session_destroy();
    }
}
