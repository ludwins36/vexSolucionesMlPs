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
require_once dirname(__FILE__) . '/class-curl.php';

class VexMLRequest
{
    public static function postTokenMLDataUserOrRefresh($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => array(
            "Cookie: _d2id=ec9fa230-bc69-47b1-874e-c6ce01977fd7-n"
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response);

    }

    public static function getUrlMLDataUser()
    {
        return Vex_soluciones_mercado_libre::CONFIG_API_BASE . 
        'oauth/token?grant_type=authorization_code&client_id=' .
        Configuration::get(Vex_soluciones_mercado_libre::CONFIG_APP_ID) .
        '&client_secret=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_SECRET_ID) .
        '&code=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_CODE) .
        '&redirect_uri=' . Context::getContext()->link->getAdminLink('AdminLoginMercadoLibre', false, []);
    }

    public static function getUrlMLRefreshToken()
    {
        return Vex_soluciones_mercado_libre::CONFIG_API_BASE . 
        'oauth/token?grant_type=refresh_token&client_id=' .
        Configuration::get(Vex_soluciones_mercado_libre::CONFIG_APP_ID) .
        '&client_secret=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_SECRET_ID) .
        '&refresh_token=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_REFRES_TOKEN);
    }

    public static function validateTokenML()
    {

        $token = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN, null, null, null, '0');
        $refres_token = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_REFRES_TOKEN, null, null, null, '0');
        $date_expired = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_EXPIRED_DATE, null, null, null, '0');
        if(!empty($token) && !empty($refres_token)){
            if($date_expired < strtotime('now')){
                $url = self::getUrlMLRefreshToken();
                $rest = self::postTokenMLDataUserOrRefresh($url);
                if(property_exists($rest, 'error')){
                    //TODO agregar log del error
                    return false;

                }else{

                    Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_REFRES_TOKEN, $rest->refresh_token);
                    Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN, $rest->access_token);
                    Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_EXPIRED_DATE, $rest->expires_in + strtotime('now'));
                    Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_USER_ID, $rest->user_id);
                    // TODO actualizar data de token
                }
            }else{
                return true;
            }

        }else{
            $url = self::getUrlMLDataUser();
            $rest = self::postTokenMLDataUserOrRefresh($url);
            if(property_exists($rest, 'error')){
                //TODO agregar log del error
                return false;
            }else{
                Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_REFRES_TOKEN, $rest->refresh_token);
                Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN, $rest->access_token);
                Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_EXPIRED_DATE, $rest->expires_in + strtotime('now'));
                Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_USER_ID, $rest->user_id);
                // TODO guardar data de token
            }
        }
           


        return true;


    }

    public static function resetData()
    {
        Configuration::deleteByName(Vex_soluciones_mercado_libre::CONFIG_REFRES_TOKEN);
        Configuration::deleteByName(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN);
        Configuration::deleteByName(Vex_soluciones_mercado_libre::CONFIG_EXPIRED_DATE);
        Configuration::deleteByName(Vex_soluciones_mercado_libre::CONFIG_USER_ID);
    }

    public static function getItemsUserMl($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cookie: _d2id=ec9fa230-bc69-47b1-874e-c6ce01977fd7-n"
          ),
        ));

        $response = curl_exec($curl);
        $rest = json_decode($response);
        curl_close($curl);

       return $rest;

    }

    public static function getProductsUserMl($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cookie: _d2id=ec9fa230-bc69-47b1-874e-c6ce01977fd7-n"
          ),
        ));

        $response = curl_exec($curl);
        $rest = json_decode($response, true);
        curl_close($curl);

        if(in_array('error', $rest)){
            //TODO agregar log del error
            return false;
        }else{
            return $rest;
        }

    }

    public static function seo_friendly_url($string){
        //De nombre del producto a url, limpieza
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\1', $string );
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
        return strtolower(trim($string, '-'));
    }

    public static function checkproduct($refp){ // estamos usando codigo dentro de un "class" clase, debemos usar public function
        $reference =$refp;
        //codigo para verificar referencia 
        $sqlid_product = "select * from "._DB_PREFIX_."product where reference = '".$reference."'";
        $rowsidp = 0;
        $rowsidp = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlid_product);

        if(count($rowsidp) > 0 ){ // comprobamos si tiene lineas
            $pexiste = true;  // Si existe
        }else{
            $pexiste = false; // No existe
        }
        return $pexiste; // devolvemos que existe o no existe
    }

    public static function get_categories($cat)
    {
        $url = Vex_soluciones_mercado_libre::CONFIG_API_BASE . 'categories/' . ltrim($cat);
        $categorias = self::getItemsUserMl($url);
        $categorias = $categorias;
        return $categorias;

    }

    public static function add_images($id_product,$producto,$cover){
        $idpadd = $id_product;
        $imageid = false;
        $shops = Shop::getShops($active = true, $id_shop_group = null, $get_as_list_id = false);
        $imagenes = array ();
        $lincover = 0;
        if($lincover == 0){
           $cover = true;
        }else{
           $cover = false;
        }
    
        foreach (json_decode($producto['imagenes'], true) as $image){
            $imagenes[] = array (
                'url' => $image['url'],
                'cover' => $cover,
            );
            $lincover++;
        }
        
        foreach ($imagenes as $imagen){
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
            $url = $imagen['url'];
            $image = new Image();
            $image->id_product = $id_product;
            $image->position = Image::getHighestPosition($id_product) + 1;
            if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add()){
                $copy = self::copyImg($idpadd, $image->id, $url, 'products', true);
                if (!$copy){
                    $image->delete();
                }
                $imageid = $image->id;
            }
            
            if($imagen['cover'] == true){
                $image->cover = $image->id;
            }
            
            $image->save();
        }
        $imagenes = Image::getImages($id_lang, $id_product);
        $covimg = 0;
        foreach ($imagenes as $imagen){
            $shops = Shop::getShops($active = true, $id_shop_group = null, $get_as_list_id = false);
            foreach ($shops as $shop){
                if($cover == $imagen['id_image']){
                    $covimg = 1;
                }else{
                    $covimg = 0;
                }
                
                if ($covimg == 0){
                    $sqlimshop = "INSERT INTO "._DB_PREFIX_."image_shop (`id_product`, `id_image`, `id_shop`, `cover`) VALUES 
                    (".$idpadd.",".$imagen['id_image'].",".$shop['id_shop'].",0)";
                    if (Db::getInstance()->execute($sqlimshop)){}
                }
                
            }
        }
        return true;
    }


    public static function setMLDataUser()
    {
        $url = Vex_soluciones_mercado_libre::CONFIG_API_BASE . 'users/' . 
                Configuration::get(Vex_soluciones_mercado_libre::CONFIG_USER_ID) . 
                '?access_token=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                    "Cookie: _d2id=ec9fa230-bc69-47b1-874e-c6ce01977fd7-n"
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                $rest = json_decode($response);
                if(property_exists($rest, 'error')){
                    return false;
                }else{
                    Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_SITE_ID, $rest->site_id);
                }

                return true;
    }



    public static function validateDataML()
    {
        $isRun = true;
        $id_user = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_APP_ID, null, null, null, '0');
        $secrep_key = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_SECRET_ID, null, null, null, '0');
        $license   = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_LICENSE, null, null, null, '');
        $activated = Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACTIVE, null, null, null, '0');
        
        if (empty($license) && !$activated) {

            // $isRun = false;
        }else if(empty($id_user) || empty($secrep_key)){
            $isRun = false;
        }
        
        if(!self::validateTokenML()){
            $isRun = false;

        }
        return $isRun;
    }





}