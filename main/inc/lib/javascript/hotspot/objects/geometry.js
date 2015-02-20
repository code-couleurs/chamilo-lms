ChamiloGeometry.prototype = {
	
	dragging : false,
	type: false, // should be implemented by child
	
	draw: function(){
		// implemented by childs
	},
	
	clear: function(){
		var self = this;
		for(var i in self.points){
			self.points[i].remove();
		}
		self.points = [];
		self.draw();
	},
	
	addPoint: function(x, y){
		var self = this;
		var point = self.paper.circle(x,y,3).attr({
			fill: self.color,
			cursor: "move",
			"stroke-width": 20,
			stroke: "transparent"
		});
		self.paper.set(point).drag(function(dx, dy){self.handleWhileDraggingPoint(point, dx, dy);}, function(){self.handleStartDraggingPoint(point);}, function(){self.handleEndDraggingPoint(point);});
		self.points.push(point);
	},
	
	handleWhileDraggingPoint: function(point, dx, dy){
		var self = this;
		self.dragging = true;
		point.attr({cx: point.ox + dx, cy: point.oy + dy});
		self.draw();
	},
	
	handleEndDraggingPoint: function() {
		var self = this;
		setTimeout(function(){
			self.dragging = false;	
		},500);
	},
	handleStartDraggingPoint: function(point) {
		point.ox = point.attr("cx");
		point.oy = point.attr("cy");
	},
	setColor: function(color) {
		this.color = color;
		for(var i in this.points){
			this.points[i].attr('fill', color);
		}
		this.draw();
	},
	export: function() {
		return false; // should be implemented by child
	}
	
};

ChamiloGeometry.forge = function(paper, color, coordinates, type) {
	switch (type)
	{
		case 'circle':
			return new ChamiloEllipse(paper, color, coordinates);
			break;
		case 'square':
			return new ChamiloRectangle(paper, color, coordinates);
			break;
		case 'poly':
		default: 
			return new ChamiloPolygon(paper, color, coordinates);
			break;
	}
	return false;
};

function ChamiloGeometry(paper, color){
	this.color = color;
	this.paper = paper;
	this.points = [];
}
