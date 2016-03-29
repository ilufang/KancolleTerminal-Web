ShipInfoTableArea = (function() {
var $, $$, Col, Divider, Grid, Panel, ROOT, React, ReactBootstrap, ShipInfoTable, ShipInfoTableArea, Slotitems, Table, _, __, collator, jpCollator, nameCompare, path, resolveTime, resultPanelTitle,
	indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

$ = window.$, $$ = window.$$, _ = window._, React = window.React, ReactBootstrap = window.ReactBootstrap, ROOT = window.ROOT, path = window.path, resolveTime = window.resolveTime, __ = window.__;

Divider = React.createClass({
	displayName: "exports",
	render: function() {
		return React.createElement("div", {
			"className": "divider"
		}, React.createElement("hr", null), React.createElement("h5", null, this.props.text + '  ', (this.props.icon ? this.props.show ? React.createElement(FontAwesome, {
			"name": 'chevron-circle-down'
		}) : React.createElement(FontAwesome, {
			"name": 'chevron-circle-right'
		}) : void 0)));
	}
});

Panel = ReactBootstrap.Panel, Table = ReactBootstrap.Table, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col;

collator = new Intl.Collator();

jpCollator = new Intl.Collator("ja-JP");

nameCompare = function(a, b) {
	if (a.yomi === b.yomi) {
		if (a.lv === b.lv) {
			return collator.compare(a.id, b.id);
		} else {
			return collator.compare(a.lv, b.lv);
		}
	} else {
		return jpCollator.compare(a.yomi, b.yomi);
	}
};

resultPanelTitle = React.createElement("h3", null, __('Ship Girls Info'));

Slotitems = React.createClass({displayName: "Slotitems",
	getBackgroundStyle: function() {
		if (window.isDarkTheme) {
			return {
				backgroundColor: 'rgba(33, 33, 33, 0.7)'
			};
		} else {
			return {
				backgroundColor: 'rgba(256, 256, 256, 0.7)'
			};
		}
	},
	render: function() {
		var $slotitems, _slotitems, item, itemId, itemInfo, name;
		return React.createElement("div", {
			"className": "slotitem-container"
		}, ((function() {
			var i, len, ref, results;
			$slotitems = window.$slotitems, _slotitems = window._slotitems;
			ref = this.props.slot.concat(this.props.exslot);
			results = [];
			for (i = 0, len = ref.length; i < len; i++) {
				itemId = ref[i];
				if (itemId <= 0) {
					continue;
				}
				item = _slotitems[itemId];
				if (item != null) {
					itemInfo = $slotitems[item.api_slotitem_id];
					if (item.api_level > 0) {
						name = itemInfo.api_name + ' ' + item.api_level + '★';
					} else {
						name = itemInfo.api_name;
					}
					results.push(React.createElement("span", null, React.createElement("img", {
						"key": itemId,
						"title": name,
						"src": 'assets/img/slotitem/'+(itemInfo.api_type[3]+100)+".png"
					}), (itemId === this.props.exslot ? React.createElement("span", {
						"className": 'slotitem-onslot',
						"style": this.getBackgroundStyle()
					}, "+") : void 0)));
				} else {
					results.push(void 0);
				}
			}
			return results;
		}).call(this)));
	}
});

ShipInfoTable = React.createClass({displayName: "ShipInfoTable",
	shouldComponentUpdate: function(nextProps, nextState) {
		if (nextProps.dataVersion !== this.props.dataVersion) {
			if (!_.isEqual(nextProps.shipInfo, this.props.shipInfo)) {
				return true;
			}
		}
		return false;
	},
	render: function() {
		var condColor, karyoku, karyokuClass, karyokuMax, karyokuNow, karyokuString, karyokuToInc, locked, lucky, luckyClass, luckyMax, luckyNow, luckyString, luckyToInc, lv, maxhp, nowhp, raisou, raisouClass, raisouMax, raisouNow, raisouString, raisouToInc, repairColor, soukou, soukouClass, soukouMax, soukouNow, soukouString, soukouToInc, taiku, taikuClass, taikuMax, taikuNow, taikuString, taikuToInc;
		karyokuNow = this.props.shipInfo.houg[0] + this.props.shipInfo.kyouka[0];
		karyokuMax = this.props.shipInfo.karyoku[1];
		karyoku = this.props.shipInfo.karyoku[0];
		raisouNow = this.props.shipInfo.raig[0] + this.props.shipInfo.kyouka[1];
		raisouMax = this.props.shipInfo.raisou[1];
		raisou = this.props.shipInfo.raisou[0];
		taikuNow = this.props.shipInfo.tyku[0] + this.props.shipInfo.kyouka[2];
		taikuMax = this.props.shipInfo.taiku[1];
		taiku = this.props.shipInfo.taiku[0];
		soukouNow = this.props.shipInfo.souk[0] + this.props.shipInfo.kyouka[3];
		soukouMax = this.props.shipInfo.soukou[1];
		soukou = this.props.shipInfo.soukou[0];
		luckyNow = this.props.shipInfo.luck[0] + this.props.shipInfo.kyouka[4];
		luckyMax = this.props.shipInfo.lucky[1];
		lucky = this.props.shipInfo.lucky[0];
		lv = this.props.shipInfo.lv;
		nowhp = this.props.shipInfo.nowhp;
		maxhp = this.props.shipInfo.maxhp;
		locked = this.props.shipInfo.locked;
		karyokuClass = 'td-karyoku';
		raisouClass = 'td-raisou';
		taikuClass = 'td-taiku';
		soukouClass = 'td-soukou';
		luckyClass = 'td-lucky';
		karyokuToInc = karyokuMax - karyokuNow;
		karyokuString = '+' + karyokuToInc;
		raisouToInc = raisouMax - raisouNow;
		raisouString = '+' + raisouToInc;
		taikuToInc = taikuMax - taikuNow;
		taikuString = '+' + taikuToInc;
		soukouToInc = soukouMax - soukouNow;
		soukouString = '+' + soukouToInc;
		luckyToInc = luckyMax - luckyNow;
		luckyString = '+' + luckyToInc;
		if (karyokuNow === karyokuMax) {
			karyokuClass = 'td-karyoku-max';
			karyokuString = 'MAX';
		}
		if (raisouNow === raisouMax) {
			raisouClass = 'td-raisou-max';
			raisouString = 'MAX';
		}
		if (taikuNow === taikuMax) {
			taikuClass = 'td-taiku-max';
			taikuString = 'MAX';
		}
		if (soukouNow === soukouMax) {
			soukouClass = 'td-soukou-max';
			soukouString = 'MAX';
		}
		if (luckyNow === luckyMax) {
			luckyClass = 'td-lucky-max';
			luckyString = 'MAX';
		}
		if (nowhp * 4 <= maxhp) {
			repairColor = 'rgba(255, 0, 0, 0.4)';
		} else if (nowhp * 2 <= maxhp) {
			repairColor = 'rgba(255, 65, 0, 0.4)';
		} else if (nowhp * 4 <= maxhp * 3) {
			repairColor = 'rgba(255, 255, 0, 0.4)';
		} else {
			repairColor = 'transparent';
		}
		if (this.props.shipInfo.cond >= 0 && this.props.shipInfo.cond < 20) {
			condColor = 'rgba(255, 0, 0, 0.4)';
		} else if (this.props.shipInfo.cond >= 20 && this.props.shipInfo.cond < 30) {
			condColor = 'rgba(255, 165, 0, 0.4)';
		} else if (this.props.shipInfo.cond >= 50 && this.props.shipInfo.cond <= 100) {
			condColor = 'rgba(255, 255, 0, 0.4)';
		} else {
			condColor = 'transparent';
		}
		return React.createElement("tr", null,
		React.createElement("td", null, this.props.shipInfo.id),
		React.createElement("td", null, this.props.shipInfo.type),
		React.createElement("td", {
			style: {
				wordBreak: "keep-all",
				whiteSpace: "nowrap",
				overflow: "hidden"
			}
		}, this.props.shipInfo.name),
		React.createElement("td", {
			"className": 'center'
		}, this.props.shipInfo.lv), React.createElement("td", {
			"className": 'center',
			"style": {
				backgroundColor: condColor
			}
		}, this.props.shipInfo.cond), React.createElement("td", {
			"className": karyokuClass
		}, karyoku + '/', React.createElement("span", {
			"style": {
				fontSize: '80%'
			}
		}, karyokuString)), React.createElement("td", {
			"className": raisouClass
		}, raisou + '/', React.createElement("span", {
			"style": {
				fontSize: '80%'
			}
		}, raisouString)), React.createElement("td", {
			"className": taikuClass
		}, taiku + '/', React.createElement("span", {
			"style": {
				fontSize: '80%'
			}
		}, taikuString)), React.createElement("td", {
			"className": soukouClass
		}, soukou + '/', React.createElement("span", {
			"style": {
				fontSize: '80%'
			}
		}, soukouString)), React.createElement("td", {
			"className": luckyClass
		}, lucky + '/', React.createElement("span", {
			"style": {
				fontSize: '80%'
			}
		}, luckyString)), React.createElement("td", {
			"className": 'center'
		}, this.props.shipInfo.sakuteki), React.createElement("td", {
			"className": 'center',
			"style": {
				backgroundColor: repairColor
			}
		}, resolveTime(this.props.shipInfo.repairtime)), React.createElement("td", null, React.createElement(Slotitems, {
			"slot": this.props.shipInfo.slot,
			"exslot": this.props.shipInfo.exslot
		})), React.createElement("td", null, (locked === 1 ? React.createElement(FontAwesome, {
			"name": 'lock'
		}) : ' ')));
	}
});

return React.createClass({displayName: "ShipInfoTableArea",
	getInitialState: function() {
		return {
			rows: [],
			show: false,
			dataVersion: 0
		};
	},
	handleResponse: function(e) {
		var $shipTypes, $ships, _shipId, _ships, body, method, postBody, ref, row, rows, rowsUpdateFlag, ship;
		ref = e.detail, method = ref.method, path = ref.path, body = ref.body, postBody = ref.postBody;
		$shipTypes = window.$shipTypes, $ships = window.$ships, _ships = window._ships;
		rows = this.state.rows;
		rowsUpdateFlag = false;
		switch (path) {
			case '/kcsapi/api_port/port':
			case '/kcsapi/api_req_kousyou/destroyship':
			case '/kcsapi/api_req_kaisou/powerup':
			case '/kcsapi/api_get_member/ship3':
				rowsUpdateFlag = true;
				rows = [];
				for (_shipId in _ships) {
					ship = _ships[_shipId];
					row = {
						id: ship.api_id,
						type_id: $ships[ship.api_ship_id].api_stype,
						type: $shipTypes[$ships[ship.api_ship_id].api_stype].api_name,
						name: $ships[ship.api_ship_id].api_name,
						yomi: $ships[ship.api_ship_id].api_yomi,
						sortno: $ships[ship.api_ship_id].api_sortno,
						lv: ship.api_lv,
						cond: ship.api_cond,
						karyoku: ship.api_karyoku,
						houg: ship.api_houg,
						raisou: ship.api_raisou,
						raig: ship.api_raig,
						taiku: ship.api_taiku,
						tyku: ship.api_tyku,
						soukou: ship.api_soukou,
						souk: ship.api_souk,
						lucky: ship.api_lucky,
						luck: ship.api_luck,
						kyouka: ship.api_kyouka,
						sakuteki: ship.api_sakuteki[0],
						slot: ship.api_slot,
						exslot: ship.api_slot_ex,
						locked: ship.api_locked,
						nowhp: ship.api_nowhp,
						maxhp: ship.api_maxhp,
						losshp: ship.api_maxhp - ship.api_nowhp,
						repairtime: parseInt(ship.api_ndock_time / 1000.0),
						after: ship.api_aftershipid
					};
					rows.push(row);
				}
				break;
			case '/kcsapi/api_req_kousyou/getship':
				rowsUpdateFlag = true;
				ship = body.api_ship;
				row = {
					id: ship.api_id,
					type_id: $ships[ship.api_ship_id].api_stype,
					type: $shipTypes[$ships[ship.api_ship_id].api_stype].api_name,
					name: $ships[ship.api_ship_id].api_name,
					yomi: $ships[ship.api_ship_id].api_yomi,
					sortno: $ships[ship.api_ship_id].api_sortno,
					lv: ship.api_lv,
					cond: ship.api_cond,
					karyoku: ship.api_karyoku,
					houg: $ships[ship.api_ship_id].api_houg,
					raisou: ship.api_raisou,
					raig: $ships[ship.api_ship_id].api_raig,
					taiku: ship.api_taiku,
					tyku: $ships[ship.api_ship_id].api_tyku,
					soukou: ship.api_soukou,
					souk: $ships[ship.api_ship_id].api_souk,
					lucky: ship.api_lucky,
					luck: $ships[ship.api_ship_id].api_luck,
					kyouka: ship.api_kyouka,
					sakuteki: ship.api_sakuteki[0],
					slot: ship.api_slot,
					exslot: ship.api.slot_ex,
					locked: ship.api_locked,
					nowhp: ship.api_nowhp,
					maxhp: ship.api_maxhp,
					losshp: ship.api_maxhp - ship.api_nowhp,
					repairtime: parseInt(ship.api_ndock_time / 1000.0),
					after: ship.api_aftershipid
				};
				rows.push(row);
		}
		if (rowsUpdateFlag) {
			if (this.state.dataVersion > 12450) {
				return this.setState({
					rows: rows,
					show: true,
					dataVersion: 1
				});
			} else {
				return this.setState({
					rows: rows,
					show: true,
					dataVersion: this.state.dataVersion + 1
				});
			}
		}
	},
	handleTypeFilter: function(type, shipTypes) {
		if (indexOf.call(shipTypes, type) >= 0) {
			return true;
		} else {
			return false;
		}
	},
	handleLvFilter: function(lv) {
		switch (this.props.lvRadio) {
			case 0:
				return true;
			case 1:
				if (lv === 1) {
					return true;
				} else {
					return false;
				}
				break;
			case 2:
				if (lv >= 2) {
					return true;
				} else {
					return false;
				}
		}
	},
	handleLockedFilter: function(locked) {
		switch (this.props.lockedRadio) {
			case 0:
				return true;
			case 1:
				if (locked === 1) {
					return true;
				} else {
					return false;
				}
				break;
			case 2:
				if (locked === 0) {
					return true;
				} else {
					return false;
				}
		}
	},
	handleExpeditionFilter: function(id, expeditionShips) {
		switch (this.props.expeditionRadio) {
			case 0:
				return true;
			case 1:
				if (indexOf.call(expeditionShips, id) >= 0) {
					return true;
				} else {
					return false;
				}
				break;
			case 2:
				if (indexOf.call(expeditionShips, id) >= 0) {
					return false;
				} else {
					return true;
				}
		}
	},
	handleModernizationFilter: function(ship) {
		var isCompleted, karyokuMax, karyokuNow, raisouMax, raisouNow, soukouMax, soukouNow, taikuMax, taikuNow;
		karyokuNow = ship.houg[0] + ship.kyouka[0];
		karyokuMax = ship.karyoku[1];
		raisouNow = ship.raig[0] + ship.kyouka[1];
		raisouMax = ship.raisou[1];
		taikuNow = ship.tyku[0] + ship.kyouka[2];
		taikuMax = ship.taiku[1];
		soukouNow = ship.souk[0] + ship.kyouka[3];
		soukouMax = ship.soukou[1];
		isCompleted = karyokuNow >= karyokuMax && raisouNow >= raisouMax && taikuNow >= taikuMax && soukouNow >= soukouMax;
		switch (this.props.modernizationRadio) {
			case 0:
				return true;
			case 1:
				return isCompleted;
			case 2:
				return !isCompleted;
		}
	},
	handleRemodelFilter: function(ship) {
		var remodelable;
		remodelable = ship.after !== "0";
		switch (this.props.remodelRadio) {
			case 0:
				return true;
			case 1:
				return !remodelable;
			case 2:
				return remodelable;
		}
	},
	handleShowRows: function() {
		var $shipTypes, deck, decks, expeditionShips, i, j, k, l, len, len1, len2, len3, ref, ref1, ref2, row, shipId, shipTypes, showRows, x;
		$shipTypes = window.$shipTypes;
		shipTypes = [];
		if ($shipTypes != null) {
			ref = this.props.shipTypeBoxes;
			for (i = 0, len = ref.length; i < len; i++) {
				x = ref[i];
				shipTypes.push($shipTypes[x].api_name);
			}
		}
		decks = window._decks;
		expeditionShips = [];
		if (decks != null) {
			for (j = 0, len1 = decks.length; j < len1; j++) {
				deck = decks[j];
				if (deck.api_mission[0] === 1) {
					ref1 = deck.api_ship;
					for (k = 0, len2 = ref1.length; k < len2; k++) {
						shipId = ref1[k];
						if (shipId === -1) {
							continue;
						}
						expeditionShips.push(shipId);
					}
				}
			}
		}
		showRows = [];
		ref2 = this.state.rows;
		for (l = 0, len3 = ref2.length; l < len3; l++) {
			row = ref2[l];
			if (this.handleTypeFilter(row.type, shipTypes) && this.handleLvFilter(row.lv) && this.handleLockedFilter(row.locked) && this.handleExpeditionFilter(row.id, expeditionShips) && this.handleModernizationFilter(row) && this.handleRemodelFilter(row)) {
				showRows.push(row);
			}
		}
		switch (this.props.sortName) {
			case 'name':
				showRows = showRows.sort(nameCompare);
				break;
			case 'karyoku':
				showRows.sort(function(a, b) {
					return a.karyoku[0]-b.karyoku[0];
				});
				break;
			case 'raisou':
				showRows.sort(function(a, b) {
					return a.raisou[0]-b.raisou[0];
				});
				break;
			case 'taiku':
				showRows.sort(function(a, b) {
					return a.taiku[0]-b.taiku[0];
				});
				break;
			case 'soukou':
				showRows.sort(function(a, b) {
					return a.soukou[0]-b.soukou[0];
				});
				break;
			case 'lucky':
				showRows.sort(function(a, b) {
					return a.lucky[0]-b.lucky[0];
				});
				break;
			case 'lv':
				showRows.sort(function(a, b) {
					if (a.lv !== b.lv) {
						return a.lv - b.lv;
					}
					if (a.sortno !== b.sortno) {
						return -(a.sortno - b.sortno);
					}
					if (a.id !== b.id) {
						return a.id - b.id;
					} else {
						return 0;
					}
				});
				break;
			case 'type':
				showRows.sort(function(a, b) {
					if (a.type_id !== b.type_id) {
						return a.type_id - b.type_id;
					}
					if (a.sortno !== b.sortno) {
						return -(a.sortno - b.sortno);
					}
					if (a.lv !== b.lv) {
						return a.lv - b.lv;
					}
					if (a.id !== b.id) {
						return a.id - b.id;
					} else {
						return 0;
					}
				});
				break;
			default:
				var sortName = this.props.sortName;
				showRows.sort(function(a, b) {
					return a[sortName]-b[sortName];
				});
		}
		if (!this.props.sortOrder) {
			showRows.reverse();
		}
		return showRows;
	},
	handleClickTitle: function(title) {
		var order;
		if (this.props.sortName !== title) {
			order = title === 'id' || title === 'type' || title === 'name' ? 1 : 0;
			return this.props.sortRules(title, order);
		} else {
			return this.props.sortRules(this.props.sortName, (this.props.sortOrder + 1) % 2);
		}
	},
	componentDidMount: function() {
		this.setState({
			rows: this.state.rows,
			show: true,
			dataVersion: this.state.dataVersion + 1
		});
		return window.addEventListener('game.response', this.handleResponse);
	},
	componentWillUnmount: function() {
		this.setState({
			rows: this.state.rows,
			show: false
		});
		return window.removeEventListener('game.response', this.handleResponse);
	},
	render: function() {
		var index, row, showRows;
		showRows = [];
		if (this.state.show) {
			showRows = this.handleShowRows();
		}
		return React.createElement("div", {
			"id": "ship-info-show"
		}, React.createElement(Divider, {
			"text": __('Ship Girls Info'),
			"icon": false
		}), React.createElement(Grid, null, React.createElement(Col, {
			"xs": 1.,
			"style": {
				padding: '0 0 0 15px',
				width: "5em"
			}
		}, React.createElement(Table, {
			"striped": true,
			"condensed": true,
			"hover": true
		}, React.createElement("thead", null, React.createElement("tr", null, React.createElement("th", null, "NO"))), React.createElement("tbody", null, (function() {
			var i, len, results;
			results = [];
			for (index = i = 0, len = showRows.length; i < len; index = ++i) {
				row = showRows[index];
				results.push(React.createElement("tr", {
					"key": index
				}, React.createElement("td", null, index + 1)));
			}
			return results;
		})()))), React.createElement(Col, {
			"xs": 11.,
			"style": {
				padding: '0 15px 0 0'
			}
		}, React.createElement(Table, {
			"striped": true,
			"condensed": true,
			"hover": true
		}, React.createElement("thead", null, React.createElement("tr", null, React.createElement("th", {
			"className": 'clickable',
			"onClick": this.handleClickTitle.bind(this, 'id')
		}, __('ID')), React.createElement("th", {
			"className": 'clickable',
			"onClick": this.handleClickTitle.bind(this, 'type')
		}, __('Class')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'name')
		}, __('Name')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'lv')
		}, __('Level')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'cond')
		}, __('Cond')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'karyoku')
		}, __('Firepower')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'raisou')
		}, __('Torpedo')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'taiku')
		}, __('AA')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'soukou')
		}, __('Armor')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'lucky')
		}, __('Luck')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'sakuteki')
		}, __('LOS')), React.createElement("th", {
			"className": 'center clickable',
			"onClick": this.handleClickTitle.bind(this, 'repairtime')
		}, __('Repair')), React.createElement("th", {
			className: "center",
			style: {
				width: "170px"
			}
		}, __('Equipment')), React.createElement("th", null, __('Lock')))), React.createElement("tbody", null, ((function() {
			var i, len, results;
			if (this.state.show) {
				results = [];
				for (index = i = 0, len = showRows.length; i < len; index = ++i) {
					row = showRows[index];
					results.push(React.createElement(ShipInfoTable, {
						"key": row.id,
						"shipInfo": row,
						"dataVersion": this.state.dataVersion
					}));
				}
				return results;
			}
		}).call(this)))))));
	}
});
})();
