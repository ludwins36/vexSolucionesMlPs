<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Vex_soluciones_mercado_libre extends Module
{
    protected $config_form = false;
    public $template_dir;
    public $path;

    const CONFIG_LICENSE = "VEX_MERCADO_LIBRE_LICENCE";
    const CONFIG_ACTIVE = "VEX_MERCADO_LIBRE_ACTIVATED";
    const CONFIG_DATE_ACTIVE = "VEX_MERCADO_LIBRE_DATE_ACTIVE";
    const CONFIG_COUNTRY = "VEX_MERCADO_LIBRE_COUNTRY";
    const CONFIG_APP_ID = "VEX_MERCADO_LIBRE_APP_ID";
    const CONFIG_SECRET_ID = "VEX_MERCADO_LIBRE_SECRET_ID";
    const CONFIG_CODE = "VEX_MERCADO_LIBRE_CODE";
    const CONFIG_ACCEST_TOKEN = "VEX_MERCADO_LIBRE_ACCEST_TOKEN";
    const CONFIG_REFRES_TOKEN = "VEX_MERCADO_LIBRE_REFRES_TOKEN";
    const CONFIG_EXPIRED_DATE = "VEX_MERCADO_LIBRE_EXPIRED_DATE";
    const CONFIG_USER_ID = "VEX_MERCADO_LIBRE_USER_ID";
    const CONFIG_SITE_ID = "VEX_MERCADO_LIBRE_SITE_ID";
    
    
    const CONFIG_API_BASE = "https://api.mercadolibre.com/";
    const CONFIG_API_BASE_AUTH = "https://auth.mercadolibre.com.";



    public function __construct()
    {
        $this->name = 'vex_soluciones_mercado_libre';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'vex_soluciones';
        $this->need_instance = 1;
        
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->template_dir = '../../../../modules/'.$this->name;
        $this->path = __FILE__;

        parent::__construct();

        $this->displayName = $this->l('vex mercado libre');
        $this->description = $this->l('el mejor complemento para fusionar tu web mercado libre con tu tienda online');

        $this->confirmUninstall = $this->l('seguro que deseas desinstalar el modulo?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('VEX_SOLUCIONES_MERCADO_LIBRE_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');
        $this->createAdminTabs();
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('VEX_SOLUCIONES_MERCADO_LIBRE_LIVE_MODE');

        // include(dirname(__FILE__).'/sql/uninstall.php');
        $this->uninstallTab();

        return parent::uninstall();
    }

    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once(dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php');

        // Build dynamically the controller name
        $controller_name = $this->name . $hook_name . 'Controller';

        // Instantiate controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        // Return the controller
        return $controller;
    }



    /**
     * Load the configuration form
     */
    public function getContent()
    {
         /**
         * If values have been submitted in the form, process.
         */
        $controller = $this->getHookController('getContent');
        return $controller->run($this->local_path, $this->tab, $this->table, $this->identifier);
    }

    public function createAdminTabs()
    {
        $langs = Language::getLanguages();

        $tab0 = new Tab();
        $tab0->class_name = "AdminMLConfig";
        $tab0->module = $this->name;
        $tab0->id_parent = 0;
        foreach ($langs as $l) {
            $tab0->name[$l['id_lang']] = $this->l('Mercado Libre Configuración');
        }
        $tab0->save();
        $main_tab_id = $tab0->id;

        unset($tab0);

        $tab1 = new Tab();
        $tab1->class_name = "AdminLoginMercadoLibre";
        $tab1->module = $this->name;
        $tab1->id_parent = $main_tab_id;
        foreach ($langs as $l) {
            $tab1->name[$l['id_lang']] = $this->l('Configuracion de tienda');
        }
        $tab1->save();

        $tab2 = new Tab();
        $tab2->class_name = "AdminML";
        $tab2->module = $this->name;
        $tab2->id_parent = $main_tab_id;
        foreach ($langs as $l) {
            $tab2->name[$l['id_lang']] = $this->l('Mercado Libre');
        }
        $tab2->save();
    }

    public function uninstallTab()
    {
        $prefix = '';

        $tab_id = Tab::getIdFromClassName("AdminLoginMercadoLibre" . $prefix);
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminML" . $prefix);
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'VEX_SOLUCIONES_MERCADO_LIBRE_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

   

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}