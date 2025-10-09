
// Adding the tooltip and customizing the position for UK to make- england/Scotland in MAPGEO plugin.

(function($) {
    $(document).ready(function() {

        function initTooltip() {
            var ukPath = $('svg g[id="id-398"] path.imapsPolygon');
            if (ukPath.length === 0) return false; // not ready yet

            // Create tooltip with text and arrow
            var tooltip = $('<div class="uk-tooltip"><span class="tooltip-text"></span><div class="arrow"></div></div>').appendTo('body');

            // Style tooltip
            tooltip.css({
                position: 'absolute',
                padding: '10px 15px 5px',
                background: '#ffffffE6',
                color: '#000',
                borderRadius: '4px',
                pointerEvents: 'none',
                display: 'none',
                zIndex: 9999,
                boxShadow: '0 2px 6px rgba(0, 0, 0, 0.1)',
                fontSize: '16px',
                textAlign: 'center'
            });

            // Style arrow
            tooltip.find('.arrow').css({
                width: 0,
                height: 0,
                borderLeft: '5px solid transparent',
                borderRight: '5px solid transparent',
                borderTop: '5px solid #ffffffE6',
                position: 'absolute',
                bottom: '-5px',
                left: '50%',
                transform: 'translateX(-50%)'
            });

            var tooltipText = tooltip.find('.tooltip-text');

            ukPath.on('mousemove', function(e) {
    var bbox = this.getBoundingClientRect();
    var relativeX = e.clientX - bbox.left;
    var relativeY = e.clientY - bbox.top;
    var width = bbox.width;
    var height = bbox.height;

    var region = ''; // default = no tooltip

    // Top portion = Scotland
    if (relativeY < height * 0.35) {
        region = 'Scotland';
    } 
    // Bottom portion = England (exclude bottom-left)
    else if (relativeY >= height * 0.35 && relativeX > width * 0.2) { 
        region = 'England';
    }

    tooltipText.text(region);

    if (region) {
        tooltip.css({ 
            top: e.pageY - tooltip.outerHeight() - 10,
            left: e.pageX - tooltip.outerWidth()/2
        }).fadeIn(100);
    } else {
        tooltip.fadeOut(100);
    }
});


            ukPath.on('mouseleave', function() {
                tooltip.fadeOut(100);
            });

            return true; // initialized
        }

        // Poll every 100ms until tooltip is initialized
        var interval = setInterval(function() {
            if (initTooltip()) clearInterval(interval);
        }, 100);

    });
})(jQuery);
