<?php
    if(!empty($backetPositions)){
?>
<table class="table">
    <tr>
        <th></th>
        <th><?=$this->lang->line('template_tablesearch_artikul')?></th>
        <th><?=$this->lang->line('template_tablesearch_brand')?></th>
        <th><?=$this->lang->line('template_tablesearch_description')?></th>
        <th><?=$this->lang->line('template_tablesearch_quantity')?></th>
        <th><?=$this->lang->line('template_tablesearch_price')?></th>
        <th><?=$this->lang->line('template_tablesearch_days_delivery')?></th>
    </tr>
    <?php 
        foreach($backetPositions as $position){
    ?>
        
            <tr>
                <td><input type="checkbox" name="position<?=$position['id']?>" value="<?=$position['id']?>" checked id="ident<?=$position['id']?>"></td>
                <td><?=$position['artikul']?></td>
                <td><?=$position['brand']?></td>
                <td><?=$position['description']?></td>
                <td><?=$position['quantity']?></td>
                <td><?=$position['price']?></td>
                <td><?=$position['delivery']?></td>
            </tr>
        
    <?php 
        }
    ?>
</table>
<hr/>
<div class="row">
    <div class="col-md-6">
        <button class="invert_btn btn align_center_block"><?=$this->lang->line('template_confirm_order_check_all')?></button>
    </div>
    <div class="col-md-6">
        <button class="invert_btn btn align_center_block"><?=$this->lang->line('template_confirm_order_uncheck_all')?></button>
    </div>
</div>
<?php
    }
?>