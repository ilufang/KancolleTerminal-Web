(function() {
	var $, $$, Alert, Col, Grid, Label, Overlay, OverlayTrigger, PaneBody, ProgressBar, React, ReactBootstrap, Row, Slotitems, StatusLabel, Table, Tooltip, TopAlert, _,  getHpStyle, getMaterialStyle, getShipStatus, getStatusStyle, join, notify, ref, ref1, relative, resolveTime,
		indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

	$ = window.$, $$ = window.$$, _ = window._, React = window.React, ReactBootstrap = window.ReactBootstrap, resolveTime = window.resolveTime, notify = window.notify;

	SlotitemIcon = window.CommonIcon.SlotitemIcon, TopAlert = window.TopAlert, StatusLabel = window.StatusLabel;

	Slotitems = <?php include 'Slotitems.js.php';?>;

	Table = ReactBootstrap.Table, ProgressBar = ReactBootstrap.ProgressBar, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col, Alert = ReactBootstrap.Alert, Row = ReactBootstrap.Row, Overlay = ReactBootstrap.Overlay, Label = ReactBootstrap.Label;

	getMaterialStyle = function(percent) {
		if (percent <= 50) {
			return 'danger';
		} else if (percent <= 75) {
			return 'warning';
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

	getShipStatus = function(shipId, escapeId, towId) {
		var status;
		status = -1;
		if (shipId === escapeId || shipId === towId) {
			return status = 0;
		}
		if (indexOf.call(_ndocks, shipId) >= 0) {
			return status = 1;
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

	PaneBody = React.createClass({displayName: "PaneBody",
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
				status = getShipStatus(shipId, this.props.escapeId, this.props.towId);
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
			var body, i, k, label, len, method, path, postBody, ref2, shipId, updateflag;
			ref2 = e.detail, method = ref2.method, path = ref2.path, body = ref2.body, postBody = ref2.postBody;
			label = this.state.label;
			updateflag = false;
			switch (path) {
				case '/kcsapi/api_port/port':
					updateflag = true;
					label = this.updateLabels();
					break;
				case '/kcsapi/api_req_hensei/change':
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
					break;
				case '/kcsapi/api_req_nyukyo/speedchange':
					updateflag = true;
					label = this.updateLabels();
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
			cond = [0, 0, 0, 0, 0, 0];
			({
				label: [-1, -1, -1, -1, -1, -1]
			});
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
			var $shipTypes, $ships, _ships, j, ship, shipId, shipInfo, shipType;
			return React.createElement("div", null, React.createElement(TopAlert, {
				"updateCond": this.onCondChange,
				"messages": this.props.messages,
				"deckIndex": this.props.deckIndex,
				"deckName": this.props.deckName,
				"mini": false
			}), React.createElement("div", {
				"className": "ship-details"
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
						"className": "ship-item"
					}, React.createElement("div", {
						"className": "ship-tile"
					}, React.createElement("div", {
						"className": "ship-basic-item"
					}, React.createElement("div", {
						"className": "ship-info",
						"style": getStatusStyle(this.state.label[j])
					}, React.createElement("div", {
						"className": "ship-basic"
					}, React.createElement("span", {
						"className": "ship-lv"
					}, "Lv. ", ship.api_lv), React.createElement("span", {
						"className": 'ship-type'
					}, shipType)), React.createElement("span", {
						"className": "ship-name"
					}, shipInfo.api_name), React.createElement("span", {
						"className": "ship-exp"
					}, "Next. ", ship.api_exp[1])), React.createElement("div", {
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
					}, (ship.api_ndock_time ? [
						React.createElement(OverlayTrigger, {
							"key": j,
							"show": ship.api_ndock_time,
							"placement": 'top',
							"overlay": React.createElement(Tooltip, {
								"id": "panebody-repair-time-" + this.props.key + "-" + j
							}, __('Repair Time'), ": ", resolveTime(ship.api_ndock_time / 1000))
						}, React.createElement(ProgressBar, {
							"key": j,
							"bsStyle": getHpStyle(ship.api_nowhp / ship.api_maxhp * 100),
							"now": ship.api_nowhp / ship.api_maxhp * 100
						}))
					] : [
						React.createElement(ProgressBar, {
							"key": j,
							"bsStyle": getHpStyle(ship.api_nowhp / ship.api_maxhp * 100),
							"now": ship.api_nowhp / ship.api_maxhp * 100
						})
					]))))), React.createElement("span", {
						"className": "ship-fb",
						"style": getStatusStyle(this.state.label[j])
					}, React.createElement("span", {
						"style": {
							flex: 1
						}
					}, React.createElement(OverlayTrigger, {
						"placement": 'right',
						"overlay": React.createElement(Tooltip, {
							"id": "panebody-fuel-" + this.props.key + "-" + j
						}, ship.api_fuel, " \x2F ", shipInfo.api_fuel_max)
					}, React.createElement(ProgressBar, {
						"bsStyle": getMaterialStyle(ship.api_fuel / shipInfo.api_fuel_max * 100),
						"now": ship.api_fuel / shipInfo.api_fuel_max * 100
					}))), React.createElement("span", {
						"style": {
							flex: 1
						}
					}, React.createElement(OverlayTrigger, {
						"placement": 'right',
						"overlay": React.createElement(Tooltip, {
							"id": "panebody-bull-" + this.props.key + "-" + j
						}, ship.api_bull, " \x2F ", shipInfo.api_bull_max)
					}, React.createElement(ProgressBar, {
						"bsStyle": getMaterialStyle(ship.api_bull / shipInfo.api_bull_max * 100),
						"now": ship.api_bull / shipInfo.api_bull_max * 100
					})))), React.createElement("div", {
						"className": "ship-slot",
						"style": getStatusStyle(this.state.label[j])
					}, React.createElement(Slotitems, {
						"fleet": this.props.key,
						"key": j,
						"data": ship.api_slot.concat(ship.api_slot_ex || -1),
						"onslot": ship.api_onslot,
						"maxeq": ship.api_maxeq
					}))));
				}
				return results;
			}).call(this))));
		}
	});

	return PaneBody;

}).call(this);
