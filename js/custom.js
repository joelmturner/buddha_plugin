jQuery.noConflict();
(function($){
	$('.bb-wrap .getBuddha').click(function(){
		$(this).text('More Wisdom');
		
		// Cycle through the list items upon click
		var $toHighlight = $('.active').next().length > 0 ? $('.active').next() : $('.bb-quotes li').first();
        $('.active').hide().removeClass('active');
        $toHighlight.fadeIn('slow').addClass('active');
	});
})(jQuery);