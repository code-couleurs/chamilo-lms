ChamiloRectangle.prototype = new ChamiloGeometry();
ChamiloRectangle.prototype.constructor = ChamiloRectangle;

ChamiloRectangle.prototype.type = 'square';

ChamiloRectangle.prototype.handleClick = function(e){
	var self = this;
	if(!self.dragging && self.points.length < 2)
	{
		var canvas_offset = $(self.paper.canvas).offset();
		var relX = e.pageX - canvas_offset.left;
		var relY = e.pageY - canvas_offset.top;
		self.addPoint(relX, relY);
		self.draw();
	}
};

ChamiloRectangle.prototype.draw = function(){
	
	var self = this;
	if(self.rect)
	{
		self.rect.remove();
	}
	
	if(self.points.length < 2)
		return false;
	var x1 = self.points[0].attr('cx');
	var y1 = self.points[0].attr('cy');
	var x2 = Math.abs(self.points[1].attr('cx')-x1);
	var y2 = Math.abs(self.points[1].attr('cy')-y1);
	self.rect = self.paper.rect(x1,y1,x2,y2).attr({
		fill: self.color,
		opacity: 0.6
	});
	
};

ChamiloRectangle.prototype.export = function() {
	if(this.points.length < 2)
		return false;
	var ret = this.points[0].attr('cx')+';'+this.points[0].attr('cy')+'|'; // coords first point
	ret += (this.points[1].attr('cx') - this.points[0].attr('cx'))+'|';//width;
	ret += (this.points[1].attr('cy') - this.points[0].attr('cy'))+'|';//height;
	return ret;
};

function ChamiloRectangle(paper, color, coordinates){
	ChamiloGeometry.call(this, paper, color);
	if(coordinates)
	{
		var coords_elements = coordinates.split('|');
		if(coords_elements.length >= 3)
		{
			var first_point_coords = coords_elements[0].split(';');
			this.addPoint(first_point_coords[0], first_point_coords[1]);
			this.addPoint(parseInt(first_point_coords[0]) + parseInt(coords_elements[1]), parseInt(first_point_coords[1]) + parseInt(coords_elements[2]));
			this.draw();
		}
	}
}

