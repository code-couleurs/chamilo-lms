<?php
/* For licensing terms, see /license.txt */
/**
 * This script aims at replacing the old swf hotspot admin by a comination of javascript and svg
 * @package chamilo.exercise
 * @author Eric Marguin
 */

// TODO there seems to be a security lack here
$modifyAnswers = intval($_GET['hotspotadmin']);

if (!is_object($objQuestion)) {
    $objQuestion = Question :: read($modifyAnswers);
}
?>

<!-- TODO load javascript properly -->
<script type="text/javascript" src="<?php echo api_get_path(WEB_LIBRARY_JS_PATH) ?>raphael-min.js"></script>
<script>
	$(document).ready(function(){
		
		var hotspots_colors = ['red', 'blue', 'green', 'yellow', 'pink', 'purple'];
		var inc_hotspots = 0;
		var hotspots = {};
		var current_hotspot;
		var dragging = false;
		var paper = Raphael('paper', 1000, 800);
		
		$('#paper').click(function(e){
			if(!dragging)
			{
				var parentOffset = $(this).offset();
				var relX = e.pageX - parentOffset.left;
				var relY = e.pageY - parentOffset.top;

				
				add_point(relX, relY);
			}
		});
		
		function add_point(x, y)
		{
			if(!current_hotspot)
			{
				return false;
			}
			var point = paper.circle(x,y,5).attr({
				fill: current_hotspot.color,
				cursor: "move",
				"stroke-width": 20,
				stroke: "transparent"
			});
			paper.set(point).drag(move, start, up);
			current_hotspot.geometry.points.push(point);
			if(current_hotspot.type == 'polygon')
			{
				draw_polygon(current_hotspot.geometry);		
			}
			else if(current_hotspot.type == 'ellipse')
			{
				draw_ellipse(current_hotspot);
			}
		}
		
		function draw_ellipse(hotspot)
		{
			if(hotspot.geometry.points.length < 2)
				return false;
			if(hotspot.geometry.ellipse)
			{
				hotspot.geometry.ellipse.remove();
			}
			console.log(hotspot.geometry.points[0]);
			var x1 = hotspot.geometry.points[0].attr('cx');
			var y1 = hotspot.geometry.points[0].attr('cy');
			var x2 = Math.abs(hotspot.geometry.points[1].attr('cx')-x1);
			var y2 = Math.abs(hotspot.geometry.points[1].attr('cy')-y1);
			hotspot.geometry.ellipse = paper.ellipse(x1,y1,x2,y2).attr({
				fill: hotspot.color,
				opacity: 0.6
			});
			
		}
		
		function draw_polygon(polygon)
		{
			if(polygon.path)
			{
				polygon.path.remove();
			}
			if(polygon.points.length > 1)
			{
				var polygon_str = 'M ';
				for(var i in polygon.points)
				{
					polygon_str += ' '+polygon.points[i].attr('cx')+' '+polygon.points[i].attr('cy');
					if(i != polygon.points.length-1)
					{
						polygon.points[i].attr('r', '3');
					}
				}
				polygon_str += 'Z'; 
				polygon.path = paper.path(polygon_str).attr('fill', current_hotspot.color).attr('opacity', 0.6);
			}
		}
		
		function move (dx, dy) {
			dragging = true;
			this.attr({cx: this.ox + dx, cy: this.oy + dy});
			for(var i in hotspots)
			{
				for(var j in hotspots[i].geometry.points)
				{
					if(hotspots[i].geometry.points[j] == this)
					{
						if(hotspots[i].type == 'polygon')
							draw_polygon(hotspots[i].geometry);
						else if(hotspots[i].type == 'ellipse')
							draw_ellipse(hotspots[i]);
						return;
					}
				}
			}
		}
		function up () {
			setTimeout(function(){
				dragging = false;	
			},500);
			
		}
		function start  () {
			this.ox = this.attr("cx");
			this.oy = this.attr("cy");
		}
		
		function add_hotspot(){
			var li = $('<li>');
			li.attr('id', 'hotspot_'+inc_hotspots);
			li.text('Hotspot '+inc_hotspots);
			$('#hotspots').append(li);
			li.click(function(){
				select_hotspot($(this));
			});
			
			li.css('color', hotspots_colors[li.index()]);
			hotspots['hotspot_'+inc_hotspots] = {
				type: 'polygon',
				geometry: {
					path: false,
					points: []
				},
				color: li.css('color')
			};
			select_hotspot(li);
			inc_hotspots++;
			
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
			hotspots[li.attr('id')].type = select.val();
			clear_hotspot(hotspots[li.attr('id')]);
		}
		
		function select_hotspot(li){
			$('#hotspots li').removeClass('active');
			li.addClass('active');
			current_hotspot = hotspots[li.attr('id')];
		}
		
		function clear_current_hotspot(){
			if(!current_hotspot)
				return false;
			clear_hotspot(current_hotspot);
			
		}
		
		function clear_hotspot(hotspot)
		{
			for(var i in hotspot.geometry.points)
			{
				hotspot.geometry.points[i].remove();
			}
			
			hotspot.geometry.points = [];
			if(hotspot.geometry.path)
				hotspot.geometry.path.remove();
			if(hotspot.geometry.ellipse)
				hotspot.geometry.ellipse.remove();
		}
		
		
		$('.add_hotspot').click(add_hotspot);
		$('.clear_hotspot').click(clear_current_hotspot);
	});
</script>

<style>
	#hotspots li, a {
		cursor: pointer;
	}
	#hotspots li.active {
		font-weight: bold;
	}
	#draw_menu li {
		float: left;
	}
</style>

<a class="add_hotspot">Add hotspot</a>
<ul id="hotspots">
</ul>

Tools :
<ul id="draw_menu">
	<li class="clear_hotspot"><a>Clear hotspot</a></li>
</ul>
<div class="clear"></div>
<div id="paper" style="background: url(<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$objQuestion->picture; ?>); width:1000px; height: 1000px; background-repeat: no-repeat"></div>