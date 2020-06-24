<?php

/**
 * 2007-2015 PrestaShop
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
 *  @author    Snegurka <site@web-esse.ru>
 *  @copyright 2007-2018 Snegurka WS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class AdminLoginMercadoLibreController extends ModuleAdminController
{
    private $_name_controller = 'vex_soluciones_mercado_libre';
    private $count_products = 0;
    public function __construct()
    {
        parent::__construct();

        require_once dirname($this->module->path) . '/classes/class-request.php';
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->red_url = 'index.php?controller=AdminModules&configure=' . $this->_name_controller . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $id_lang = Configuration::get('PS_LANG_DEFAULT'); // buscamos el ID del idioma
        $shops = Shop::getShops($active = true, $id_shop_group = null, $get_as_list_id = false); // vemos las tiendas que tiene el prestashop

        if(!empty(Tools::getValue('code'))){
           Configuration::updateValue(Vex_soluciones_mercado_libre::CONFIG_CODE, Tools::getValue('code'));

        }

        if (((bool)Tools::isSubmit('submitVex_MLProducts')) == true) {
            $count = 1;
            
            foreach($_POST as $aaa){
                $name = 'product' . $count;
                if($producto = Tools::getValue($name, false)){
                   
                    if(!VexMLRequest::checkproduct($producto['reference'])){

                        $product = new Product(); //añadimos un nuevo producto
                        $product->name = array($id_lang =>  $producto['title']);
                        
                        $seo = VexMLRequest::seo_friendly_url($producto['title']); // función externa para convertir el nombre en formato URL 
                        $product->link_rewrite = array($id_lang =>  $seo);
                        if($product->add()){
                            $id_product = $product->id;
                            $product->reference = $producto['reference']; //añadimos referencia
                            $product->ean13 = ""; // añadimos ean13
                            $product->weight = 1; // añadimos peso
                            $product->price = (float)str_replace(',','.',$producto['price']) ; //añadimos precio cambiando la , por un .

                            $categorias = Category::getSimpleCategories($id_lang);
                            $encontrada = false; // ponemos que no existe la categoria antes de buscarla

                            $catparent = 2; // indicamos que la categoria padre es la 2 que siempre suele ser "home"
                            $cat = $producto['categoria'];
                            $categorias_ml = VexMLRequest::get_categories($cat);
                            // foreach ($categorias as $categoria){
                            //         if ($categorias_ml->name == $categoria['name']){
                            //             $catparent = $categoria["id_category"];
                            //             $catpro = $categoria["id_category"];
                            //             break;
                            //         }else{
                            //             $category = new Category();
                            //             $category->name = array($id_lang => $categorias_ml->name);
                            //             $seo = VexMLRequest::seo_friendly_url($categorias_ml->name);
                            //             $category->link_rewrite = array($id_lang => $seo);
                            //             $category->id_parent = $catparent;
                            //             $category->is_root_category = false;
                            //             $category->add();
                            //             $catpro = $category->id;
                            //             $catparent = $category->id;
                            //             $product->addToCategories(array($catpro));
                            //             $product->id_category = $catpro;
                            //             $product->id_category_default = $catpro;
                            //             $product->save();
                            //         }
                                   
                            // }

                            // $product->addToCategories(array($catpro));
                            // $product->id_category = $catpro;
                            // $product->id_category_default = $catpro;
                            $product->minimal_quantity = 1;

                            // if (strlen($producto['descripcion']) > 50 ){
                            //     $pos = strpos($producto['descripcion'], ".",10);
                            // }
                            // if (strlen($producto['descripcion']) > 150 ){
                            //     $pos = strpos($producto['descripcion'], ".",100);
                            // }
                            // if (strlen($producto['descripcion']) > 250 ){
                            //     $pos = strpos($producto['descripcion'], ".",200);
                            // }
                            // if (strlen($producto['descripcion']) > 350 ){
                            //     $pos = strpos($producto['descripcion'], ".",300);
                            // }

                            // $descrip = substr($producto['descripcion'], 0, $pos);
                            // $product->description_short = $descrip;
                            // $product->description = $producto['descripcion'];

                            // $product->advanced_stock_management = 0;
                            // $product->show_price = 1;
                            // $product->on_sale = 0;
                            // $product->unit_price = $product->price;
                            // $product->weight = (float)(1);
                            // $product->redirect_type = '404';
                            // $product->minimal_quantity = 1;
                            // $product->show_price = 1;
                            // $product->on_sale = 0;
                            // $product->minimal_quantity = 1;
                            // $product->available_for_order = 1;
                            // $product->online_only = 1;
                            // $product->unit_price = $product->price;
                            $product->active = 0; // dejamos el producto NO activado, mejor activar despues manualmente
                            $product->save();  // Guardamos los datos del producto y continuamos

                            // if(VexMLRequest::add_images($id_product, $producto, true)){
                            //     exit('todo bello');
                            // }
                            
                        }
                        

                    }
                }
                $count += 1;
            }
        }

       
        
        if(!VexMLRequest::validateDataML()){
            Tools::redirectAdmin($this->red_url);
        }

        if(!VexMLRequest::setMLDataUser()){
            Tools::redirectAdmin($this->red_url);
        }

    }

    public function initContent()
    {
        parent::initContent();

        $list_ids = [];
        $list_items = [];
        $url_products = '';
        $url = Vex_soluciones_mercado_libre::CONFIG_API_BASE . 'users/' . 
        Configuration::get(Vex_soluciones_mercado_libre::CONFIG_USER_ID) . 
        '/items/search?access_token=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN);

        if ($list_ids = VexMLRequest::getItemsUserMl($url)){
            $url_products = Vex_soluciones_mercado_libre::CONFIG_API_BASE . 'items?ids=';
            foreach($list_ids->results as $item){
                $url_products .= $item . ',';
            }
            $url_products .= '&access_token=' . Configuration::get(Vex_soluciones_mercado_libre::CONFIG_ACCEST_TOKEN);
            if ($list_ = VexMLRequest::getProductsUserMl($url_products)){
                $list_items = $list_;

            }
            // $list_ids = $list_items->results;

        }
        $id_lang = Configuration::get('PS_LANG_DEFAULT'); // buscamos el ID del idioma
        
        $seach = Search::find($id_lang, 'the lang');

        echo '<pre>';
        // print_r(Category::getSimpleCategories($id_lang));
        print_r(var_dump($seach));

        echo '</pre>';
        exit();

       
        $this->count_products = count($list_items);

        $this->context->smarty->assign(
            array(
                'list_items'     => $list_items,
            )
        );


        $this->setTemplate($this->module->template_dir.'/views/templates/admin/login.tpl');

    }
}