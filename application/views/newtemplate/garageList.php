<?php
    if(!empty($garageCars)){
        foreach ($garageCars as $k=>$v) {
        if ($k % 2 != 0)
            $a[] = $v;
        else
            $b[] = $v;
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5 bg_nav padding10 margin10">
            <?php
                
                   foreach($b as $k=>$gC){
                       ?>
                            <div class="private-user-point">
                                <a href="#" class="private-user-link display_block" data-toggle="modal" data-target="#modal<?=$gC['vin']?>">
                                    <div class="private-user-point-icon inline_block">
                                        <span class="fa fa-car"></span>
                                    </div>
                                    <?=$gC['vin']?>
                                </a>
                                <div class="private-user-description">
                                    <?php
                                        $descr=(!empty($gC['info']['Марка']))?$gC['info']['Марка']:'';
                                        $descr.=' ';
                                        $descr.=(!empty($gC['info']['Модель']))?$gC['info']['Модель']:'';
                                        $descr.=' ';
                                        $descr.=(!empty($gC['info']['Дата производства']))?$gC['info']['Дата производства']:'';
                                        echo $descr;
                                    ?>
                                    
                                        <div id="modal<?=$gC['vin']?>" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                         <table class="table">
                                                            <?php
                                                                foreach($gC['info'] as $k=>$v){
                                                                    ?>
                                                            <tr>
                                                                <td><?=$k?></td>
                                                                <td><?=$v?></td>
                                                            </tr>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal">Закрыть</button></div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <hr>
                       <?php
                   } 
            ?>
        </div>
        <div class="col-md-5 bg_nav padding10  margin10">
              <?php
                   foreach($a as $gC){
                       ?>
                            <div class="private-user-point">
                                <a href="#" class="private-user-link display_block" data-toggle="modal" data-target="#modal<?=$gC['vin']?>">
                                    <div class="private-user-point-icon inline_block">
                                        <span class="fa fa-car"></span>
                                    </div>
                                    <?=$gC['vin']?>
                                </a>
                                <div class="private-user-description">
                                        <?php
                                        $descr=(!empty($gC['info']['Марка']))?$gC['info']['Марка']:'';
                                        $descr.=' ';
                                        $descr.=(!empty($gC['info']['Модель']))?$gC['info']['Модель']:'';
                                        $descr.=' ';
                                        $descr.=(!empty($gC['info']['Дата производства']))?$gC['info']['Дата производства']:'';
                                        echo $descr;
                                    ?>
                                    <div id="modal<?=$gC['vin']?>" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header"><button class="close" type="button" data-dismiss="modal">×</button>
                                                    </div>
                                                    <div class="modal-body">
                                                         <table class="table">
                                                            <?php
                                                                foreach($gC['info'] as $k=>$v){
                                                                    ?>
                                                            <tr>
                                                                <td><?=$k?></td>
                                                                <td><?=$v?></td>
                                                            </tr>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal">Закрыть</button></div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <hr>
                       <?php
                       
                   } 
                
            ?>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>