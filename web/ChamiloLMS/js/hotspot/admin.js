$(document).ready(function() {

	var hotspots_colors = ['red', 'blue', 'green', 'yellow', 'pink', 'purple'];
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

		var inc = $('#hotspots li').length;
		var hotspot = new ChamiloHotspot('hotspot_' + inc, new ChamiloPolygon(paper, hotspots_colors[inc]));

		var li = $('<li>');
		li.attr('id', hotspot.id);
		li.text('Hotspot ' + inc);
		li.css('color', hotspot.geometry.color);
		$('#hotspots').append(li);
		li.click(function() {
			select_hotspot($(this));
		});
		hotspots.add(hotspot);
		select_hotspot(li);

		var select = $('<select>');
		select.addClass('choose_geometry');
		select.append('<option value="polygon" selected="selected">Polygon</option>');
		select.append('<option value="ellipse">Ellipse</option>');
		select.change(change_geometry);
		li.append(select);

	}

	function change_geometry()
	{
		var select = $(this);
		var li = select.parents('li').first();
		var hotspot = hotspots.find(li.attr('id'));
		hotspot.geometry.clear();
		switch (select.val())
		{
			case 'polygon':
				hotspot.geometry = new ChamiloPolygon(paper, hotspot.geometry.color);
				break;
			case 'ellipse':
				hotspot.geometry = new ChamiloEllipse(paper, hotspot.geometry.color);
				break;
		}
	}

	function select_hotspot(li) {
		$('#hotspots li').removeClass('active');
		li.addClass('active');
		current_hotspot = hotspots.find(li.attr('id'));
	}

	function clear_current_hotspot() {
		if (!current_hotspot)
			return false;
		current_hotspot.geometry.clear();

	}


	$('.add_hotspot').click(add_hotspot);
	$('.clear_hotspot').click(clear_current_hotspot);
});