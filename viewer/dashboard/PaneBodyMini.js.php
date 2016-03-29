(function() {
	var $, $$, $shipTypes, $ships, Alert, Button, ButtonGroup, FontAwesome, Label, Overlay, OverlayTrigger, PaneBodyMini, Panel, Popover, ProgressBar, ROOT, React, ReactBootstrap, SlotitemIcon, Slotitems, StatusLabel, Tooltip, TopAlert, _, _ships, getFontStyle, getHpStyle, getMaterialStyle, getShipStatus, getStatusStyle, join, path, ref, ref1, relative, toggleModal,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

	_ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, ROOT = window.ROOT, FontAwesome = window.FontAwesome, toggleModal = window.toggleModal;

	$ships = window.$ships, $shipTypes = window.$shipTypes, _ships = window._ships;

	SlotitemIcon = window.CommonIcon.SlotitemIcon, TopAlert = window.TopAlert, StatusLabel = window.StatusLabel;

	Button = ReactBootstrap.Button, ButtonGroup = ReactBootstrap.ButtonGroup;

	ProgressBar = ReactBootstrap.ProgressBar, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Alert = ReactBootstrap.Alert, Overlay = ReactBootstrap.Overlay, Label = ReactBootstrap.Label, Panel = ReactBootstrap.Panel, Popover = ReactBootstrap.Popover;

	getHpStyle = function(percent) {
		if (percent <= 25) {
			return 'danger';
		} else if (percent <= 50) {
			return 'warning';
		} else if (percent <= 75) {
			return 'info';
		} else {
			return 'success';
		}
	};

	getMaterialStyle = function(percent) {
		if (percent <= 50) {
			return 'danger';
		} else if (percent <= 75) {
			return 'info';
		} else if (percent < 100) {
			return 'info';
		} else {
			return 'success';
		}
	};

	getStatusStyle = function(status) {
		var flag;
		if (status != null) {
			flag = status === 0 || status === 1;
			if ((flag != null) && flag) {
				return {
					opacity: 0.4
				};
			}
		} else {
			return {};
		}
	};

	getShipStatus = function(shipId) {
		var status;
		status = -1;
		if (indexOf.call(_ndocks, shipId) >= 0) {
			return status = 1;
		} else if ((Math.min(_ships[shipId].api_fuel / _ships[shipId].api_fuel_max * 100, _ships[shipId].api_bull / _ships[shipId].api_bull_max * 100)) < 100) {
			return status = 6;
		} else if (_ships[shipId].api_sally_area === 1) {
			return status = 2;
		} else if (_ships[shipId].api_sally_area === 2) {
			return status = 3;
		} else if (_ships[shipId].api_sally_area === 3) {
			return status = 4;
		} else if (_ships[shipId].api_sally_area === 4) {
			return status = 5;
		}
		return status;
	};

	getFontStyle = function(theme) {
		if (window.isDarkTheme) {
			return {
				color: '#FFF'
			};
		} else {
			return {
				color: '#000'
			};
		}
	};

	Slotitems = React.createClass({displayName: "Slotitems",
		render: function() {
			var $slotitems, _slotitems, i, item, itemId;
			return React.createElement("div", {
				"className": "slotitems-mini",
				"style": {
					display: "flex",
					flexFlow: "column"
				}
			}, ((function() {
				var k, len, ref2, ref3, results;
				$slotitems = window.$slotitems, _slotitems = window._slotitems;
				ref2 = this.props.data;
				results = [];
				for (i = k = 0, len = ref2.length; k < len; i = ++k) {
					itemId = ref2[i];
					if (itemId === -1) {
						continue;
					}
					item = _slotitems[itemId];
					results.push(React.createElement("div", {
						"key": i,
						"className": "slotitem-container-mini"
					}, React.createElement(SlotitemIcon, {
						"key": itemId,
						"className": 'slotitem-img',
						"slotitemId": item.api_type[3]
					}), React.createElement("span", {
						"className": "slotitem-name-mini"
					}, item.api_name, (item.api_level > 0 ? React.createElement("strong", {
						"style": {
							color: '#45A9A5'
						}
					}, " ★", item.api_level) : ''), "                ", ((item.api_alv != null) && (1 <= (ref3 = item.api_alv) && ref3 <= 7) ? React.createElement("img", {
						"className": 'alv-img',
						"src": 'assets/img/airplane/alv' + item.api_alv + ".png"
					}) : '')), React.createElement(Label, {
						"className": "slotitem-onslot-mini " + ((item.api_type[3] >= 6 && item.api_type[3] <= 10) || (item.api_type[3] >= 21 && item.api_type[3] <= 22) || item.api_type[3] === 33 ? 'show' : 'hide'),
						"bsStyle": "" + (this.props.onslot[i] < this.props.maxeq[i] ? 'warning' : 'default')
					}, this.props.onslot[i])));
				}
				return results;
			}).call(this)));
		}
	});

	PaneBodyMini = React.createClass({displayName: "PaneBodyMini",
		condDynamicUpdateFlag: false,
		getInitialState: function() {
			return {
				cond: [0, 0, 0, 0, 0, 0],
				label: [-1, -1, -1, -1, -1, -1]
			};
		},
		updateLabels: function() {
			var j, k, label, len, ref2, ship, shipId, status;
			label = this.state.label;
			ref2 = this.props.deck.api_ship;
			for (j = k = 0, len = ref2.length; k < len; j = ++k) {
				shipId = ref2[j];
				if (shipId === -1) {
					continue;
				}
				ship = _ships[shipId];
				status = getShipStatus(shipId);
				label[j] = status;
			}
			return label;
		},
		onCondChange: function(cond) {
			var condDynamicUpdateFlag;
			condDynamicUpdateFlag = true;
			return this.setState({
				cond: cond
			});
		},
		handleResponse: function(e) {
			var body, i, k, label, len, method, postBody, ref2, shipId, updateflag;
			ref2 = e.detail, method = ref2.method, path = ref2.path, body = ref2.body, postBody = ref2.postBody;
			label = this.state.label;
			updateflag = false;
			switch (path) {
				case '/kcsapi/api_port/port':
				case '/kcsapi/api_req_hensei/change':
				case '/kcsapi/api_req_hokyu/charge':
				case '/kcsapi/api_req_map/next':
				case '/kcsapi/api_get_member/ship3':
				case '/kcsapi/api_req_nyukyo/speedchange':
				case '/kcsapi/api_req_hensei/preset_select':
					updateflag = true;
					label = this.updateLabels();
					break;
				case '/kcsapi/api_req_nyukyo/start':
					if (postBody.api_highspeed === 1) {
						updateflag = true;
					}
					break;
				case '/kcsapi/api_get_member/ndock':
					for (k = 0, len = _ndocks.length; k < len; k++) {
						shipId = _ndocks[k];
						i = this.props.deck.api_ship.indexOf(shipId);
						if (i !== -1) {
							label[i] = 1;
							updateflag = true;
						}
					}
			}
			if (updateflag) {
				return this.setState({
					label: label
				});
			}
		},
		shouldComponentUpdate: function(nextProps, nextState) {
			return nextProps.activeDeck === this.props.deckIndex && nextProps.show;
		},
		componentWillReceiveProps: function(nextProps) {
			var cond, j, k, len, ref2, ship, shipId;
			_ships = window._ships;
			if (this.condDynamicUpdateFlag) {
				return this.condDynamicUpdateFlag = !this.condDynamicUpdateFlag;
			} else {
				cond = [0, 0, 0, 0, 0, 0];
				ref2 = nextProps.deck.api_ship;
				for (j = k = 0, len = ref2.length; k < len; j = ++k) {
					shipId = ref2[j];
					if (shipId === -1) {
						cond[j] = 49;
						continue;
					}
					ship = _ships[shipId];
					cond[j] = ship.api_cond;
				}
				return this.setState({
					cond: cond
				});
			}
		},
		componentWillMount: function() {
			var cond, j, k, len, ref2, ship, shipId;
			$ships = window.$ships, $shipTypes = window.$shipTypes, _ships = window._ships;
			cond = [0, 0, 0, 0, 0, 0];
			ref2 = this.props.deck.api_ship;
			for (j = k = 0, len = ref2.length; k < len; j = ++k) {
				shipId = ref2[j];
				if (shipId === -1) {
					cond[j] = 49;
					continue;
				}
				ship = _ships[shipId];
				cond[j] = ship.api_cond;
			}
			return this.setState({
				cond: cond
			});
		},
		componentDidMount: function() {
			var label;
			window.addEventListener('game.response', this.handleResponse);
			label = this.updateLabels();
			return this.setState({
				label: label
			});
		},
		render: function() {
			var j, ship, shipId, shipInfo, shipType;
			return React.createElement("div", null, React.createElement("div", {
				"className": "fleet-name"
			}, React.createElement(TopAlert, {
				"updateCond": this.onCondChange,
				"messages": this.props.messages,
				"deckIndex": this.props.deckIndex,
				"deckName": this.props.deckName,
				"mini": true
			})), React.createElement("div", {
				"className": "ship-details-mini"
			}, ((function() {
				var k, len, ref2, results;
				$ships = window.$ships, $shipTypes = window.$shipTypes, _ships = window._ships;
				ref2 = this.props.deck.api_ship;
				results = [];
				for (j = k = 0, len = ref2.length; k < len; j = ++k) {
					shipId = ref2[j];
					if (shipId === -1) {
						continue;
					}
					ship = _ships[shipId];
					shipInfo = $ships[ship.api_ship_id];
					shipType = $shipTypes[shipInfo.api_stype].api_name;
					results.push(React.createElement("div", {
						"key": j,
						"className": "ship-tile"
					}, React.createElement(OverlayTrigger, {
						"placement": ((!window.doubleTabbed) && (window.layout === 'vertical') ? 'left' : 'right'),
						"overlay": React.createElement(Tooltip, {
							"id": "ship-pop-" + this.props.key + "-" + j,
							"className": "ship-pop " + (ship.api_slot[0] > 0 || ship.api_slot_ex > 0 ? '' : 'hidden')
						}, React.createElement("div", {
							"className": "item-name"
						}, React.createElement(Slotitems, {
							"data": ship.api_slot.concat(ship.api_slot_ex || -1),
							"onslot": ship.api_onslot,
							"maxeq": ship.api_maxeq
						})))
					}, React.createElement("div", {
						"className": "ship-item"
					}, React.createElement(OverlayTrigger, {
						"placement": 'top',
						"overlay": React.createElement(Tooltip, {
							"id": "miniship-exp-" + this.props.key + "-" + j
						}, "Next. ", ship.api_exp[1])
					}, React.createElement("div", {
						"className": "ship-info"
					}, React.createElement("span", {
						"className": "ship-name",
						"style": getStatusStyle(this.state.label[j])
					}, shipInfo.api_name), React.createElement("span", {
						"className": "ship-lv-text top-space",
						"style": getStatusStyle(this.state.label[j])
					}, "Lv. ", ship.api_lv))), React.createElement("div", {
						"className": "ship-stat"
					}, React.createElement("div", {
						"className": "div-row"
					}, React.createElement("span", {
						"className": "ship-hp",
						"style": getStatusStyle(this.state.label[j])
					}, ship.api_nowhp, " \x2F ", ship.api_maxhp), React.createElement("div", {
						"className": "status-label"
					}, React.createElement(StatusLabel, {
						"label": this.state.label[j]
					})), React.createElement("div", {
						"style": getStatusStyle(this.state.label[j])
					}, React.createElement("span", {
						"className": "ship-cond " + window.getCondStyle(ship.api_cond)
					}, "★", ship.api_cond))), React.createElement("span", {
						"className": "hp-progress top-space",
						"style": getStatusStyle(this.state.label[j])
					}, React.createElement(ProgressBar, {
						"bsStyle": getHpStyle(ship.api_nowhp / ship.api_maxhp * 100),
						"now": ship.api_nowhp / ship.api_maxhp * 100
					})))))));
				}
				return results;
			}).call(this))));
		}
	});

	return PaneBodyMini;

})();
