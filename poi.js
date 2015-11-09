var poi = {
	handleResponse: function(e) {
		var req = e.detail;
		switch(req.path) {
			case '/kcsapi/api_start2':

				window.$slotitems = [];
				for(var i=0; i<req.body.api_mst_slotitem.length; i++) {
					window.$slotitems[req.body.api_mst_slotitem[i].api_id] = req.body.api_mst_slotitem[i];
				}

				window.$ships = [];
				for(var i=0; i<req.body.api_mst_ship.length; i++) {
					window.$ships[req.body.api_mst_ship[i].api_id] = req.body.api_mst_ship[i];
				}

				break;
			case '/kcsapi/api_port/port':
				window._decks = req.body.api_deck_port;
				window._ships = {};
				for(var i in req.body.api_ship) {
					var idx = req.body.api_ship[i].api_id;
					var shipid = req.body.api_ship[i].api_ship_id;
					window._ships[idx] = req.body.api_ship[i];
					for (var attr in window.$ships[shipid]) {
						window._ships[idx][attr] = window.$ships[shipid][attr];
					}
				}
				break;
		}
	}
}
