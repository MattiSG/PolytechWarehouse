/**An enhancement to the teachers' calendar in Polytech'Warehouse.
*Can not be considered a reusable module, too much coupling with the PHP module. Won't hurt global code quality, since coupling is already total between both sides.
*Author:	Matti Schneider-Ghibaudo
*/

var PWHCalendar = new Class({
	Implements: Options,
	
	table: null, // a table element
	tds: [], // Element[], the tds inside this.table
	
	options: {
		linksFile: '/index.php',
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
		this.tds.each(this.addLink, this);
		return this;
	},
	
	addLink: function addLink(td) {
		td.addEvent('click', function() {
			var destination = new URI(this.options.linksFile);
			destination.setData(this.locationURI.getData())
						.setData(this.options.linksParams, true) // second param means "merge" instead of "overwrite"
						.setData({
							d: td.getChildren('.date')[0].get('text')
						}, true);
			destination.go();
		}.bind(this));
		
		td.setStyle('cursor', 'pointer');
	}
});
