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

				add_polygon_point(relX, relY);
			}
		});
		
		function add_polygon_point(x, y)
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
			draw_polygon(current_hotspot.geometry);			
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
			for(var i in current_hotspot)
			{
				for(var j in current_hotspot[i].geometry.points)
				{
					if(current_hotspot[i].geometry.points[j] == this)
					{
						draw_polygon(current_hotspot[i].geometry);
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
		}
		
		function select_hotspot(li){
			$('#hotspots li').removeClass('active');
			li.addClass('active');
			current_hotspot = hotspots[li.attr('id')];
		}
		
		function clear_current_hotspot(){
			if(!current_hotspot)
				return false;
			
			for(var i in current_hotspot.geometry.points)
			{
				current_hotspot.geometry.points[i].remove();
			}
			
			current_hotspot.geometry.points = [];
			draw_polygon(current_hotspot.geometry);
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