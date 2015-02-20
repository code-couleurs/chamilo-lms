ChamiloHostpotCollection.prototype = {
		
	add: function(hotspot){
		this.hotspots[hotspot.id] = hotspot;
		return this;
	},
	
	remove: function(hotspot_id){
		this.hotspots[hotspot_id] = null;
		return this;
	},
	
	find: function(id){
		return this.hotspots[id];
	}
	
};

function ChamiloHostpotCollection(){
	this.hotspots = {};
}