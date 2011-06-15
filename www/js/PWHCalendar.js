/**An enhancement to the teachers' calendar in Polytech'Warehouse.
*Can not be considered a reusable module, too much coupling with the PHP module. Won't hurt global code quality, since coupling is already total between both sides.
*Author:	Matti Schneider-Ghibaudo
*/

var PWHCalendar = new Class({
	Implements: Options,
	
	table: null, // a table element
	tds: [], // Element[], the tds inside this.table
	
	options: {
		linksFile: 'index.php',
		linksParams: { // everything in the current query string will be passed, but anything could be overwritten in options
			page: 'teacher_create_work_name_constraints'
		}
	},
	
	initialize: function init(table, options) {
		this.table = $(table);
		this.tds = this.table.getElements('.day');
		
		this.locationURI = new URI(window.location);
		
		this.setOptions(options);
	},
	
	color: function color() {
		this.tds.each(function(td, index) {
			td.setStyle('background-color', 'rgb(' + (200 + index * 10) + ', 200, 200)');
		});
		return this;
	},
	
	addLinks: function addLinks() {
		this.tds.each(this.storeLink, this);
		this.tds.each(this.attachLink, this);
		return this;
	},
	
	storeLink: function storeLink(td) {
		var day = td.getChildren('.date')[0].get('text');
		var destination; // URI
		var cursor = "pointer";
		
		if (td.hasClass('prev-next')) {
			if (day > 15) { //heuristic to determine whether it is a "prev-month" day or "next-month" day, since the calendar does not do any difference
				destination = new URI($('next-month').get('href'));
				cursor = 'w-resize';
			} else {
				destination = new URI($('prev-month').get('href'));
				cursor = 'e-resize';
			}
		} else {
			var destination = new URI(this.options.linksFile);
			destination.setData(this.locationURI.getData())
						.setData(this.options.linksParams, true) // second param means "merge" instead of "overwrite"
						.setData({
							d: day
						}, true);
		}
		
		td.store('destination', destination);
		td.store('cursor', cursor);
	},
	
	attachLink: function attachLink(td) {
		td.addEvent('click', function() {
			this.retrieve('destination').go();
		});
		
		td.setStyle('cursor', td.retrieve('cursor'));
	}
});
