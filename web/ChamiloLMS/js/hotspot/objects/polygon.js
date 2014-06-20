// chamilo polygon inherits chamilo geometry
ChamiloPolygon.prototype = new ChamiloGeometry();
ChamiloPolygon.prototype.constructor = ChamiloPolygon;

/********************
 **** properties ****
 ********************/

ChamiloPolygon.prototype.type = 'poly';

/*******************
 **** functions ****
 *******************/

ChamiloPolygon.prototype.handleClick = function(e){
	var self = this;
	if(!self.dragging)
	{
		var canvas_offset = $(self.paper.canvas).offset();
		var relX = e.pageX - canvas_offset.left;
		var relY = e.pageY - canvas_offset.top;
		self.addPoint(relX, relY);
		self.draw();
	}
};

ChamiloPolygon.prototype.draw = function(){
	
	var self = this;
	
	// if polygon already has a path, we remove it before redraw
	if(self.path)
	{
		self.path.remove();
	}
	if(self.points.length > 1)
	{
		var polygon_str = 'M ';
		for(var i in self.points)
		{
			polygon_str += ' '+self.points[i].attr('cx')+' '+self.points[i].attr('cy');
			if(i !== self.points.length-1)
			{
				self.points[i].attr('r', '3');
			}
		}
		polygon_str += 'Z'; 
		self.path = self.paper.path(polygon_str).attr('fill', self.color).attr('opacity', 0.6);
	}
	
};

ChamiloPolygon.prototype.export = function() {
	var ret = '';
	for(var i in this.points)
	{
		ret += this.points[i].attr('cx')+';'+this.points[i].attr('cy')+'|';
	}
	return ret;
};


/*******************
 **** constructor ****
 *******************/

function ChamiloPolygon(paper, color, coordinates){
	ChamiloGeometry.call(this, paper, color);
	
	if(coordinates)
	{
		var points_to_draw = coordinates.split('|');
		for(var i in points_to_draw)
		{
			if(!points_to_draw[i])
				continue;
			var point_coords = points_to_draw[i].split(';');
			this.addPoint(point_coords[0], point_coords[1]);
		}
		this.draw();
	}
}

