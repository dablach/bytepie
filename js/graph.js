$(document).ready(function() {
	var svg = document.getElementById('bytepie-graph');
	var svgSize = -parseInt(svg.getAttribute('viewBox').split()[0])-50;
	var cursorLine = $(document.createElementNS('http://www.w3.org/2000/svg','line')).attr({x1:0,y1:0,'stroke-width':'1',stroke:'black','vector-effect':'non-scaling-stroke','pointer-events':'none','stroke-opacity':0.3}).appendTo($('#bytepie-graph'));
	var cursorPt = svg.createSVGPoint();
	var svgMatrix = svg.getScreenCTM().inverse();
	var legend = [];
	$('#folders path',svg).add('#folders circle',svg).mousemove(function(e) {
		if($(this).data('parent') == '') {
			cursorLine.attr({x2:0,y2:0});
			var level = 0;
		} else {
			cursorPt.x = e.clientX;
			cursorPt.y = e.clientY;
			var cursor = cursorPt.matrixTransform(svgMatrix);
			var length = Math.sqrt(cursor.x*cursor.x+cursor.y*cursor.y);
			var dx = cursor.x/length;
			var dy = cursor.y/length;
			var level = Math.floor(length/10);
			length = level*10+5;
			cursorLine.attr({x2:dx*length,y2:dy*length});
			var path = $(this);
			for(i = level-1; i >= 0; i--) {
				if(!(i in legend)) {
					legend[i] = $(document.createElementNS('http://www.w3.org/2000/svg','line')).attr({stroke:'black','stroke-width':6,'stroke-linecap':'round','vector-effect':'non-scaling-stroke','pointer-events':'none'})
						.add($(document.createElementNS('http://www.w3.org/2000/svg','line')).attr({stroke:'black','stroke-width':1,'vector-effect':'non-scaling-stroke','pointer-events':'none'}))
						.add($(document.createElementNS('http://www.w3.org/2000/svg','text'))
							.append($(document.createElementNS('http://www.w3.org/2000/svg','tspan')).attr({dy:'1.2em'}))
							.append($(document.createElementNS('http://www.w3.org/2000/svg','tspan')).attr({dy:'1.2em'})));
					$(svg).append(legend[i]);
				}
				var l = i*10+15;
				var x = dx*l; y = dy*l;
				var ty = Math.abs(dy) > 0.7 ? y : dy*(0.7/Math.abs(dy)*(l-svgSize)+svgSize);
				$(legend[i][0]).attr({x1:x,y1:y,x2:x,y2:y});
				$(legend[i][1]).attr({x1:x,y1:y,x2:dx < 0 ? -svgSize-10 : svgSize+10,y2:ty});
				var tspans = $(legend[i][2]).attr({y:ty-3,'text-anchor':dx < 0 ? 'end' : 'start'}).children().attr({x:dx < 0 ? -svgSize-11 : svgSize+11});
				$(tspans[0]).text(path.data('name'));
				$(tspans[1]).text(path.data('size'));
				path = $('#'+path.data('parent'));
			}
		}
		for(i = legend.length; i > level; i--) {
			legend.pop().remove();
		}
	});
	$('#folders',svg).mouseleave(function() {
		while(legend.length) {
			legend.pop().remove();
		}
		cursorLine.attr({x2:0,y2:0});
	});
	$(window).resize(function() {
		svgMatrix = svg.getScreenCTM().inverse();
	});
});
