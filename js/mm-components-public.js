/**
 * General Scripts.
 *
 * @since 1.0.0
 *
 * @package mm-components
 */

( function( $ ) {

	var $window = $( window );
	var $body   = $( 'body' );

	/**
	 * Reusable utility functions.
	 */

	// Debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	function mmDebounce( func, threshold ) {
		var timeout;

		return function mmDebounced() {
			var obj = this;
			var args = arguments;

			function mmDelayed() {
				func.apply( obj, args );
				timeout = null;
			}

			if ( timeout ) {
				clearTimeout( timeout );
			}

			timeout = setTimeout( mmDelayed, threshold || 50 );
		};
	};

	// Return the number of digits after the decimal point that a float has.
	function mmDecimalPlaces( num ) {
		var match = ( '' + num ).match( /(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/ );
		if ( ! match ) { return 0; }
		return Math.max(
			0,
			// Number of digits right of decimal point.
			( match[1] ? match[1].length : 0 )
			// Adjust for scientific notation.
			- ( match[2] ? +match[2] : 0 )
		);
	}

	// Utility function for getting the height of the document across all browsers.
	function mmGetDocHeight() {
		var D = document;

		return Math.max(
			D.body.scrollHeight, D.documentElement.scrollHeight,
			D.body.offsetHeight, D.documentElement.offsetHeight,
			D.body.clientHeight, D.documentElement.clientHeight
		);
	}

	// Utility function for getting the height of the viewport across all browsers.
	function mmGetViewportHeight() {
		var height = window.innerHeight; // Safari, Opera
		var mode = document.compatMode;

		if ( ( mode || ! $.support.boxModel ) ) { // IE, Gecko
			height = ( mode == 'CSS1Compat' ) ?
			document.documentElement.clientHeight : // Standards
			document.body.clientHeight; // Quirks
		}

		return height;
	}

	// Scroll callback function for detecting when elements enter and exit the viewport.
	// Author: Remy Sharp - http://remysharp.com/2009/01/26/element-in-view-event-plugin/
	function mmScrollCallback() {
		var vpH = mmGetViewportHeight(),
			scrolltop = ( document.documentElement.scrollTop ?
				document.documentElement.scrollTop :
				document.body.scrollTop ),
			elems = [];

		// naughty, but this is how it knows which elements to check for
		$.each( $.cache, function () {
			if ( this.events && this.events.inview ) {
				elems.push( this.handle.elem );
			}
		});

		if ( elems.length ) {
			$( elems ).each( function () {
				var $el = $( this ),
					top = $el.offset().top,
					height = $el.height(),
					inview = $el.data( 'inview' ) || false;

				if ( scrolltop > ( top + height ) || scrolltop + vpH < top ) {
					if ( inview ) {
						$el.data( 'inview', false );
						$el.trigger( 'inview', [ false ] );
					}
				} else if ( scrolltop < ( top + height ) ) {
					if ( ! inview ) {
						$el.data( 'inview', true );
						$el.trigger( 'inview', [ true ] );
					}
				}
			});
		}
	}

	// Function to initialize vertical centering.
	$.fn.mmInitVerticalCenter = function( offset ) {
		var selector, eventData, ourEvents, eventSet, thisEvent, eventName;

		selector = this.selector;
		offset   = offset || 0;

		// Check if our global already exists.
		if ( window.mmVerticalCenterItems ) {
			// It does, so copy the current object in it.
			window.mmVerticalCenterItems[selector] = offset;
		} else {
			// It doesn't, so create the global and store the current object in it.
			window.mmVerticalCenterItems = {};
			window.mmVerticalCenterItems[selector] = offset;
		}

		// Grab the current event data from the window object if it exists.
		eventData = $._data( window, 'events' ) || {};

		// Store the events that will retrigger doVerticalCenter().
		ourEvents = [ 'resize', 'orientationchange', 'mmverticalcenter' ];

		// Loop through each event and attach our handler if it isn't attached already.
		$( ourEvents ).each( function() {
			eventSet  = false;
			thisEvent = this;
			eventName = this + '.mmverticalcenter';

			// Check whether this event is already on the window.
			if ( eventData[ thisEvent ] ) {

				// Be careful not to disturb any unrelated listeners.
				$( eventData[ thisEvent ] ).each( function() {

					// Confirm that the event has our namespace.
					if ( this.namespace == 'mmverticalcenter' ) {

						// It does, so set our flag to true.
						eventSet = true;
					}
				});
			}

			// If our flag is still false we can safely attach the event.
			if ( ! eventSet ) {

				// Debounce it to be safe.
				$( window ).on( eventName, mmDebounce( mmTriggerVerticalCenter ) );
			}
		});

		// Trigger the first vertical centering.
		mmTriggerVerticalCenter();

		// Make this function chainable.
		return this;
	};

	// Function to trigger the vertical centering.
	function mmTriggerVerticalCenter() {

		// Loop through each object in our global and call doVerticalCenter.
		$.each( window.mmVerticalCenterItems, function( selector, offset ) {
			$( selector ).mmDoVerticalCenter( offset );
		});
	}

	// Function to do the vertical centering.
	$.fn.mmDoVerticalCenter = function( offset ) {
		var offset = offset || 0;

		// Selector might match multiple items, so do all
		// centering calculations on one item at a time.
		$( this ).each( function() {
			var parentHeight = $( this ).parent().height();

			// Make sure the element is block level.
			if ( $( this ).css( 'display' ) === 'inline' ) {
				$( this ).css( 'display', 'inline-block' );
			}

			// Calculate and add the margin-top to center the element.
			$( this ).css(
				'margin-top',
				( ( ( parentHeight - $( this ).outerHeight() ) / 2 ) + parseInt( offset ) )
			).addClass( 'mm-vc-complete' );
		});
	}

	/**
	 * Count Up.
	 */
	function mmSetupCountUps() {

		var $countUps = $( '.mm-count-up' );

		if ( $countUps.length ) {

			// Trigger the counting when the element enters the viewport.
			$countUps.on( 'inview', function( event, visible ) {
				if ( visible == true ) {
					$( this ).trigger( 'mmTriggerCountUp' );
				}
			});

			// Set up scrolling detection for when Count Ups come into the viewport.
			$window.on( 'scroll', mmScrollCallback );
		}

		$countUps.each( function() {

			var $this = $( this );

			$this.on( 'mmTriggerCountUp', function() {

				// Only count up once for each instance.
				if ( $this.hasClass( 'done-counting' ) ) {
					return;
				}

				var $numberWrap = $this.find( '.mm-count-up-number-wrap' );
				var number      = $this.attr( 'data-number' );
				var duration    = $this.attr( 'data-duration' ) * 1000;
				var floatDigits = mmDecimalPlaces( number );

				$( { Counter: 0 } ).animate( { Counter: number }, {
					duration: duration,
					easing: 'swing',
					step: function () {
						if ( 0 === floatDigits ) {
							$numberWrap.text( Math.ceil( this.Counter ) );
						} else if ( 1 === floatDigits ) {
							$numberWrap.text( ( Math.round( ( this.Counter ) * 10 ) / 10 ).toFixed( 1 ) );
						} else if ( 2 === floatDigits ) {
							$numberWrap.text( ( Math.round( ( this.Counter ) * 100 ) / 100 ).toFixed( 2 ) );
						}
					},
					complete: function() {
						$this.addClass( 'done-counting' );
					}
				});
			});
		});
	}

	/**
	 * Countdown.
	 */
	function mmStartCountdowns() {
		$( '.mm-countdown' ).each( function() {

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

	/**
	 * Expandable Content.
	 */
	function mmSetupExpandableContent() {
		$( '.mm-expandable-content' ).each( function() {

			var $trigger = $( this ).find( '.mm-expandable-content-trigger' );
			var $target = $( this ).find( '.mm-expandable-content-target' );

			$trigger.on( 'click', function( e ) {

				e.preventDefault();

				$trigger.toggleClass( 'open' );

				if ( $trigger.hasClass( 'fade' ) ) {
					$target.toggleClass( 'open' ).fadeToggle();
				} else {
					$target.toggleClass( 'open' ).toggle();
				}
			});
		});
	};

	/**
	 * Hero Banner.
	 */
	function mmSetupHeroBanners() {
		$( '.mm-hero-banner .hero-content-wrap' ).mmInitVerticalCenter();
	}

	/**
	 * Masonry layouts for Mm Posts.
	 */
	function mmPostsInitMasonry() {

	 	if ( typeof $().isotope !== 'function' ) {
	 		return;
	 	}

	 	$( '.mm-posts.mm-masonry' ).each( function() {

	 		var options = {
				itemSelector: '.mm-post',
				percentPosition: true,
				masonry: {
					columnWidth: '.mm-post'
				}
			};

			if ( ! $( this ).hasClass( 'no-gutter' ) ) {
				options.masonry.gutter = '.mm-posts-masonry-gutter';
			}

			var $this = $( this ).imagesLoaded( function() {
				$this.isotope( options );
			});
	 	});
	}

	function mmSliderSlideClass() {
		i = 0;
		$( '.mm-slider.content-as-slides .content-wrapper > div' ).each( function() {
			$( this ).addClass( 'mm-carousel-item' );
		})

		$( '.mm-carousel-item' ).each( function() {
			i++;
			$( this ).addClass( 'slide-' + i );
		})
	}

	function mmSliderHeight() {
		var containerHeight = 0;
		var windowWidth = $(window).width();
		if( windowWidth < 768 ) {
			// Loop through elements children to find & set the biggest height
			$( '.mm-slider.static-content .content-wrapper' ).each( function(){
			 // If this elements height is bigger than the biggestHeight
				 if ($(this).height() > containerHeight ) {
				   // Set the biggestHeight to this Height
				   containerHeight = $( this ).height() + 20;
				 }
			});
			// Set the container height
			$( '.mm-slider.static-content, .mm-slider-image img' ).height( containerHeight );
		}
	}

	/**
	 * Start the party.
	 */
	$( document ).ready( function() {
		mmSetupCountUps();
		mmStartCountdowns();
		mmSetupExpandableContent();
		mmPostsInitMasonry();
		mmSliderSlideClass();
		mmSliderHeight();

		// Trigger the scroll event once to ensure our inview listeners fire
		// if their elements are initially in view.
		$window.trigger( 'scroll' );
	});

	/**
	 * Start the after party.
	 */
	$( window ).load( function() {
		mmSetupHeroBanners();
	});

})( jQuery );