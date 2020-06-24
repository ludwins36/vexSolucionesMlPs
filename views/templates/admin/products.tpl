{*
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
*}
<style>
    .label-glovo{
        text-align: right;
    }

    th{
        text-align: center;
        justify-content: center;
        background-color: #f2f2f2 !important;
        width: 10%; 
        border-bottom: 1px solid;
    }

    table, th, td{
        border: solid 1px #BDBDBD;
        padding: .3rem;
        border: none;

    }

    table{
        color: #424242;
        font-size: .8rem;
    }

    tr:nth-child(odd){
        background-color: #fbfbfb;

    }

    tr:nth-child(even) {

        background-color: #f2f2f2;
    }

    .turns{
        width: 100%;
        margin-top: 1rem; 
    }
</style>

<form id='store_form' class='defaultForm form-horizontal'  method='post' enctype='multipart/form-data' novalidate>

<div class="panel">
	
	<h3><i class="icon icon-credit-card"></i> {l s='Productos Mercado Libre' mod='vex_descuentos'}</h3>
	{* <p>
		<strong>{l s='Here is my new generic module!' mod='vex_descuentos'}</strong><br />
		{l s='Thanks to PrestaShop, now I have a great module.' mod='vex_descuentos'}<br />
		{l s='I can configure it using the following configuration form.' mod='vex_descuentos'}
	</p> *}
	<br />
	<table class='turns' id='turns'>
            <tr>
                <th scope='col'>{l s='Titulo' mod='vex_descuentos'}</th>
                <th scope='col'>{l s='Referencia' mod='vex_descuentos'}</th>
                <th scope='col'>{l s='Precio' mod='vex_descuentos'}</th>
                <th scope='col'>{l s='Estado' mod='vex_descuentos'}</th>
                <th scope='col'>{l s='Condición' mod='vex_descuentos'}</th>
                <th scope='col'>{l s='Ver Producto' mod='vex_descuentos'}</th>
            <tr>
            {if $list_items}
                {foreach $list_items as $item}
                    <tr>
                        <td>
                            <input type="hidden" name="product{$item@iteration}[title]" value="{l s=$item.body.title|escape:'html':'UTF-8'}">
                            {l s=$item.body.title|escape:'html':'UTF-8' mod='vex_descuentos'}
                        </td>
                        <td>
                            <input type="hidden" name="product{$item@iteration}[reference]" value="{l s=$item.body.id|escape:'html':'UTF-8'}">
                            {l s=$item.body.id|escape:'html':'UTF-8' mod='vex_descuentos'}
                        </td>
                         <td>
                            <input type="hidden" name="product{$item@iteration}[price]" value="{l s=$item.body.price|escape:'html':'UTF-8' mod='vex_descuentos'}">
                            <input type="hidden" name="product{$item@iteration}[categoria]" value=" {l s=$item.body.category_id|escape:'html':'UTF-8' mod='vex_descuentos'}">
                            <input type="hidden" name="product{$item@iteration}[imagenes]" value=" {l s=$item.body.pictures|@json_encode mod='vex_descuentos'}">
                            {l s=$item.body.price|escape:'html':'UTF-8' mod='vex_descuentos'}
                        </td>
                         <td>
                            <input type="hidden" name="product{$item@iteration}[status]" value="{l s=$item.body.status|escape:'html':'UTF-8' mod='vex_descuentos'}">
                            {l s=$item.body.status|escape:'html':'UTF-8' mod='vex_descuentos'}
                        </td>
                        <td>
                            <input type="hidden" name="product{$item@iteration}[condition]" value="{l s=$item.body.condition|escape:'html':'UTF-8' mod='vex_descuentos'}">
                            {l s=$item.body.condition|escape:'html':'UTF-8' mod='vex_descuentos'}
                        </td>
                        <td>
                            <a target="_blank" href="{l s=$item.body.permalink|escape:'html':'UTF-8'}">Ver</a>
                            
                        </td>

                        {* <td>
                            <input type="text" class="monto" name="MONTO_MIN_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" value='{$value.montoMin|escape:'htmlall':'UTF-8'}'/>
                        </td>
                        <td>
                            <input type="text" class="monto" name="MONTO_MAX_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" value='{$value.montoMax|escape:'htmlall':'UTF-8'}'/>
                        </td>

                        <td>
                            <select id='cambios' name="CAMBIO_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" class='form-control' style='height: 2rem; border-radius: .5rem;'>
                                <option value="Seleccionar">Seleccione</option>
                                <option {if $value.cambio == 0} selected {/if} value="0">{l s='Disminuir' mod='vex_descuentos'}</option>
                                <option {if $value.cambio == 1} selected {/if} value="1">{l s='Aumentar' mod='vex_descuentos'}</option>
                            </select>
                        </td>

                        <td>
                            <input type="radio" name="TIPO_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" {if $value.tipo == 1} checked {/if} value="1"/>Por porcentaje
                            <br/>
                            <input type="radio" name="TIPO_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" {if $value.tipo == 0} checked {/if} value="0"/>Por monto Fijo
                        </td>
                        
                        <td>
                            <input type="text" class="monto" name="MONTO_{l s=$value.pay|escape:'html':'UTF-8' mod='vex_descuentos'}" value='{$value.monto|escape:'htmlall':'UTF-8'}' placeholder="Monto a variar"/>
                        </td> *}
                            
                    </tr>
                {/foreach}
            {/if}
        
        </table>
        <div class='panel-footer'>
            <button type='submit' name='submitVex_MLProducts' class='btn btn-default pull-right'>

                <i class='process-icon-save' ></i>
                {l s='    GUARDAR    ' mod='vexglovo'}
            </button>
        </div>
</div>

</form>


