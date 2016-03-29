<?php
header("Content-Type: application/json;charset=utf-8");
require_once 'agectrl.php';
tryModified("expedition.json");
?>

var Col, FontAwesome, Grid, ListGroup, ListGroupItem, OverlayTrigger, Panel, React, ReactBootstrap, Row, Tab, Tabs, Tooltip, _, getMaterialImage, itemNames, layout,
	indexOf = [].indexOf || function(item) {
		for (var i = 0, l = this.length; i < l; i++) {
			if (i in this && this[i] === item) return i;
		}
		return -1;
	};

var React = window.React, ReactBootstrap = window.ReactBootstrap, FontAwesome = window.FontAwesome, layout = window.layout;

var Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row, Col = ReactBootstrap.Col, Tabs = ReactBootstrap.Tabs, Tab = ReactBootstrap.Tab, ListGroup = ReactBootstrap.ListGroup, ListGroupItem = ReactBootstrap.ListGroupItem, Panel = ReactBootstrap.Panel, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

var itemNames = ["", "桶", "喷火枪", "紫菜", "家具箱(小)", "家具箱(中)", "家具箱(大)"];

var getMaterialImage = function(idx) {
	return "assets/img/material/0" + idx + ".png";
};

Expedition = {
	reactClass: React.createClass({
		displayName: "reactClass",
		getInitialState: function() {
			var all_status, expedition, expeditions, j, json, len;
			all_status = [];
			json = <?php echo file_get_contents("expedition.json");?>;
			expeditions = [];
			for (j = 0, len = json.length; j < len; j++) {
				expedition = json[j];
				expeditions[expedition.id] = expedition;
			}
			return {
				expedition_id: 0,
				expeditions: expeditions,
				expedition_information: [],
				expedition_constraints: [],
				fleet_status: [false, false, false],
				fleet_reward: [
					[0, 0, 0, 0],
					[0, 0, 0, 0],
					[0, 0, 0, 0]
				],
				fleet_reward_hour: [
					[0, 0, 0, 0],
					[0, 0, 0, 0],
					[0, 0, 0, 0]
				],
				fleet_reward_big: [
					[0, 0, 0, 0],
					[0, 0, 0, 0],
					[0, 0, 0, 0]
				],
				fleet_reward_hour_big: [
					[0, 0, 0, 0],
					[0, 0, 0, 0],
					[0, 0, 0, 0]
				]
			};
		},
		checkFlagshipLv: function(deck_id, flagship_lv, decks, ships) {
			var _flagship_lv, flagship_id, fleet;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			flagship_id = fleet.api_ship[0];
			if (flagship_id !== -1) {
				_flagship_lv = ships[flagship_id].api_lv;
				if (_flagship_lv >= flagship_lv) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},
		checkFleetLv: function(deck_id, fleet_lv, decks, ships) {
			var _fleet_lv, fleet, j, len, ref, ship_id, ship_lv;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_fleet_lv = 0;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (!(ship_id !== -1)) {
					continue;
				}
				ship_lv = ships[ship_id].api_lv;
				_fleet_lv += ship_lv;
			}
			if (_fleet_lv >= fleet_lv) {
				return true;
			} else {
				return false;
			}
		},
		checkFlagshipShiptype: function(deck_id, flagship_shiptype, decks, ships, Ships) {
			var _flagship_shiptype, flagship_id, flagship_shipid, fleet;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			flagship_id = fleet.api_ship[0];
			if (flagship_id !== -1) {
				flagship_shipid = ships[flagship_id].api_ship_id;
				_flagship_shiptype = Ships[flagship_shipid].api_stype;
				if (_flagship_shiptype === flagship_shiptype) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},
		checkShipCount: function(deck_id, ship_count, decks) {
			var _ship_count, fleet, j, len, ref, ship_id;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_ship_count = 0;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (ship_id !== -1) {
					_ship_count += 1;
				}
			}
			if (_ship_count >= ship_count) {
				return true;
			} else {
				return false;
			}
		},
		checkDrumShipCount: function(deck_id, drum_ship_count, decks, ships, slotitems) {
			var _drum_ship_count, fleet, j, k, len, len1, ref, ref1, ship_id, slotitem_id, slotitem_slotitemid;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_drum_ship_count = 0;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (ship_id !== -1) {
					ref1 = ships[ship_id].api_slot;
					for (k = 0, len1 = ref1.length; k < len1; k++) {
						slotitem_id = ref1[k];
						if (!(slotitem_id !== -1)) {
							continue;
						}
						slotitem_slotitemid = slotitems[slotitem_id].api_slotitem_id;
						if (slotitem_slotitemid === 75) {
							_drum_ship_count += 1;
							break;
						}
					}
				}
			}
			if (_drum_ship_count >= drum_ship_count) {
				return true;
			} else {
				return false;
			}
		},
		checkDrumCount: function(deck_id, drum_count, decks, ships, slotitems) {
			var _drum_count, fleet, j, k, len, len1, ref, ref1, ship_id, slotitem_id, slotitem_slotitemid;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_drum_count = 0;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (ship_id !== -1) {
					ref1 = ships[ship_id].api_slot;
					for (k = 0, len1 = ref1.length; k < len1; k++) {
						slotitem_id = ref1[k];
						if (!(slotitem_id !== -1)) {
							continue;
						}
						slotitem_slotitemid = slotitems[slotitem_id].api_slotitem_id;
						if (slotitem_slotitemid === 75) {
							_drum_count += 1;
						}
					}
				}
			}
			if (_drum_count >= drum_count) {
				return true;
			} else {
				return false;
			}
		},
		checkRequiredShiptype: function(deck_id, required_shiptype, decks, ships, Ships) {
			var _required_shiptype_count, fleet, j, len, ref, ship_id, ship_shipid, ship_shiptype;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_required_shiptype_count = 0;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (!(ship_id !== -1)) {
					continue;
				}
				ship_shipid = ships[ship_id].api_ship_id;
				ship_shiptype = Ships[ship_shipid].api_stype;
				if (indexOf.call(required_shiptype.shiptype, ship_shiptype) >= 0) {
					_required_shiptype_count += 1;
				}
			}
			if (_required_shiptype_count >= required_shiptype.count) {
				return true;
			} else {
				return false;
			}
		},
		checkSupply: function(deck_id, decks, ships) {
			var _supply_ok, fleet, j, len, ref, ship_bull, ship_bull_max, ship_fuel, ship_fuel_max, ship_id;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_supply_ok = true;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (!(ship_id !== -1)) {
					continue;
				}
				ship_fuel = ships[ship_id].api_fuel;
				ship_fuel_max = ships[ship_id].api_fuel_max;
				ship_bull = ships[ship_id].api_bull;
				ship_bull_max = ships[ship_id].api_bull_max;
				if (ship_fuel < ship_fuel_max || ship_bull < ship_bull_max) {
					_supply_ok = false;
					break;
				}
			}
			return _supply_ok;
		},
		checkCondition: function(deck_id, decks, ships) {
			var _condition_ok, fleet, j, len, ref, ship_cond, ship_id;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			_condition_ok = true;
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (!(ship_id !== -1)) {
					continue;
				}
				ship_cond = ships[ship_id].api_cond;
				if (ship_cond < 30) {
					_condition_ok = false;
					break;
				}
			}
			return _condition_ok;
		},
		checkFlagshipHp: function(deck_id, decks, ships) {
			var flagship_hp, flagship_id, flagship_maxhp, fleet;
			fleet = decks[deck_id];
			if (fleet == null) {
				return false;
			}
			flagship_id = fleet.api_ship[0];
			if (flagship_id !== -1) {
				flagship_hp = ships[flagship_id].api_nowhp;
				flagship_maxhp = ships[flagship_id].api_maxhp;
				if (flagship_hp / flagship_maxhp > 0.25) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		},
		examineConstraints: function(exp_id, deck_id) {
			var $ships, _decks, _ships, _slotitems, expedition, j, len, ref, required_shiptype, status;
			$ships = window.$ships, _decks = window._decks, _ships = window._ships, _slotitems = window._slotitems;
			if (exp_id === 0) {
				return false;
			}
			if (!(($ships != null) && (_decks != null) && (_ships != null) && (_slotitems != null))) {
				return false;
			}
			expedition = this.state.expeditions[exp_id];
			status = true;
			if (expedition != null) {
				if (expedition.flagship_lv !== 0) {
					status &= this.checkFlagshipLv(deck_id, expedition.flagship_lv, _decks, _ships);
				}
				if (expedition.fleet_lv !== 0) {
					status &= this.checkFleetLv(deck_id, expedition.fleet_lv, _decks, _ships);
				}
				if (expedition.flagship_shiptype !== 0) {
					status &= this.checkFlagshipShiptype(deck_id, expedition.flagship_shiptype, _decks, _ships, $ships);
				}
				if (expedition.ship_count !== 0) {
					status &= this.checkShipCount(deck_id, expedition.ship_count, _decks);
				}
				if (expedition.drum_ship_count !== 0) {
					status &= this.checkDrumShipCount(deck_id, expedition.drum_ship_count, _decks, _ships, _slotitems);
				}
				if (expedition.drum_count !== 0) {
					status &= this.checkDrumCount(deck_id, expedition.drum_count, _decks, _ships, _slotitems);
				}
				if (expedition.required_shiptypes.length !== 0) {
					ref = expedition.required_shiptypes;
					for (j = 0, len = ref.length; j < len; j++) {
						required_shiptype = ref[j];
						status &= this.checkRequiredShiptype(deck_id, required_shiptype, _decks, _ships, $ships);
					}
				}
			}
			status &= this.checkSupply(deck_id, _decks, _ships);
			status &= this.checkCondition(deck_id, _decks, _ships);
			status &= this.checkFlagshipHp(deck_id, _decks, _ships);
			return status;
		},
		getMaxSupply: function(deck_id, decks, ships) {
			var _max_supply, fleet, j, len, ref, ship_bull_max, ship_fuel_max, ship_id;
			_max_supply = [0, 0];
			fleet = decks[deck_id];
			if (fleet == null) {
				return _max_supply;
			}
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (!(ship_id !== -1)) {
					continue;
				}
				ship_fuel_max = ships[ship_id].api_fuel_max;
				ship_bull_max = ships[ship_id].api_bull_max;
				_max_supply[0] += ship_fuel_max;
				_max_supply[1] += ship_bull_max;
			}
			return _max_supply;
		},
		getDaihatsuBonus: function(deck_id, decks, ships, slotitems) {
			var _daihatsu_count, fleet, j, k, len, len1, ref, ref1, ship_id, slotitem_id, slotitem_slotitemid;
			_daihatsu_count = 0;
			fleet = decks[deck_id];
			if (fleet == null) {
				return _daihatsu_count;
			}
			ref = fleet.api_ship;
			for (j = 0, len = ref.length; j < len; j++) {
				ship_id = ref[j];
				if (ship_id !== -1) {
					ref1 = ships[ship_id].api_slot;
					for (k = 0, len1 = ref1.length; k < len1; k++) {
						slotitem_id = ref1[k];
						if (!(slotitem_id !== -1)) {
							continue;
						}
						slotitem_slotitemid = slotitems[slotitem_id].api_slotitem_id;
						if (slotitem_slotitemid === 68) {
							_daihatsu_count += 1;
						}
					}
				}
			}
			if (_daihatsu_count > 4) {
				_daihatsu_count = 4;
			}
			return 1 + _daihatsu_count * 0.05;
		},
		calculateReward: function(exp_id, deck_id, deck_status) {
			var $missions, _decks, _ships, _slotitems, actual_reward, coeff, expedition, i, inv_time, j, k, max_supply, mission, reward;
			reward = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			$missions = window.$missions, _decks = window._decks, _ships = window._ships, _slotitems = window._slotitems;
			if (exp_id === 0) {
				return reward;
			}
			if (!(($missions != null) && (_decks != null) && (_ships != null) && (_slotitems != null))) {
				return reward;
			}
			mission = $missions[exp_id];
			expedition = this.state.expeditions[exp_id];
			if (!((mission != null) && (expedition != null))) {
				return reward;
			}
			max_supply = this.getMaxSupply(deck_id, _decks, _ships);
			coeff = this.getDaihatsuBonus(deck_id, _decks, _ships, _slotitems);
			if (!deck_status) {
				coeff = 0;
			}
			actual_reward = [0, 0, 0, 0];
			actual_reward[0] = expedition.reward_fuel * coeff - mission.api_use_fuel * max_supply[0];
			actual_reward[1] = expedition.reward_bullet * coeff - mission.api_use_bull * max_supply[1];
			actual_reward[2] = expedition.reward_steel * coeff;
			actual_reward[3] = expedition.reward_alum * coeff;
			inv_time = 60 / mission.api_time;
			for (i = j = 0; j < 4; i = ++j) {
				reward[0][i] = Math.floor(actual_reward[i]);
				reward[1][i] = Math.floor(reward[0][i] * inv_time);
			}
			coeff *= 1.5;
			actual_reward[0] = expedition.reward_fuel * coeff - mission.api_use_fuel * max_supply[0];
			actual_reward[1] = expedition.reward_bullet * coeff - mission.api_use_bull * max_supply[1];
			actual_reward[2] = expedition.reward_steel * coeff;
			actual_reward[3] = expedition.reward_alum * coeff;
			for (i = k = 0; k < 4; i = ++k) {
				reward[2][i] = Math.floor(actual_reward[i]);
				reward[3][i] = Math.floor(reward[2][i] * inv_time);
			}
			return reward;
		},
		describeConstraints: function(exp_id) {
			var $missions, $shipTypes, constraints, expedition, hours, i, information, j, k, l, len, len1, len2, minutes, mission, ref, ref1, ref2, required_shiptype, reward_item, stype, stype_name;
			$shipTypes = window.$shipTypes, $missions = window.$missions;
			if (exp_id === 0) {
				return {
					information: [],
					constraints: []
				};
			}
			if (!(($shipTypes != null) && ($missions != null))) {
				return {
					information: [],
					constraints: []
				};
			}
			mission = $missions[exp_id];
			expedition = this.state.expeditions[exp_id];
			information = [];
			if (mission != null) {
				hours = Math.floor(mission.api_time / 60);
				minutes = mission.api_time % 60;
				information.push(React.createElement("li", {
					"key": 'time'
				}, "远征时间 ", hours, ":", (minutes < 10 ? "0" + minutes : minutes)));
				information.push(React.createElement("li", {
					"key": 'use_fuel'
				}, "消费燃料 ", mission.api_use_fuel * 100, "%"));
				information.push(React.createElement("li", {
					"key": 'use_bull'
				}, "消费弹药 ", mission.api_use_bull * 100, "%"));
				if (expedition != null) {
					if (expedition.reward_fuel !== 0) {
						information.push(React.createElement("li", {
							"key": 'reward_fuel'
						}, React.createElement(OverlayTrigger, {
							"placement": 'right',
							"overlay": React.createElement(Tooltip, {
								"id": 'fuel-per-hour'
							}, "获得燃料 ", Math.round(expedition.reward_fuel * 60 / mission.api_time), " \x2F 小时")
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, "获得燃料 ", expedition.reward_fuel))));
					}
					if (expedition.reward_bullet !== 0) {
						information.push(React.createElement("li", {
							"key": 'reward_bullet'
						}, React.createElement(OverlayTrigger, {
							"placement": 'right',
							"overlay": React.createElement(Tooltip, {
								"id": 'bull-per-hour'
							}, "获得弹药 ", Math.round(expedition.reward_bullet * 60 / mission.api_time), " \x2F 小时")
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, "获得弹药 ", expedition.reward_bullet))));
					}
					if (expedition.reward_steel !== 0) {
						information.push(React.createElement("li", {
							"key": 'reward_steel'
						}, React.createElement(OverlayTrigger, {
							"placement": 'right',
							"overlay": React.createElement(Tooltip, {
								"id": 'steel-per-hour'
							}, "获得钢材 ", Math.round(expedition.reward_steel * 60 / mission.api_time), " \x2F 小时")
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, "获得钢材 ", expedition.reward_steel))));
					}
					if (expedition.reward_alum !== 0) {
						information.push(React.createElement("li", {
							"key": 'reward_alum'
						}, React.createElement(OverlayTrigger, {
							"placement": 'right',
							"overlay": React.createElement(Tooltip, {
								"id": 'bauxite-per-hour'
							}, "获得铝土 ", Math.round(expedition.reward_alum * 60 / mission.api_time), " \x2F 小时")
						}, React.createElement("div", {
							"className": 'tooltipTrigger'
						}, "获得铝土 ", expedition.reward_alum))));
					}
					if (expedition.reward_items.length !== 0) {
						ref = expedition.reward_items;
						for (i = j = 0, len = ref.length; j < len; i = ++j) {
							reward_item = ref[i];
							information.push(React.createElement("li", {
								"key": "reward_items_" + i
							}, itemNames[reward_item.itemtype], " 0~", reward_item.max_number, " 个"));
						}
					}
				}
			}
			constraints = [];
			if (expedition != null) {
				if (expedition.flagship_lv !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'flagship_lv'
					}, "旗舰等级 Lv. ", expedition.flagship_lv));
				}
				if (expedition.fleet_lv !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'fleet_lv'
					}, "舰队等级合计 Lv. ", expedition.fleet_lv));
				}
				if (expedition.flagship_shiptype !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'flagship_shiptype'
					}, "旗舰舰种 ", $shipTypes[expedition.flagship_shiptype].api_name));
				}
				if (expedition.ship_count !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'ship_count'
					}, "总舰数 ", expedition.ship_count, " 只"));
				}
				if (expedition.drum_ship_count !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'drum_ship_count'
					}, "装备缶的舰数 ", expedition.drum_ship_count, " 只"));
				}
				if (expedition.drum_count !== 0) {
					constraints.push(React.createElement("li", {
						"key": 'drum_count'
					}, "装备的缶个数 ", expedition.drum_count, " 个"));
				}
				if (expedition.required_shiptypes.length !== 0) {
					ref1 = expedition.required_shiptypes;
					for (i = k = 0, len1 = ref1.length; k < len1; i = ++k) {
						required_shiptype = ref1[i];
						stype_name = $shipTypes[required_shiptype.shiptype[0]].api_name;
						if (required_shiptype.shiptype.length > 1) {
							ref2 = required_shiptype.shiptype.slice(1);
							for (l = 0, len2 = ref2.length; l < len2; l++) {
								stype = ref2[l];
								stype_name = stype_name + " 或 " + $shipTypes[stype].api_name;
							}
						}
						constraints.push(React.createElement("li", {
							"key": "required_shiptypes_" + i
						}, stype_name, " ", required_shiptype.count, " 只"));
					}
				}
				if (expedition.big_success != null) {
					constraints.push(React.createElement("li", {
						"key": 'big_success'
					}, "特殊大成功条件: ", expedition.big_success));
				}
			}
			return {
				information: information,
				constraints: constraints
			};
		},
		getAllStatus: function() {
			var all_status, i, j, k, len, mission, status;
			all_status = [];
			status = [];
			for (j = 0, len = $missions.length; j < len; j++) {
				mission = $missions[j];
				if (!(mission != null)) {
					continue;
				}
				for (i = k = 1; k <= 3; i = ++k) {
					status[i - 1] = this.examineConstraints(mission.api_id, i);
				}
				all_status[mission.api_id] = Object.clone(status);
			}
			return this.setState({
				all_status: all_status
			});
		},
		handleStatChange: function(exp_id) {
			var all_status, deck_id, j, ret_reward, ret_status, reward, reward_big, reward_hour, reward_hour_big, status;
			all_status = [];
			status = [false, false, false];
			reward = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			reward_hour = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			reward_big = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			reward_hour_big = [
				[0, 0, 0, 0],
				[0, 0, 0, 0],
				[0, 0, 0, 0]
			];
			for (deck_id = j = 1; j <= 3; deck_id = ++j) {
				ret_status = this.examineConstraints(exp_id, deck_id);
				status[deck_id - 1] = ret_status;
				ret_reward = this.calculateReward(exp_id, deck_id, ret_status);
				reward[deck_id - 1] = ret_reward[0];
				reward_hour[deck_id - 1] = ret_reward[1];
				reward_big[deck_id - 1] = ret_reward[2];
				reward_hour_big[deck_id - 1] = ret_reward[3];
			}
			this.getAllStatus();
			return this.setState({
				fleet_status: status,
				fleet_reward: reward,
				fleet_reward_hour: reward_hour,
				fleet_reward_big: reward_big,
				fleet_reward_hour_big: reward_hour_big
			});
		},
		handleInfoChange: function(exp_id) {
			var constraints, information, ref;
			ref = this.describeConstraints(exp_id), information = ref.information, constraints = ref.constraints;
			return this.setState({
				expedition_information: information,
				expedition_constraints: constraints
			});
		},
		handleExpeditionSelect: function(exp_id) {
			this.handleStatChange(exp_id);
			this.handleInfoChange(exp_id);
			return this.setState({
				expedition_id: exp_id
			});
		},
		handleResponse: function(e) {
			var $missions, body, deck_id, exp_id, method, path, postBody, ref, status;
			ref = e.detail, method = ref.method, path = ref.path, body = ref.body, postBody = ref.postBody;
			switch (path) {
				case '/kcsapi/api_port/port':
				case '/kcsapi/api_req_hensei/change':
				case '/kcsapi/api_req_kaisou/slotset':
				case '/kcsapi/api_req_hokyu/charge':
				case '/kcsapi/api_get_member/ndock':
					return this.handleStatChange(this.state.expedition_id);
				case '/kcsapi/api_req_mission/start':
					$missions = window.$missions;
					deck_id = postBody.api_deck_id - 1;
					exp_id = postBody.api_mission_id;
					status = this.examineConstraints(exp_id, deck_id);
					if (!status) {
						return toggleModal('远征注意！', "第 " + (deck_id + 1) + " 舰队远征 " + $missions[exp_id].api_name + " 不满足成功条件，请及时召回！");
					}
			}
		},
		componentDidMount: function() {
			return window.addEventListener("game.response", this.handleResponse);
		},
		render: function() {
			var $mapareas, $missions, i, map_missions, maparea, mission;
			return React.createElement("div", null, React.createElement("link", {
				"rel": 'stylesheet',
				"href": "assets/css/expedition.css"
			}), React.createElement(Grid, null, React.createElement(Row, null, React.createElement(Col, {
				"xs": 12
			}, React.createElement(Tabs, {
				"defaultActiveKey": 1.,
				"animation": false,
				"bsStyle": 'pills',
				"className": 'areaTabs'
			}, ((function() {
				var j, len, results;
				$mapareas = window.$mapareas, $missions = window.$missions;
				if ($mapareas != null) {
					results = [];
					for (j = 0, len = $mapareas.length; j < len; j++) {
						maparea = $mapareas[j];
						if (!(maparea != null)) {
							continue;
						}
						map_missions = (function() {
							var k, len1, results1;
							results1 = [];
							for (k = 0, len1 = $missions.length; k < len1; k++) {
								mission = $missions[k];
								if ((mission != null) && mission.api_maparea_id === maparea.api_id) {
									results1.push(mission);
								}
							}
							return results1;
						})();
						if (map_missions.length === 0) {
							continue;
						}
						results.push(React.createElement(Tab, {
							"eventKey": maparea.api_id,
							"key": maparea.api_id,
							"title": maparea.api_name
						}, React.createElement("table", {
							"width": '100%',
							"className": 'expItems'
						}, React.createElement("tbody", null, React.createElement("tr", null, React.createElement("td", null, (function() {
							var k, len1, ref, results1;
							ref = map_missions.slice(0, 4);
							results1 = [];
							for (k = 0, len1 = ref.length; k < len1; k++) {
								mission = ref[k];
								results1.push(React.createElement(ListGroupItem, {
									"key": mission.api_id,
									"className": (mission.api_id === this.state.expedition_id ? "active" : ""),
									"style": {
										display: "flex",
										flexFlow: "row nowrap",
										justifyContent: "space-between"
									},
									"onClick": this.handleExpeditionSelect.bind(this, mission.api_id)
								}, React.createElement("span", {
									"style": {
										marginRight: "auto",
										overflow: "hidden",
										textOverflow: "ellipsis",
										whiteSpace: "nowrap",
										marginRight: 10
									}
								}, mission.api_id, " ", mission.api_name), React.createElement("span", {
									"style": {
										flex: "none",
										display: "flex",
										alignItems: "center",
										width: 30,
										justifyContent: "space-between"
									}
								}, (function() {
									var l, results2;
									results2 = [];
									for (i = l = 0; l < 3; i = ++l) {
										results2.push(React.createElement("span", {
											"key": i
										}, ((this.state.all_status != null) && this.state.all_status[mission.api_id][i] ? React.createElement("span", {
											"className": 'deckIndicator',
											"style": {
												backgroundColor: "#0F0"
											}
										}) : React.createElement("span", {
											"className": 'deckIndicator',
											"style": {
												backgroundColor: "#F00"
											}
										}))));
									}
									return results2;
								}).call(this))));
							}
							return results1;
						}).call(this)), React.createElement("td", null, (function() {
							var k, len1, ref, results1;
							ref = map_missions.slice(4, 8);
							results1 = [];
							for (k = 0, len1 = ref.length; k < len1; k++) {
								mission = ref[k];
								results1.push(React.createElement(ListGroupItem, {
									"key": mission.api_id,
									"className": (mission.api_id === this.state.expedition_id ? "active" : ""),
									"style": {
										display: "flex",
										flexFlow: "row nowrap",
										justifyContent: "space-between"
									},
									"onClick": this.handleExpeditionSelect.bind(this, mission.api_id)
								}, React.createElement("span", {
									"style": {
										marginRight: "auto",
										overflow: "hidden",
										textOverflow: "ellipsis",
										whiteSpace: "nowrap",
										marginRight: 10
									}
								}, mission.api_id, " ", mission.api_name), React.createElement("span", {
									"style": {
										flex: "none",
										display: "flex",
										alignItems: "center",
										width: 30,
										justifyContent: "space-between"
									}
								}, (function() {
									var l, results2;
									results2 = [];
									for (i = l = 0; l < 3; i = ++l) {
										results2.push(React.createElement("span", {
											"key": i
										}, ((this.state.all_status != null) && this.state.all_status[mission.api_id][i] ? React.createElement("span", {
											"className": 'deckIndicator',
											"style": {
												backgroundColor: "#0F0"
											}
										}) : React.createElement("span", {
											"className": 'deckIndicator',
											"style": {
												backgroundColor: "#F00"
											}
										}))));
									}
									return results2;
								}).call(this))));
							}
							return results1;
						}).call(this)))))));
					}
					return results;
				}
			}).call(this))))), React.createElement(Row, null, React.createElement(Col, {
				"xs": 12
			}, React.createElement(Panel, {
				"header": '舰队准备情况',
				"bsStyle": 'default',
				"className": 'fleetPanel'
			}, React.createElement("table", {
				"width": '100%'
			}, React.createElement("tbody", null, React.createElement("tr", null, (function() {
				var j, results;
				results = [];
				for (i = j = 0; j < 3; i = ++j) {
					results.push(React.createElement("td", {
						"key": i,
						"width": '33.3%'
					}, React.createElement(OverlayTrigger, {
						"placement": 'top',
						"overlay": React.createElement(Tooltip, {
							"id": "fleet-" + i + "-resources"
						}, React.createElement("div", null, "远征收益理论值 (时均)"), React.createElement("table", {
							"width": '100%',
							"className": 'materialTable'
						}, React.createElement("tbody", null, React.createElement("tr", null, React.createElement("td", {
							"width": '10%'
						}, React.createElement("img", {
							"src": getMaterialImage(1),
							"className": "material-icon"
						})), React.createElement("td", {
							"width": '40%'
						}, React.createElement("div", null, this.state.fleet_reward[i][0], " (", this.state.fleet_reward_hour[i][0], ")"), React.createElement("div", {
							"className": 'text-success'
						}, this.state.fleet_reward_big[i][0], " (", this.state.fleet_reward_hour_big[i][0], ")")), React.createElement("td", {
							"width": '10%'
						}, React.createElement("img", {
							"src": getMaterialImage(3),
							"className": "material-icon"
						})), React.createElement("td", {
							"width": '40%'
						}, React.createElement("div", null, this.state.fleet_reward[i][2], " (", this.state.fleet_reward_hour[i][2], ")"), React.createElement("div", {
							"className": 'text-success'
						}, this.state.fleet_reward_big[i][2], " (", this.state.fleet_reward_hour_big[i][2], ")"))), React.createElement("tr", null, React.createElement("td", null, React.createElement("img", {
							"src": getMaterialImage(2),
							"className": "material-icon"
						})), React.createElement("td", null, React.createElement("div", null, this.state.fleet_reward[i][1], " (", this.state.fleet_reward_hour[i][1], ")"), React.createElement("div", {
							"className": 'text-success'
						}, this.state.fleet_reward_big[i][1], " (", this.state.fleet_reward_hour_big[i][1], ")")), React.createElement("td", null, React.createElement("img", {
							"src": getMaterialImage(4),
							"className": "material-icon"
						})), React.createElement("td", null, React.createElement("div", null, this.state.fleet_reward[i][3], " (", this.state.fleet_reward_hour[i][3], ")"), React.createElement("div", {
							"className": 'text-success'
						}, this.state.fleet_reward_big[i][3], " (", this.state.fleet_reward_hour_big[i][3], ")"))))))
					}, React.createElement("div", {
						"className": 'tooltipTrigger'
					}, "第", i + 2, "艦隊 ", (this.state.fleet_status[i] ? React.createElement(FontAwesome, {
						"key": i * 2,
						"name": 'check'
					}) : React.createElement(FontAwesome, {
						"key": i * 2 + 1,
						"name": 'close'
					}))))));
				}
				return results;
			}).call(this))))))), React.createElement(Row, null, React.createElement(Col, {
				"xs": 12
			}, React.createElement("div", {
				"className": 'expInfo'
			}, React.createElement(Panel, {
				"header": '远征收支',
				"bsStyle": 'default',
				"className": 'expAward'
			}, React.createElement("ul", null, this.state.expedition_information)), React.createElement(Panel, {
				"header": '必要条件',
				"bsStyle": 'default',
				"className": 'expCond'
			}, React.createElement("ul", null, this.state.expedition_constraints)))))));
		}
	})
};
