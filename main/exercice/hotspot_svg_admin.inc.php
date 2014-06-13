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
		
		var polygons_colors = ['red', 'blue', 'green', 'yellow', 'pink', 'purple'];
		var inc_hotspots = 0;
		var polygons = {};
		var current_polygon;
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
			if(!current_polygon)
			{
				return false;
			}
			var point = paper.circle(x,y,5).attr({
				fill: current_polygon.color,
				cursor: "move",
				"stroke-width": 20,
				stroke: "transparent"
			});
			paper.set(point).drag(move, start, up);
			current_polygon.points.push(point);
			draw_polygon(current_polygon, true);			
		}
		
		function draw_polygon(polygon, finish)
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
				if(finish)
				{
					polygon_str += 'Z'; 
				}
				polygon.path = paper.path(polygon_str).attr('fill', current_polygon.color).attr('opacity', 0.6);
			}
		}
		
		function move (dx, dy) {
			dragging = true;
			this.attr({cx: this.ox + dx, cy: this.oy + dy});
			for(var i in polygons)
			{
				for(var j in polygons[i].points)
				{
					if(polygons[i].points[j] == this)
					{
						draw_polygon(polygons[i]);
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
			
			li.css('color', polygons_colors[li.index()]);
			polygons['hotspot_'+inc_hotspots] = {
				path: false,
				points: [],
				color: li.css('color')
			};
			select_hotspot(li);
			inc_hotspots++;
		}
		
		function select_hotspot(li){
			$('#hotspots li').removeClass('active');
			li.addClass('active');
			current_polygon = polygons[li.attr('id')];
		}
		
		function clear_current_hotspot(){
			if(!current_polygon)
				return false;
			
			for(var i in current_polygon.points)
			{
				current_polygon.points[i].remove();
			}
			
			current_polygon.points = [];
			draw_polygon(current_polygon, true);
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