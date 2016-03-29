(function() {
	var Button, Col, Divider, FontAwesome, Grid, ItemInfoTable, ItemInfoTableArea, OverlayTrigger, Panel, React, ReactBootstrap, Row, Table, Tooltip, __, jQuery,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

	React = window.React, ReactBootstrap = window.ReactBootstrap, jQuery = window.jQuery, FontAwesome = window.FontAwesome, __ = window.__;

	Panel = ReactBootstrap.Panel, Row = ReactBootstrap.Row, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col, Table = ReactBootstrap.Table, Button = ReactBootstrap.Button, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	Divider = React.createClass({displayName: "exports",
		render: function() {
			return React.createElement("div", {
			"className": "divider"
			}, React.createElement("h5", null, this.props.text), React.createElement("hr", null));
		}
	});

	ItemInfoTable = React.createClass({displayName: "ItemInfoTable",
		render: function() {
			var $ships, $slotitems, _ships, index, level, path, ship;
			$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships;
			return React.createElement("tr", {
				"className": "vertical"
			}, React.createElement("td", {
				"style": {
					paddingLeft: 10
				}
			}, React.createElement("img", {
				"key": this.props.slotItemType,
				"src": 'assets/img/slotitem/'+ (this.props.itemPngIndex + 100) + ".png"
			}), $slotitems[this.props.slotItemType].api_name), React.createElement("td", {
				"className": 'center'
			}, this.props.sumNum + ' ', React.createElement("span", {
				"style": {
					fontSize: '12px'
				}
			}, '(' + this.props.restNum + ')')), React.createElement("td", null, React.createElement(Table, {
				"id": 'equip-table'
			}, React.createElement("tbody", null, (function() {
				var i, results;
				results = [];
				for (level = i = 0; i <= 10; level = ++i) {
					if (this.props.levelList[level] != null) {
						results.push(React.createElement("tr", {
							"key": level
						}, React.createElement("td", {
							"style": {
								width: '13%'
							}
						}, level + '★' + ' × ' + this.props.levelList[level]), React.createElement("td", null, ((function() {
							var j, len, ref, results1;
							if (this.props.equipList[level] != null) {
								ref = this.props.equipList[level];
								results1 = [];
								for (index = j = 0, len = ref.length; j < len; index = ++j) {
									ship = ref[index];
									results1.push(React.createElement("div", {
										"key": index,
										"className": 'equip-list-div'
									}, React.createElement("span", {
										"className": 'equip-list-div-span'
									}, 'Lv.' + ship.shipLv), _ships[ship.shipId].api_name));
								}
								return results1;
							}
						}).call(this)))));
					} else {
						results.push(void 0);
					}
				}
				return results;
			}).call(this)))));
		}
	});

	ItemInfoTableArea = React.createClass({displayName: "ItemInfoTableArea",
		getInitialState: function() {
			return {
				rows: []
			};
		},
		handleResponse: function(e) {
			var $ships, $slotitems, _, _shipId, _ships, _slotId, _slotitems, body, equip, equipAdd, findShip, i, j, k, l, len, len1, len2, len3, level, method, path, postBody, ref, ref1, ref2, ref3, row, rows, ship, shipIdTmp, slot, slotId, slotType;
			ref = e.detail, method = ref.method, path = ref.path, body = ref.body, postBody = ref.postBody;
			$ships = window.$ships, _ships = window._ships, _slotitems = window._slotitems, $slotitems = window.$slotitems, _ = window._;
			rows = this.state.rows;
			switch (path) {
				case '/kcsapi/api_get_member/slot_item':
				case '/kcsapi/api_req_kousyou/destroyitem2':
				case '/kcsapi/api_req_kousyou/destroyship':
				case '/kcsapi/api_req_kousyou/remodel_slot':
				case '/kcsapi/api_req_kaisou/powerup':
					rows = [];
					for (_slotId in _slotitems) {
						slot = _slotitems[_slotId];
						slotType = slot.api_slotitem_id;
						level = slot.api_level;
						if (rows[slotType] != null) {
							rows[slotType].sumNum++;
							if (rows[slotType].levelList[level] != null) {
								rows[slotType].levelList[level]++;
							} else {
								rows[slotType].levelList[level] = 1;
							}
						} else {
							row = {
								slotItemType: slotType,
								sumNum: 1,
								useNum: 0,
								equipList: [],
								levelList: []
							};
							row.levelList[level] = 1;
							rows[slotType] = row;
						}
					}
					break;
				case '/kcsapi/api_req_kousyou/getship':
					ref1 = body.api_slotitem;
					for (i = 0, len = ref1.length; i < len; i++) {
						slot = ref1[i];
						slotType = slot.api_slotitem_id;
						level = slot.api_level;
						if (rows[slotType] != null) {
							rows[slotType].sumNum++;
							if (rows[slotType].levelList[level] != null) {
								rows[slotType].levelList[level]++;
							} else {
								rows[slotType].levelList[level] = 1;
							}
						} else {
							row = {
								slotItemType: slotType,
								sumNum: 1,
								useNum: 0,
								equipList: [],
								levelList: []
							};
							row.levelList[level] = 1;
							rows[slotType] = row;
						}
					}
					break;
				case '/kcsapi/api_req_kousyou/createitem':
					if (body.api_create_flag === 1) {
						slot = body.api_slot_item;
						slotType = slot.api_slotitem_id;
						level = slot.api_level;
						if (rows[slotType] != null) {
							rows[slotType].sumNum++;
							if (rows[slotType].levelList[level] != null) {
								rows[slotType].levelList[level]++;
							} else {
								rows[slotType].levelList[level] = 1;
							}
						} else {
							row = {
								slotItemType: slotType,
								sumNum: 1,
								useNum: 0,
								equipList: [],
								levelList: []
							};
							row.levelList[level] = 1;
							rows[slotType] = row;
						}
					}
					break;
				case '/kcsapi/api_port/port':
				case '/kcsapi/api_req_kaisou/slotset':
					if (rows.length > 0) {
						for (j = 0, len1 = rows.length; j < len1; j++) {
							row = rows[j];
							if (row != null) {
								row.equipList = [];
								row.useNum = 0;
							}
						}
						for (_shipId in _ships) {
							ship = _ships[_shipId];
							ref2 = ship.api_slot;
							for (k = 0, len2 = ref2.length; k < len2; k++) {
								slotId = ref2[k];
								if (slotId === -1) {
									continue;
								}
								if (_slotitems[slotId] == null) {
									continue;
								}
								slotType = _slotitems[slotId].api_slotitem_id;
								if (slotType === -1) {
									console.log("Error:Cannot find the slotType by searching slotId from ship.api_slot");
									continue;
								}
								shipIdTmp = ship.api_id;
								if (rows[slotType] != null) {
									row = rows[slotType];
									row.useNum++;
									level = _slotitems[slotId].api_level;
									findShip = false;
									if (row.equipList[level] != null) {
										ref3 = row.equipList[level];
										for (l = 0, len3 = ref3.length; l < len3; l++) {
											equip = ref3[l];
											if (equip.shipId === shipIdTmp) {
												findShip = true;
												break;
											}
										}
									} else {
										row.equipList[level] = [];
									}
									equipAdd = null;
									if (!findShip) {
										equipAdd = {
											shipId: shipIdTmp,
											shipLv: ship.api_lv
										};
										row.equipList[level].push(equipAdd);
										rows[slotType] = row;
									}
								} else {
									console.log("Error: Not defined row");
								}
							}
						}
					}
			}
			return this.setState({
				rows: rows
			});
		},
		componentDidMount: function() {
			return window.addEventListener('game.response', this.handleResponse);
		},
		componentWillUnmount: function() {
			return window.removeEventListener('game.response', this.handleResponse);
		},
		render: function() {
			var $slotitems, _, index, itemInfo, itemPngIndex, level, printRows, row;
			return React.createElement("div", {
				"id": 'item-info-show'
			}, React.createElement(Divider, {
				"text": __('Equipment Info')
			}), React.createElement(Grid, {
				"id": "item-info-area"
			}, React.createElement(Table, {
				"striped": true,
				"condensed": true,
				"hover": true,
				"id": "main-table-item"
			}, React.createElement("thead", {
				"className": "slot-item-table-thead"
			}, React.createElement("tr", null, React.createElement("th", {
				"className": "center",
				"style": {
					width: '25%'
				}
			}, __('Name')), React.createElement("th", {
				"className": "center",
				"style": {
					width: '9%'
				}
			}, __('Total'), React.createElement("span", {
				"style": {
					fontSize: '11px'
				}
			}, '(' + __('rest') + ')')), React.createElement("th", {
				"className": "center",
				"style": {
					width: '66%'
				}
			}, __('State')))), React.createElement("tbody", null, ((function() {
				var i, j, k, len, len1, ref, ref1, results;
				_ = window._, $slotitems = window.$slotitems;
				if (this.state.rows != null) {
					itemPngIndex = null;
					printRows = [];
					ref = this.state.rows;
					for (i = 0, len = ref.length; i < len; i++) {
						row = ref[i];
						if (row != null) {
							itemInfo = $slotitems[row.slotItemType];
							itemPngIndex = itemInfo.api_type[3];
							row.itemPngIndex = itemPngIndex;
							if (ref1 = row.itemPngIndex, indexOf.call(this.props.itemTypeBoxes, ref1) >= 0) {
								printRows.push(row);
							}
						}
					}
					printRows = _.sortBy(printRows, 'itemPngIndex');
					results = [];
					for (index = j = 0, len1 = printRows.length; j < len1; index = ++j) {
						row = printRows[index];
						for (level = k = 0; k <= 10; level = ++k) {
							if (row.equipList[level] != null) {
								row.equipList[level] = _.sortBy(row.equipList[level], 'shipLv');
								row.equipList[level].reverse();
							}
						}
						results.push(React.createElement(ItemInfoTable, {
							"key": index,
							"slotItemType": row.slotItemType,
							"sumNum": row.sumNum,
							"restNum": row.sumNum - row.useNum,
							"equipList": row.equipList,
							"levelList": row.levelList,
							"itemPngIndex": row.itemPngIndex
						}));
					}
					return results;
				}
			}).call(this))))));
		}
	});

	return ItemInfoTableArea;

})();
