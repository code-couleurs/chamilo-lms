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
		
		var polygons = new Array();
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
		
		$('#paper').dblclick(function(e){
			if(current_polygon)
			{
				draw_polygon(current_polygon, true);
				current_polygon = false;
			}
		});
		
		function add_point(x, y)
		{
			if(!current_polygon)
			{
				current_polygon = {
					path: false,
					points: []
				};
				polygons.push(current_polygon);
			}
			var point = paper.circle(x,y,5).attr({
				fill: "green",
				cursor: "move",
				"stroke-width": 20,
				stroke: "transparent"
			});
			paper.set(point).drag(move, start, up);
			current_polygon.points.push(point);
			draw_polygon(current_polygon);			
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
				for(var i in current_polygon.points)
				{
					polygon_str += ' '+polygon.points[i].attr('cx')+' '+polygon.points[i].attr('cy');
					if(i != current_polygon.points.length-1)
					{
						current_polygon.points[i].attr('r', '3');
					}
				}
				if(finish)
				{
					polygon_str += 'Z'; 
				}
				polygon.path = paper.path(polygon_str).attr('fill', 'red').attr('opacity', 0.6);
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
	});
</script>

<div id="paper" style="background: url(<?php echo api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/images/'.$objQuestion->picture; ?>); width:545px; height: 306px;"></div>