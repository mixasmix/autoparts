<?php
if (!empty($partdata[0]['artikul'])) {
    ?>
    <h2 class="partlist_count" >По вашему запросу найдено <?= count($partdata) ?> позиций</h2>
    <?php
} else {
    ?>
    <h2 class="partlist_count">По вашему запросу ничего не найдено</h2>
    <?php
}
if (!empty($bestparts)) {
    ?>

    <!--<div id="bestParts">
        <div class="bestParts bestOrigin">
            <h3>Оригинал</h3>
            <div class="bestParts-brand-partNum">ANCHOR: 2888</div>
            <div class="parts_rating rating0"></div>
            <div class="bestParts-description">
                <p>Front Mount</p>
            </div>
            <div class="bestParts-delivery-period">
                <p> Срок доставки: 13/19 дней</p>
            </div>
            <div class="bestParts-chance-of-shipment">
                <p> Вероятность отгрузки: 65%</p>
            </div>
            <div class="bestParts-price">
                <h4>1953.2 руб</h4>
            </div>
            <a href="/addbacket/add/0_906044c6cb4224c69ba36dc736606b4d" class="linkaddbacket">В корзину<span style="display:none">{"AnalogueCode":"2888","AnalogueCodeAsIs":"2888","AnalogueManufacturerName":"ANCHOR","AnalogueWeight":"0.000","CodeAsIs":"2888","DeliveryVariantPriceAKiloForClientDescription":"","DeliveryVariantPriceAKiloForClientPrice":"0.00","DeliveryVariantPriceNote":"","PriceListItemDescription":"","PriceListItemNote":"","IsAvailability":"1","IsCross":"0","LotBase":"1","LotType":"1","ManufacturerName":"ANCHOR","OfferName":"MSC-STC-1095","PeriodMin":13,"PeriodMax":19,"PriceListDiscountCode":"117370","ProductName":"Front Mount","Quantity":"3","SupplierID":"507","GroupTitle":"","UpdatedAt":"2015-09-25T17:25:00","statistic":65,"Reference":"063427290","uniqueid":0,"brandName":"ANCHOR","artikul":"2888","description":"Front Mount","delivery_period":"13/19","nal":"3","chance_of_shipment":65,"rpr":"1728.50","price":1953.2,"last_upd":1443356717,"origin":1,"provider":"906044c6cb4224c69ba36dc736606b4d","crc":"3474997ad6d794ff1f2bbe2feea908e4","rating":0,"bid":"405","image":false}</span>
                <span style="display:none">			{"Reference":"063427290", "AnalogueCodeAsIs":"2888", "AnalogueManufacturerName":"ANCHOR", "OfferName":"MSC-STC-1095", "LotBase":"1", "LotType":"1", "PriceListDiscountCode":"117370", "rpr":"1728.50", "Quantity":"3", "PeriodMin":"13"}</span>
            </a>
        </div>
        <div class="bestParts bestPrice">
            <h3>Самая низкая цена</h3>
            <div class="bestParts-brand-partNum">WOKING: P305312</div>
            <div class="parts_rating rating0"></div>
            <div class="bestParts-description">
                <p>КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД</p>
            </div>
            <div class="bestParts-delivery-period">
                <p> Срок доставки: 2/3 дней</p>
            </div>
            <div class="bestParts-chance-of-shipment">
                <p> Вероятность отгрузки: 93%</p>
            </div>
            <div class="bestParts-price">
                <h4>555.3 руб</h4>
            </div>
            <a href="/mybacket/add/0_906044c6cb4224c69ba36dc736606b4d" class="linkaddbacket">В корзину<span style="display:none">{"AnalogueCode":"P305312","AnalogueCodeAsIs":"P3053.12","AnalogueManufacturerName":"WOKING","AnalogueWeight":"0.000","CodeAsIs":"2888","DeliveryVariantPriceAKiloForClientDescription":"","DeliveryVariantPriceAKiloForClientPrice":"0.00","DeliveryVariantPriceNote":"","PriceListItemDescription":"","PriceListItemNote":"[P3053.12] WOKING","IsAvailability":"1","IsCross":"1","LotBase":"1","LotType":"1","ManufacturerName":"HANS PRIES (TOPRAN)","OfferName":"MSC-STC-1172","PeriodMin":2,"PeriodMax":3,"PriceListDiscountCode":"83139","ProductName":"КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД","Quantity":"1","SupplierID":"612","GroupTitle":"Замена","UpdatedAt":"2015-09-27T04:54:00","statistic":93,"Reference":"251048396","uniqueid":0,"brandName":"WOKING","artikul":"P305312","description":"КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД","delivery_period":"2/3","nal":"1","chance_of_shipment":93,"rpr":"491.39","price":555.3,"last_upd":1443356717,"origin":0,"provider":"906044c6cb4224c69ba36dc736606b4d","crc":"69666aeabc2d11e5fe8b17f7c813ffb5","rating":0,"bid":"4","image":false}</span>
                <span style="display:none">			{"Reference":"251048396", "AnalogueCodeAsIs":"P3053.12", "AnalogueManufacturerName":"WOKING", "OfferName":"MSC-STC-1172", "LotBase":"1", "LotType":"1", "PriceListDiscountCode":"83139", "rpr":"491.39", "Quantity":"1", "PeriodMin":"2"}</span>
            </a>
        </div>
        <div class="bestParts bestShipment">
            <h3>Самая быстрая доставка</h3>
            <div class="bestParts-brand-partNum">FENOX: P2511</div>
            <div class="parts_rating rating0"></div>
            <div class="bestParts-description">
                <p>Цилиндр сцепления рабочий</p>
            </div>
            <div class="bestParts-delivery-period">
                <p> Срок доставки: 1/2 дней</p>
            </div>
            <div class="bestParts-chance-of-shipment">
                <p> Вероятность отгрузки: 97%</p>
            </div>
            <div class="bestParts-price">
                <h4>1465.8 руб</h4>
            </div>
            <a href="/mybacket/add/0_906044c6cb4224c69ba36dc736606b4d" class="linkaddbacket">В корзину<span style="display:none">{"AnalogueCode":"P2511","AnalogueCodeAsIs":"P2511","AnalogueManufacturerName":"FENOX","AnalogueWeight":"0.000","CodeAsIs":".2888","DeliveryVariantPriceAKiloForClientDescription":"","DeliveryVariantPriceAKiloForClientPrice":"0.00","DeliveryVariantPriceNote":"","PriceListItemDescription":"","PriceListItemNote":"","IsAvailability":"1","IsCross":"1","LotBase":"1","LotType":"1","ManufacturerName":"MAPCO","OfferName":"MSC-STC-58","PeriodMin":1,"PeriodMax":2,"PriceListDiscountCode":"72901","ProductName":"Цилиндр сцепления рабочий","Quantity":"1","SupplierID":"30","GroupTitle":"Замена","UpdatedAt":"2015-09-27T14:36:00","statistic":97,"Reference":"726797122","uniqueid":0,"brandName":"FENOX","artikul":"P2511","description":"Цилиндр сцепления рабочий","delivery_period":"1/2","nal":"1","chance_of_shipment":97,"rpr":"1297.20","price":1465.8,"last_upd":1443356717,"origin":0,"provider":"906044c6cb4224c69ba36dc736606b4d","crc":"bdfbab3ce88f1d0a4f0947c6a996851a","rating":0,"bid":"3","image":false}</span>
                <span style="display:none">			{"Reference":"726797122", "AnalogueCodeAsIs":"P2511", "AnalogueManufacturerName":"FENOX", "OfferName":"MSC-STC-58", "LotBase":"1", "LotType":"1", "PriceListDiscountCode":"72901", "rpr":"1297.20", "Quantity":"1", "PeriodMin":"1"}</span>
            </a>
        </div>
        <div class="bestParts bestTheBest">
            <h3>Самое лучшее предложение</h3>
            <div class="bestParts-brand-partNum">WOKING: P305312</div>
            <div class="parts_rating rating0"></div>
            <div class="bestParts-description">
                <p>КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД</p>
            </div>
            <div class="bestParts-delivery-period">
                <p> Срок доставки: 2/3 дней</p>
            </div>
            <div class="bestParts-chance-of-shipment">
                <p> Вероятность отгрузки: 93%</p>
            </div>
            <div class="bestParts-price">
                <h4>555.3 руб</h4>
            </div>
            <a href="/addbacket/add/0_906044c6cb4224c69ba36dc736606b4d" class="linkaddbacket">В корзину<span style="display:none">{"AnalogueCode":"P305312","AnalogueCodeAsIs":"P3053.12","AnalogueManufacturerName":"WOKING","AnalogueWeight":"0.000","CodeAsIs":"2888","DeliveryVariantPriceAKiloForClientDescription":"","DeliveryVariantPriceAKiloForClientPrice":"0.00","DeliveryVariantPriceNote":"","PriceListItemDescription":"","PriceListItemNote":"[P3053.12] WOKING","IsAvailability":"1","IsCross":"1","LotBase":"1","LotType":"1","ManufacturerName":"HANS PRIES (TOPRAN)","OfferName":"MSC-STC-1172","PeriodMin":2,"PeriodMax":3,"PriceListDiscountCode":"83139","ProductName":"КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД","Quantity":"1","SupplierID":"612","GroupTitle":"Замена","UpdatedAt":"2015-09-27T04:54:00","statistic":93,"Reference":"251048396","uniqueid":0,"brandName":"WOKING","artikul":"P305312","description":"КОЛОДКИ FRD SCORPIO/SIERRA 2 0I-2 9I/2 5TD 16V/24V 91-98 ЗАД","delivery_period":"2/3","nal":"1","chance_of_shipment":93,"rpr":"491.39","price":555.3,"last_upd":1443356717,"origin":0,"provider":"906044c6cb4224c69ba36dc736606b4d","crc":"69666aeabc2d11e5fe8b17f7c813ffb5","rating":0,"bid":"4","image":false}</span>
                <span style="display:none">			{"Reference":"251048396", "AnalogueCodeAsIs":"P3053.12", "AnalogueManufacturerName":"WOKING", "OfferName":"MSC-STC-1172", "LotBase":"1", "LotType":"1", "PriceListDiscountCode":"83139", "rpr":"491.39", "Quantity":"1", "PeriodMin":"2"}</span>
            </a>
        </div>
    </div>-->
    <?php
}
if (!empty($partdata)) {
    ?>
    <table class="table">
        <tr>
            <th><?= $this->lang->line('template_tablesearch_artikul') ?></th>
            <th><?= $this->lang->line('template_tablesearch_brand') ?></th>
            <th><?= $this->lang->line('template_tablesearch_description') ?></th>
            <th><?= $this->lang->line('template_tablesearch_image') ?></th>
            <th><?= $this->lang->line('template_tablesearch_quantity_stock') ?></th>
            <th><?= $this->lang->line('template_tablesearch_days_delivery') ?></th>
            <th><?= $this->lang->line('template_tablesearch_shance_delivery') ?></th>
            <th><?= $this->lang->line('template_tablesearch_quantity') ?></th>
            <th><?= $this->lang->line('template_tablesearch_price') ?></th>
            <th><?= $this->lang->line('template_tablesearch_addbacket') ?></th>
        </tr>
        <?php
        foreach ($partdata as $part) {
            ?>   
            <tr  class="div_tr" itemscope itemtype="http://schema.org/Product">
            <form action="/mybacket/add" method="POST">
                <td  itemprop="mpn"><?= $part['artikul'] ?></td>
                <td>
                    <p itemprop="brand">
                        <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/brands/brand/<?= $part['bid'] ?>" class="a_brandInfo" itemprop="url"><span  itemprop="name"><?= $part['brand'] ?></span></a>
                    </p>
                    <div class="parts_rating rating <?= $part['rating'] ?> "></div> 
                </td>
                <td><?= $part['description'] ?></td>
                <td></td>
                <td><?= $part['quantity'] ?></td>
                <td><?= $part['minperiod'] ?>/<?= $part['maxperiod'] ?></td>
                <td><?= $part['chanceOfDelivery'] ?></td>
                <td><input type="text" name="quant" size="2" value="<?= 1 * $part['minparties'] ?>"></td>
                <td><?= $part['price'] . $this->lang->line('template_tablesearch_valute_rub') ?></td>
                <td><input type="submit" class='add_backet_btn btn' value='<?= $this->lang->line('template_tablesearch_addbacket') ?>'/></td>
                <input type="hidden" name="uid" value="<?= $part['uid'] ?>"/>
            </form>
        </tr>

        <!-- <div class="div_tr" itemscope itemtype="http://schema.org/Product">
             <div class="div_td col1" itemprop="mpn"><?= $part->artikul ?></div>
             <div class="div_td col2">
                 <p itemprop="brand">
                     <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/brands/brand/<?= $part->bid ?>" class="a_brandInfo" itemprop="url"><span  itemprop="name"><?= $part->brandName ?></span></a>
                 </p>
                 <div class="parts_rating rating<?= $part->rating ?>">
                 </div>
             </div>
             <div class="div_td col3" itemprop="description"><?= $part->description ?></div>
             <div class="div_td col4">
                 <a class="img_parts" href="#"></a>
             </div>
            
             <div class="div_td col10"><?= $part->Quantity ?></div>
             <div class="div_td col5"><?= $part->delivery_period ?></div>
             <div class="div_td col6">
                 <div class="shipment">
                     <p><?= $part->chance_of_shipment ?></p>
                     <div class="shipment_progress"></div>
                     <div class="shipment_pointer">
                         <div class="pointer" style="margin-left:<?= $part->chance_of_shipment ?>"></div>
                     </div>
                 </div>
             </div>
             <div class="div_td col7">
                 <input value="1" class="inp_count" type="number">
                 </div>
             
             <div class="div_td col8 pcost"  itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span  itemprop="price"><?= $part->price ?></span><span style="display:none"  itemprop="priceCurrency">RUB</span></div>
              
                 <div class="div_td col11">
                     <a href="#" class="a_notebook_add" title="Добавить в закладки"></a>
                 </div>
                 <div class="div_td col9">
                     <noindex><a href="/addbacket/add/0_<?= $part->provider ?>" class="linkaddbacket">В корзину<span style="display:none"><?= $part->crc ?></span>
                         <span style="display:none">906044c6cb4224c69ba36dc736606b4d</span>
                         <span style="display:none">0</span>
                         </a></noindex>
                 </div>
             </div>-->
        <?php
    }
}
?>
</div>
</div>
</table>        
