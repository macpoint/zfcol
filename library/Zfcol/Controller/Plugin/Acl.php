<?php
/**
 * ACL plugin script, inspired by 
 * http://wolfulus.wordpress.com/2011/12/26/zend-framework-xml-based-acl-part-1/
 * 
 * @package zfcol
 * @category library/Zfcol/Controller/Plugin
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    private $_acl;
    private $_roles;
    private $_res;
    private $_resources = array();

    /**
     * Accepts config XML file 
     * Constructs:
     * 			Roles
     * 			Resources
     * 
     * @param file $configfile
     */
    public function __construct($configfile) {
        $config = simplexml_load_file($configfile);
        $this->_acl = new Zend_Acl ();

        foreach ($config->roles->role as $role) {
            $name = $role->attributes()->name;
            if ($name === "") {
                continue;
            }

            // Gets the list of roles to inherit
            $roles = array();
            if (isset($role->attributes()->inherits)) {
                $roles = $this->_parseRoles((string)
                        $role->attributes()->inherits);
            }

            // Register the new role into the ACL.
            if (sizeof($roles) > 0) {
                $this->_acl->addRole(new Zend_Acl_Role($name), $roles);
            } else {
                $this->_acl->addRole(new Zend_Acl_Role($name));
            }
        }

        // Deny everything by default
        $this->_acl->deny();


        // For each module present in the resource section:
        foreach ($config->resources->module as $module) {
            // Register the module resource as "module"
            $urlBase = $module->attributes()->name;
            $this->_registerResource($urlBase);

            // For each controller present in the current module:
            foreach ($module->controller as $controller) {
                // Register the controller resource as "module:controller"
                $controllerName = (string) $controller->attributes()->name;
                $this->_registerResource($urlBase . ":" . $controllerName);

                // For each action present in the current controller:
                foreach ($controller->action as $action) {
                    // Register the action resource as
                    // "module:controller:action"
                    $actionName = (string) $action->attributes()->name;
                    $this->_registerResource($urlBase . ":" . $controllerName . ":" . $actionName);

                    // Fills up the rules of the current action
                    $url = $urlBase . ":" . $controllerName . ":" . $actionName;
                    $this->_fillRules($action, $url);
                }

                // Fills up the rules of the current controller
                $url = $urlBase . ":" . $controllerName;
                $this->_fillRules($controller, $url);
            }

            // Fills up the rules of the current module
            $url = $urlBase;
            $this->_fillRules($module, $url);
        }

        // save roles
        $this->_roles = $this->_acl->getRoles();
        $this->_res = $this->_acl->getResources();
    }

    /**
     *
     * @param $request Zend_Controller_Request_Http
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        //$role = Zend_Registry::get('role');
        $role = Zend_Auth::getInstance()->getIdentity() ? Zend_Auth::getInstance()->getIdentity()->role : 'guest';

        if (!$this->_isAllowed($role, $request)) {
            // $this->getRequest()->setModuleName('default');
            //$this->getRequest()->setControllerName('error');
            //$this->getRequest()->setActionName('error');
            throw new Zend_Acl_Exception('Permission denied');
        }
    }

    /**
     * Registers a resource by calling new Zend_Acl_Resource
     * 
     * @param $resource string
     * @return boolean
     */
    protected function _registerResource($resource) {
        if (!$this->_isResourceRegistered($resource)) {
           
            $this->_resources["{$resource}"] = true;
            // Adds the resource to the Zend ACL.
            $this->_acl->addResource(new Zend_Acl_Resource($resource));
        }
    }

    /**
     * Checks if a resource is registered.
     *
     * @param $resource string
     * @return boolean
     */
    protected function _isResourceRegistered($resource) {
        return isset($this->_resources["{$resource}"]);
    }

    /**
     * Fills the rules defined by the XML objects.
     *
     * @param $holder SimpleXMLElement
     * @param $resource string
     * @return void
     */
    protected function _fillRules($holder, $resource) {
        if (isset($holder->attributes()->deny)) {
            $roles = $this->_parseRoles((string) $holder->attributes()->deny);
            foreach ($roles as $role) {
                $this->_acl->deny($role, $resource);
            }
        }
        
        if (isset($holder->attributes()->allow)) {
            $roles = $this->_parseRoles((string) $holder->attributes()->allow);
            foreach ($roles as $role) {
                $this->_acl->allow($role, $resource);
            }
        }
    }

    /**
     * Parses the roles from a string separated by commas.
     * Example: "guest,user"
     *
     * @param $value string
     * @return void
     */
    protected function _parseRoles($value) {
        $ret = array();
        $roles = explode(',', $value);
        foreach ($roles as $key => $base) {
            $value = trim($base);
            if ($value !== "") {
                $ret[] = $value;
            }
        }
        return $ret;
    }

    /**
     * Checks if a role can proceed to the specified request.
     *
     * @param $role string
     * @param $request Zend_Controller_Request_Http
     * @return boolean
     */
    protected function _isAllowed($role, $request) {
        if ($this->_acl->hasRole($role) == false) {
            return false;
        }

        $mod = $request->getModuleName();
        $cont = $request->getControllerName();
        $act = $request->getActionName();

        $urls = array(
            0 => strtolower($mod . ":" . $cont . ":" . $act),
            1 => strtolower($mod . ":" . $cont),
            2 => strtolower($mod)
        );

        for ($i = 0; $i < 3; $i++) { // if the user has access to this
            // url returns true
            if ($this->_checkUrl($role, $urls [$i])) {
                return true;
            } else {
                if ($this->_isResourceRegistered($urls [$i])) {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Checks if a role can access the specified resource.
     *
     * @param $role string
     * @param $resource string
     * @return boolean
     */
    protected function _checkUrl($role, $resource) {
        if (!$this->_acl->has($resource)) {
            return false;
        }
        return $this->_acl->isAllowed($role, $resource);
    }

    public function getAcl() {
        return $this->_acl;
    }
}