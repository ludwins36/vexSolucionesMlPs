<?php

/**
 * 2007-2019 PrestaShop
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
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


class vex_soluciones_mercado_libregetContentController
{
    public function __construct($module, $file, $path)
    {
        require_once dirname($file) . '/classes/class-license.php';
        // require_once dirname($file) . '/sql/request.php';
        // require_once dirname($file) . '/resources/data_locator.php';
        require_once dirname($file) . '/classes/class-request.php';
        // require_once dirname($file) . '/classes/class-polyline.php';

        $this->file = $file;
        $this->module = $module;
        $this->context = Context::getContext();
        $this->_path = $path;
    }

    /**
     * Load the configuration form
     */
    public function run($localPath, $tab, $table, $identifier)
    {
        /**
         * If values have been submitted in the form, process.
         */

          /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitVex_soluciones_mercado_libreModule')) == true) {
            $this->postProcess();
        }


        return $this->renderForm($tab, $table, $identifier);

    }


    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm($tab, $table, $identifier)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $table;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $identifier;
        $helper->submit_action = 'submitVex_soluciones_mercado_libreModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->module->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($this->getConfigForm());
        
    }

    	/**  
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{

		$licenseCtrl = new VexMercadoLibreLicense($this->module);
		$licenseCtrl->verify();

		$license   = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_LICENSE, null, null, null, '');
		$activated = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACTIVE, null, null, null, '0');


        return $this->_settings_fields();
		if (!empty($license) && $activated) {
		}
        return $this->licenseFields();
	}


    private function licenseFields()
    {
        $license_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('License'),
                    'icon'  => 'icon-user'
                ),
                'input'  => array(
                    array(
                        'type'        => 'text',
                        'label'       => $this->module->l('License'),
                        'name'        => Vex_soluciones_mercado_libre::CONFIG_LICENSE,
                        'class'       => 'fixed-width-xxl',
                        'required'    => true
                    )
                ),
                'submit' => array(
                    'title' => $this->module->l('Save')
                )
            )
        );

        return array($license_form);
    }


    	/**
	 * Create the structure of your form.
	 */
	protected function _settings_fields()
	{
		$setings = array(
			'form' => array(
				'legend' => array(
					'title' => $this->module->l("Configuracion"),
                    'icon' => 'icon-cogs',
				),
				'input' => array(
                    array(
                        'type'    => 'select',
                        'label'   => $this->module->l('Pais'),
                        'required' => true,
                        'name'    => Vex_soluciones_mercado_libre::CONFIG_COUNTRY,
                        'desc'    => $this->module->l('Selecciones el pais de su cuenta Mercado Libre.'),
                        'options' => array(
                            'query' => $this->getStatuses(),
                            'id'   => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'name' => 'TITLE_APP_DATA',
                        'label' => $this->module->l('Datos de APP, Mercado Libre'),
                        'type' => 'title',
                        'desc' => $this->module->l('Para poder utilizar este modulo es necesario crear una app en https://developers.mercadolibre.com/'),
                    ),
                    array(
                        'name' => Vex_soluciones_mercado_libre::CONFIG_APP_ID,
                        'label' => $this->module->l('APP ID'),
                        'type' => 'text',
                        'class' => 'live-field',
                        'required' => true,
                        'col' => 4
        
                    ),
                    array(
                        'name' => Vex_soluciones_mercado_libre::CONFIG_SECRET_ID,
                        'label' => $this->module->l('APP ID SECRET'),
                        'type' => 'text',
                        'class' => 'live-field',
                        'required' => true,
                        'col' => 4
        
                    ),
                    array(
                        'name' => 'URL_RETURN',
                        'label' => $this->module->l('Url de retorno'),
                        'type' => 'title',
                        'desc' => $this->context->link->getAdminLink('AdminLoginMercadoLibre', false, []),

                    ),
				),
				'submit' => array(
					'title' => $this->module->l('GUARDAR'),
				),
			),
		);
		return array($setings);
    }
    
    
    private function loginMercadoLibre(){
        $curl = curl_init();
        $url = Vex_soluciones_mercado_libre::CONFIG_API_BASE_AUTH . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_COUNTRY) . '/authorization?response_type=code&client_id=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_APP_ID);
        return $url;

        
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {

        return array(
            'VEX_SOLUCIONES_MERCADO_LIBRE_LIVE_MODE' => Configuration::get('VEX_SOLUCIONES_MERCADO_LIBRE_LIVE_MODE', true),
            'VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_EMAIL' => Configuration::get('VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_EMAIL', 'contact@prestashop.com'),
            'VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_PASSWORD' => Configuration::get('VEX_SOLUCIONES_MERCADO_LIBRE_ACCOUNT_PASSWORD', null),
            'TITLE_APP_DATA' => Configuration::get('TITLE_APP_DATA', null),
            Vex_soluciones_mercado_libre::CONFIG_LICENSE => Configuration::get(Vex_soluciones_mercado_libre::CONFIG_LICENSE, null),
            Vex_soluciones_mercado_libre::CONFIG_COUNTRY => Configuration::get(Vex_soluciones_mercado_libre::CONFIG_COUNTRY, null),
            Vex_soluciones_mercado_libre::CONFIG_SECRET_ID => Configuration::get(Vex_soluciones_mercado_libre::CONFIG_SECRET_ID, null),
            Vex_soluciones_mercado_libre::CONFIG_APP_ID => Configuration::get(Vex_soluciones_mercado_libre::CONFIG_APP_ID, null),
        );
    }
    
    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        if(!empty(Vex_soluciones_mercado_libre::CONFIG_COUNTRY)){
            Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_COUNTRY, Tools::getValue(Vex_soluciones_mercado_libre::CONFIG_COUNTRY));
        }

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }

        if(Configuration::get(Vex_soluciones_mercado_libre::CONFIG_SECRET_ID, null, null, null, null, false)){
            $login = $this->loginMercadoLibre();
            VexMLRequest::resetData();
            Tools::redirectLink($login);

        }
    }

    private function getStatuses()
	{
        $curl = new VexMercadoLibreCurl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setOpt(64, false);
        $rest = $curl->get(Vex_soluciones_mercado_libre::CONFIG_API_BASE . 'classified_locations/countries');
        $rest = json_decode($rest->response);

		$list_status = array();
		foreach ($rest as $status) {
			$list_ = array(
				'id' => $status->id,
				'name' => $status->name
			);
			array_push($list_status, $list_);
		}
		return $list_status;
	}

}