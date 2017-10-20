var dw;
function createW(href, mode, title, width, height){
    if(!$chk(mode)){
        mode = 'url';
    }
    if(!$chk(title)){
        title = 'РљРѕРґ SA';
    }
    if(!$chk(width)){
        width = 600;
    }
    if(!$chk(height)){
        height = 520;
    }
    
    dw = new dWindow(
    {
        'mode': mode,
        'top' : '50%',
        'minWidth' 	: width,
		'minHeight' : height,
		'width' 	: width,
		'height' 	: height,
        'title'     : '<div class="parts_title">' + title + '</div>',
        'id'        : 'dwId'
    }
    ).load(href).show();
}

function createWHtml(html, mode, title, width, height){
    html = html.replace(new RegExp(";", 'g'), "<br />");
    if(!$chk(mode)){
        mode = 'html';
    }
    if(!$chk(title)){
        title = 'Minor Features';
    }
    if(!$chk(width)){
        width = 600;
    }
    if(!$chk(height)){
        height = 520;
    }
    
    dw = new dWindow(
    {
        'mode': mode,
        'top' : '50%',
        'minWidth' 	: width,
		'minHeight' : height,
		'width' 	: width,
		'height' 	: height,
        'title'     : '<div class="parts_title">' + title + '</div>',
        'id'        : 'dwId'
    }).load(html).show();
    
    
}