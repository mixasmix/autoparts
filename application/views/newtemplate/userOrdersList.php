<h2 class="align_center"><?=$this->lang->line('template_orderlist_order_working')?> (<?=(!empty($activeOrders)?count($activeOrders):0)?>)</h2>
 <?php
 if(!empty($activeOrders)){
        ?>
        <div class="panel-group" id="accordion">
            
        
        <?php 
        foreach($activeOrders as $activeOrder){
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseArchive<?=$activeOrder['order_id']?>">
                        <?=$this->lang->line('template_orderlist_order_text')?> №<?=$activeOrder['order_id']?> <?=$this->lang->line('template_orderlist_order_from')?> <?=$activeOrder['date']?>
                      </a>
                    </h4>
            </div>
            <div id="collapseArchive<?=$activeOrder['order_id']?>" class="panel-collapse collapse">
                <div class="panel-body">
                     <table class="table">
                        <tr>
                            <th>№</th>
                            <th><?=$this->lang->line('template_tablesearch_artikul')?></th>
                            <th><?=$this->lang->line('template_tablesearch_brand')?></th>
                            <th><?=$this->lang->line('template_tablesearch_description')?></th>
                            <th><?=$this->lang->line('template_tablesearch_quantity')?></th>
                            <th><?=$this->lang->line('template_tablesearch_price')?></th>
                            <th><?=$this->lang->line('template_tablesearch_days_delivery')?></th>
                            <th><?=$this->lang->line('template_navigation_status')?></th>
                        </tr>
                        <?php
                            $i=1; //счетчик
                            $sum=0;//сумма заказа
                            foreach($activeOrder['positions'] as $pos){
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=$pos['artikul']?></td>
                                    <td><?=$pos['brand']?></td>
                                    <td><?=$pos['description']?></td>
                                    <td><?=$pos['quantity']?></td>
                                    <td><?=$pos['price']?></td>
                                    <td><?=$pos['delivery']?></td>
                                    <td><?=$pos['status']?></td>
                                </tr>
                                <?php
                                $sum+=$pos['price']*$pos['quantity'];
                                $i++;
                            }
                        ?>
                    </table>
        <p><?=$this->lang->line('template_orderlist_order_price_sum').' '.$sum.$this->lang->line('template_orderlist_order_price_curr')?></p>
                </div>
          </div>
      </div>
       
        <?php 
        }
        ?>
        </div>
        <?php
    }
    if(!empty($archiveOrders)){
        ?>
        <h2 class="align_center"><?=$this->lang->line('template_orderlist_order_archive')?> (<?=(!empty($archiveOrders)?count($archiveOrders):0)?>)</h2>
        <div class="panel-group" id="accordion">
            
        
        <?php 
        foreach($archiveOrders as $archiveOrder){
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapseArchive<?=$archiveOrder['order_id']?>">
                        <?=$this->lang->line('template_orderlist_order_text')?> №<?=$archiveOrder['order_id']?> <?=$this->lang->line('template_orderlist_order_from')?> <?=$archiveOrder['date']?>
                      </a>
                    </h4>
            </div>
            <div id="collapseArchive<?=$archiveOrder['order_id']?>" class="panel-collapse collapse">
                <div class="panel-body">
                     <table class="table">
                        <tr>
                            <th>№</th>
                            <th><?=$this->lang->line('template_tablesearch_artikul')?></th>
                            <th><?=$this->lang->line('template_tablesearch_brand')?></th>
                            <th><?=$this->lang->line('template_tablesearch_description')?></th>
                            <th><?=$this->lang->line('template_tablesearch_quantity')?></th>
                            <th><?=$this->lang->line('template_tablesearch_price')?></th>
                            <th><?=$this->lang->line('template_tablesearch_days_delivery')?></th>
                            <th><?=$this->lang->line('template_navigation_status')?></th>
                        </tr>
                        <?php
                            $i=1; //счетчик
                            $sum=0;//сумма заказа
                            foreach($archiveOrder['positions'] as $pos){
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td><?=$pos['artikul']?></td>
                                    <td><?=$pos['brand']?></td>
                                    <td><?=$pos['description']?></td>
                                    <td><?=$pos['quantity']?></td>
                                    <td><?=$pos['price']?></td>
                                    <td><?=$pos['delivery']?></td>
                                    <td><?=$pos['status']?></td>
                                </tr>
                                <?php
                                $sum+=$pos['price']*$pos['quantity'];
                                $i++;
                            }
                        ?>
                    </table>
        <p><?=$this->lang->line('template_orderlist_order_price_sum').' '.$sum.$this->lang->line('template_orderlist_order_price_curr')?></p>
                </div>
          </div>
      </div>
       
        <?php 
        }
        ?>
        </div>
        <?php
    }
    