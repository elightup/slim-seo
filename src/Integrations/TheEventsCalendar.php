<?php
namespace SlimSEO\Integrations;

class TheEventsCalendar {
	public function is_active(): bool {
		return defined( 'TRIBE_EVENTS_FILE' );
	}


	public function setup() {
		add_filter( 'slim_seo_skipped_shortcodes', [ $this, 'skip_shortcodes' ] );
	}

	/**
	 * Skip all shortcodes from The Events Calendar family.
	 * @link https://theeventscalendar.com/knowledgebase/shortcodes/
	 */
	public function skip_shortcodes( array $shortcodes ): array {
		return array_merge( $shortcodes, [
			'tribe_mini_calendar',
			'tribe_events_list',
			'tribe_featured_venue',
			'tribe_event_countdown',
			'tribe_this_week',
			'tribe_events',
			'tribe_event_inline',
			'tribe-user-event-confirmations',
			'tribe-tpp-success',
			'tec_tickets_checkout',
			'tec_tickets_success',
			'tribe_tickets',
			'tribe_tickets_rsvp',
			'tribe_tickets_attendees',
			'tribe_tickets_protected_content',
			'tribe_tickets_rsvp_protected_content',
			'tribe_attendee_registration',
			'tribe-user-event-confirmations',
			'tribe_community_events_title',
			'tribe_community_events',
			'tribe_community_tickets',
			'gigpress_shows',
		] );
	}

	/**
	 * Skip all blocks from The Events Calendar family.
	 * @link https://theeventscalendar.com/knowledgebase/an-introduction-to-event-blocks/
	 * @link https://theeventscalendar.com/knowledgebase/an-introduction-to-tickets-blocks/
	 * @link https://theeventscalendar.com/knowledgebase/how-to-add-event-blocks-to-wordpress-pages-and-posts/
	 */
	public function skip_blocks( array $blocks ): array {
		return array_merge( $blocks, [
			'tribe/attendees',
			'tribe/classic-event-details',
			'tribe/event-category',
			'tribe/event-datetime',
			'tribe/event-links',
			'tribe/event-organizer',
			'tribe/event-price',
			'tribe/event-tags',
			'tribe/event-venue',
			'tribe/event-website',
			'tribe/events-countdown',
			'tribe/events-featured-venue',
			'tribe/events-list',
			'tribe/featured-image',
			'tribe/mini-calendar',
			'tribe/related-events',
			'tribe/rsvp',
			'tribe/tickets',
			'tribe/tickets-item',
		] );
	}
}
