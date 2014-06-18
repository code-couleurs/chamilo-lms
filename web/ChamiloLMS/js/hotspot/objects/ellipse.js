ChamiloEllipse.prototype = new ChamiloGeometry();
ChamiloEllipse.prototype.constructor = ChamiloEllipse;

ChamiloEllipse.prototype.handleClick = function(e){
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

ChamiloEllipse.prototype.draw = function(){
	
	var self = this;
	if(self.ellipse)
	{
		self.ellipse.remove();
	}
	
	if(self.points.length < 2)
		return false;
	
	var x1 = self.points[0].attr('cx');
	var y1 = self.points[0].attr('cy');
	var x2 = Math.abs(self.points[1].attr('cx')-x1);
	var y2 = Math.abs(self.points[1].attr('cy')-y1);
	self.ellipse = self.paper.ellipse(x1,y1,x2,y2).attr({
		fill: self.color,
		opacity: 0.6
	});
	
};

function ChamiloEllipse(paper, color){
	ChamiloGeometry.call(this, paper, color);
}

