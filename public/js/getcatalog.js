$(function(){
	var a=$("#content_text table a");
	a.bind("click", getPage);
	
});
function getPage(e){
	/*e.preventDefault();*/
	var link=$(e.currentTarget).attr("href");
	/*var a=e.currentTarget();*/
	console.log();
}