<?php

require_once 'class-curl.php';


class VexMercadoLibreLicense
{

	const LICENSE_SECRET_KEY = '587423b988e403.69821411';
	const LICENSE_SERVER_URL = 'https://www.pasarelasdepagos.com';
	const ITEM_REFERENCE = 'Visa checkout - Prestashop Gateway';


	protected $module = null;


	protected $context;


	public function __construct($module)
	{
		$this->module = $module;
		$this->context = Context::getContext();
		// $this->verify();
	}


	public function verify()
	{
		$license = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_LICENSE, null, null, null, '');

		if (!empty($license)) {
			Tools::refreshCACertFile();

			$data = array(
				'slm_action'  => 'slm_check',
				'secret_key'  => self::LICENSE_SECRET_KEY,
				'license_key' => $license,
			);

			$curl = new VexMercadoLibreCurl();
			$curl->setOpt(CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
			$curl->get(self::LICENSE_SERVER_URL, $data);

			if ($curl->isSuccess()) {
				$license_data = @json_decode($curl->response);

				if ($license_data !== null && json_last_error() === JSON_ERROR_NONE) {
					if ($license_data->result == 'success') {
						if ($license_data->max_allowed_domains > count($license_data->registered_domains)) {
							// validated license
							return true;
						} else {
							foreach ($license_data->registered_domains as $item) {
								if ($item->registered_domain == $_SERVER['SERVER_NAME']) {
									return true;
								}
							}

							$this->context->controller->errors[] = $this->module->l('Vex Mercado Libre: Reached maximum allowable domains.');
						}
					} else {
						$this->context->controller->errors[] = $license_data->message;
					}
				} else {
					$this->context->controller->errors[] = $this->module->l('Vex Mercado Libre: Communication error.');
				}
			} else {
				$this->context->controller->errors[] = $this->module->l('Vex Mercado Libre: Unexpected Error! The query returned with an error.');
			}
		}

		$this->disabled();
		return false;
	}

	private function disabled()
	{
		Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_ACTIVE, '0');
		Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_ACTIVE, '');
	}

	public function activate($license)
	{
		Tools::refreshCACertFile();

		$data = array(
			'slm_action'        => 'slm_activate',
			'secret_key'        => self::LICENSE_SECRET_KEY,
			'license_key'       => $license,
			'registered_domain' => $_SERVER['SERVER_NAME'],
			'item_reference'    => urlencode(self::ITEM_REFERENCE),
		);

		$curl = new VexMercadoLibreCurl();
		$curl->setOpt(CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
		$curl->get(self::LICENSE_SERVER_URL, $data);

		if ($curl->isSuccess()) {
			$license_data = json_decode($curl->response);

			if ($license_data !== null && json_last_error() === JSON_ERROR_NONE) {
				if ($license_data->result == 'success') {
					Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_ACTIVE, '1');
					Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_DATE_ACTIVE, time());

					$this->context->controller->confirmations[] = $license_data->message;
					return;
				} else {
					$this->context->controller->errors[] = $license_data->message;
				}
			} else {
				$this->context->controller->errors[] = $this->module->l('Vex Mercado Libre: Communication error.');
			}
		} else {
			$this->context->controller->errors[] = $this->module->l('Vex Mercado Libre: Unexpected Error! The query returned with an error.');
		}

		$this->disabled();
	}
}