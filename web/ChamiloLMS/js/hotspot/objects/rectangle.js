ChamiloRectangle.prototype = new ChamiloGeometry();
ChamiloRectangle.prototype.constructor = ChamiloRectangle;

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

function ChamiloRectangle(paper, color){
	ChamiloGeometry.call(this, paper, color);
}

