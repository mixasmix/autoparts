<div id="search" class="search-block">
<form action="/page/find" method="POST">
<h1 class="h1_style align_center"><?=$this->lang->line('template_searchblock_header_label')?></h1>
<div class="search__input-area">
    <input id="partNumber" class="search-block__input input-text align_center_block form-control" value="<?php echo !empty($partNum)?$partNum:'';?>" name="part_number" placeholder="<?=$this->lang->line('template_searchblock_textinput_placeholder')?>" type="text">
    <div class="search__input-area_example">
        <h6 class="h6_style align_center"><?=$this->lang->line('template_searchblock_example_label')?></h6>
    </div>
    <input id="searchPart" class="search-block__submit input-button btn" value="<?=$this->lang->line('template_searchblock_button_search_label')?>" type="submit">
</div>
</form>
</div>
