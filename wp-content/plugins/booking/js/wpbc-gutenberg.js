/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Getenberg integration
 * @category inserting into posts
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2018-04-22
 */




var el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    blockStyle = { backgroundColor: '#900', color: '#fff', padding: '20px' };

registerBlockType( 'gutenberg-wpbc/booking', {
    title: 'Booking Calendar',

    icon:  'calendar-alt',

    category: 'layout',

    edit: function() {
        return el( 'div', { style: blockStyle }, '"Booking Calendar"' );
    },

    save: function() {
        return el( 'div', { style: blockStyle }, '[booking]' );
    },
} );

