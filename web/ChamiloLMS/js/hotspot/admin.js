$(document).ready(function() {

	var hotspots = new ChamiloHostpotCollection();
	var current_hotspot;
	var paper = Raphael('paper', 1000, 800);


	$('#paper').click(function(e) {
		if (current_hotspot)
		{
			current_hotspot.geometry.handleClick(e);
		}
	});


	function add_hotspot() {

		var inc = $('#hotspots .hotspot').length;
		var hotspot = new ChamiloHotspot('hotspot_' + inc, new ChamiloPolygon(paper, hotspots_colors[inc]));

		var hotspot_html = $('#tpl_form_hotspot_row').html();
		hotspot_html = hotspot_html.replace(/\{hotspot_id\}/g, hotspot.id);
		hotspot_html = hotspot_html.replace(/\{hotspot_inc\}/g, inc);
		hotspot_html = hotspot_html.replace(/\{hotspot_color\}/g, hotspot.geometry.color);console.log(hotspot_html);
		
		var hotspot_dom_element = $(hotspot_html);
		hotspot_dom_element.find('.hotspot-selector').click(function() {
			select_hotspot(hotspot_dom_element);
		});
		$('#hotspots').append(hotspot_dom_element);
		hotspots.add(hotspot);
		select_hotspot(hotspot_dom_element);
	}

	function change_geometry()
	{
		if(!current_hotspot)
			return;
		current_hotspot.geometry.clear();
		switch ($(this).val())
		{
			case 'polygon':
				current_hotspot.geometry = new ChamiloPolygon(paper, current_hotspot.geometry.color);
				break;
			case 'ellipse':
				current_hotspot.geometry = new ChamiloEllipse(paper, current_hotspot.geometry.color);
				break;
			case 'rectangle':
				current_hotspot.geometry = new ChamiloRectangle(paper, current_hotspot.geometry.color);
				break;
		}
	}

	function select_hotspot(dom_element) {
		$('#hotspots .hotspot').removeClass('active');
		dom_element.addClass('active');
		current_hotspot = hotspots.find(dom_element.attr('id'));
	}

	function clear_current_hotspot() {
		if (!current_hotspot)
			return false;
		current_hotspot.geometry.clear();

	}


	$('.add_hotspot').click(add_hotspot);
	$('.clear_hotspot').click(clear_current_hotspot);
	$('.choose_geometry').click(change_geometry);
});