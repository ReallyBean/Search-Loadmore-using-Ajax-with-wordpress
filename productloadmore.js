jQuery(function($){
	$('#loadmore-wrapper').on('click', '#loadmore', function(e){
 		e.preventDefault();
		var button = $(this),
		    data = {
			'action': 'loadmore',
			'query': product_loadmore_params.posts,
			'page' : product_loadmore_params.current_page,
			's' : product_loadmore_params.search_keyword
		};
 		ajax_search_loadmore(data);
	});

	$("#search_input").keypress(function(e) {
		if( e.which != 13 ) return;
		$(".products").html("");
		data = {
			'action': 'loadmore',
			'query': product_loadmore_params.posts,
			'page' : 0,
			's' : $(this).val()
		};
		ajax_search_loadmore(data);
	});
	function ajax_search_loadmore(data) {
		button = $("#loadmore");
		$.ajax({
			url : product_loadmore_params.ajaxurl, // AJAX handler
			data : data,
			type : 'POST',
			beforeSend : function ( xhr ) {
				button.text('ローディング中...'); // change the button text, you can also add a preloader image
			},
			success : function( response_str ){
				if( response_str ) {
					response = JSON.parse(response_str);
					button.text( 'もっとみる' ).prev().before(response.data); // insert new posts
					$(".products").append(response.data);
					if ( product_loadmore_params.search_keyword == data.s ) {
						product_loadmore_params.current_page++;
					} else {
						product_loadmore_params.search_keyword = data.s;
						product_loadmore_params.current_page = 1;
					}
					$("#found_posts").text(response.found_posts);
					// if ( product_loadmore_params.current_page == product_loadmore_params.max_page ) 
					if ( response.is_last_page == true ) {
						button.remove();
					} else {
						if( !$("#loadmore-wrapper #loadmore").length ) $("#loadmore-wrapper").append('<button id="loadmore">もっとみる</button>');
					}
				}
			}
		});
	}
});