function ChamiloHotspotAdmin() {

	var self = this;

	this.hotspots = new ChamiloHostpotCollection();
	this.current_hotspot = false;
	this.paper = Raphael('paper', 1000, 800);

	this.add_hotspot = function(answer, comment, weighting, coordinates, type) {

		if(!type)
			type = 'poly';
		var inc = $('#hotspots .hotspot').length+1;
		var hotspot = new ChamiloHotspot('hotspot_' + inc, ChamiloGeometry.forge(this.paper, hotspots_colors[inc], coordinates, type));

		var hotspot_html = $('#tpl_form_hotspot_row').html();
		hotspot_html = hotspot_html.replace(/\{hotspot_id\}/g, hotspot.id);
		hotspot_html = hotspot_html.replace(/\{hotspot_inc\}/g, inc);
		hotspot_html = hotspot_html.replace(/\{hotspot_color\}/g, hotspot.geometry.color);
		hotspot_html = hotspot_html.replace(/\{hotspot_answer\}/g, answer);
		hotspot_html = hotspot_html.replace(/\{hotspot_comment\}/g, comment);
		hotspot_html = hotspot_html.replace(/\{hotspot_weighting\}/g, weighting);
		
		var hotspot_dom_element = $(hotspot_html);
		hotspot_dom_element.find('.hotspot-selector').click(function() {
			self.select_hotspot(hotspot_dom_element);
		});
		hotspot_dom_element.find('.hotspot-delete').click(function() {
			self.delete_hotspot(hotspot_dom_element);
		});
		$('#hotspots').append(hotspot_dom_element);
		this.hotspots.add(hotspot);
		this.select_hotspot(hotspot_dom_element);
	};

	this.select_hotspot = function(dom_element) {
		$('#hotspots .hotspot').removeClass('active');
		dom_element.addClass('active');
		this.current_hotspot = false;
		var new_hotspot = this.hotspots.find(dom_element.attr('id'));
		console.log(new_hotspot.geometry.type);
		$('.choose_geometry').val(new_hotspot.geometry.type);
		this.current_hotspot = new_hotspot;
		
	};
	
	this.delete_hotspot = function(dom_element) {
		var hotspot = this.hotspots.find(dom_element.attr('id'));
		hotspot.geometry.clear();
		dom_element.remove();
		if(hotspot == this.current_hotspot)
		{
			this.current_hotspot = null;
		}
		this.hotspots.remove(dom_element.attr('id'));
		this.recolor();
	};

	this.clear_current_hotspot = function() {
		if (!this.current_hotspot)
			return false;
		this.current_hotspot.geometry.clear();

	};

	this.change_geometry = function(new_geometry_type)
	{
		console.log(new_geometry_type);
		if(!this.current_hotspot)
			return;
		this.current_hotspot.geometry.clear();
		this.current_hotspot.geometry = ChamiloGeometry.forge(this.paper, this.current_hotspot.geometry.color, '', new_geometry_type);
		
	};
	
	this.recolor = function()
	{
		$('#hotspots .hotspot').each(function(){
			var hotspot = self.hotspots.find($(this).attr('id'));
			var color = hotspots_colors[$(this).index()];
			if(hotspot.geometry.color != color)
			{
				hotspot.geometry.setColor(color);
				$(this).find('.hotspot-selector').css('background-color', color);
			}
		});
	};
	
	this.fill_inputs = function(){
		$('#hotspots .hotspot').each(function(){
			var hotspot = self.hotspots.find($(this).attr('id'));
			$(this).find('.hotspot_coordinates').val(hotspot.geometry.export());
			$(this).find('.hotspot_types').val(hotspot.geometry.type);
		});
	};



	$('#paper').click(function(e) {
		if (self.current_hotspot)
		{
			self.current_hotspot.geometry.handleClick(e);
		}
	});
	
	$('form').submit(function(){
		self.fill_inputs();
	});

	$('.add_hotspot').click(function(){self.add_hotspot('','','10');});
	$('.clear_hotspot').click(function(){self.clear_current_hotspot();});
	$('.choose_geometry').change(function(){self.change_geometry($(this).val());});
	
}