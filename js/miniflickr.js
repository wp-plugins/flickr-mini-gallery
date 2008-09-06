// JavaScript Document
function initialize_flickr(){
	if($('.flickr-mini-gallery')){
		//filter example : 'tags=surf'
		//var filter = $('#gallery_flickr').attr('rel')
		$('.flickr-mini-gallery').each(function (i) {
				$(this).empty();					 
				var filter = $(this).attr('rel');
				build_gallery(filter, this);
		});
	}else{
	}
}
function build_gallery(filter, obj){
  var api = "http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=36c8b00c47e8934ff302dcad7775d0a2&tag_mode=all&"+filter; 
	$.getJSON(api+"&format=json&jsoncallback=?",
        function(data){
          $.each(data.photos.photo, function(i,item){
		  $("<img/>").attr({src: "http://static.flickr.com/"+item.server+"/"+item.id+"_"+item.secret+"_s.jpg", alt:item.title}).appendTo(obj).wrap("<a href=http://static.flickr.com/"+item.server+"/"+item.id+"_"+item.secret+".jpg alt="+item.id+"></a>");
		  //var img = $(obj+" img:eq["+i+"]");
         });
		  //description();
		 $(".flickr-mini-gallery a:has(img)").lightBox();
      });	 
}
function add_description(n){
	//var img_id = $(".felickr a[alt]:eq["+n+"]");
	var img_id = '2388852124';
	$.getJSON('http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key=36c8b00c47e8934ff302dcad7775d0a2&photo_id='+img_id+'&format=json&jsoncallback=?',
        		function(data){
					var textInfo = data.photo.description._content;
					$(".felickr:first").append(textInfo+"<br/>");					
					//$(".felickr img:eq["+n+"]").attr("alt",textInfo);
         	});
}
function description(){
	$(".felickr img").each(function(i){
			add_description(i)						
	})
}
$(document).ready(function(){
	initialize_flickr()	;
	
	//$('img').css({border:"1px solid #000000"});	
		   
});