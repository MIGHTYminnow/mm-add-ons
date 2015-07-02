/**
 * General Scripts.
 *
 * @since 1.0.0
 *
 * @package mm-add-ons
 */

( function( $ ) {

	/**
	 * Countdown Component.
	 */
	$( document ).ready( function() {
		MmStartCountdowns();
	});

	// Trigger function for the countdowns.
	function MmStartCountdowns() {
		var $targets = $( '.countdown' );

		$targets.each( function() {

			var $this = $( this );
			var year = parseInt( $this.data( 'year' ) );
			var month = parseInt( $this.data( 'month' ) - 1 ); // Not sure why, but this one property is indexed differently
			var day = parseInt( $this.data( 'day' ) );
			var hour = parseInt( $this.data( 'hour' ) );
			var minute = parseInt( $this.data( 'minute' ) );
			var second = parseInt( $this.data( 'second' ) );
			var timezone_offset = parseInt( $this.data( 'timezone-offset' ) );

			// Form end date object (not including timezone).
			var dateObj = new Date( Date.UTC( year, month, day, hour, minute, second, 0 ) );

			// Adjust end date object to factor in timezone offset.
			dateObj.setUTCHours( dateObj.getUTCHours() - timezone_offset );

			$this
				.countdown( dateObj )
				.on( 'update.countdown finish.countdown', function( event ) {
			   		$this.html( event.strftime( ''
			     	+ '<div class="unit days"><span class="count">%-D</span><span class="label">day%!D</span></div>'
			     	+ '<div class="unit hours"><span class="count">%-H</span><span class="label">hour%!H</span></div>'
			     	+ '<div class="unit minutes"><span class="count">%-M</span><span class="label">minute%!M</span></div>'
			     	+ '<div class="unit seconds"><span class="count">%-S</span><span class="label">second%!S</span></div>'
		    	));
			});
		});
	}

})( jQuery );


