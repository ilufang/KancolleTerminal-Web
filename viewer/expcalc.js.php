<?php
header("Content-Type: application/json; charset=utf-8");
require_once 'agectrl.php';
tryModified("i18n_expcalc.json");
?>
var $shipTypes, $ships, Alert, Button, Col, DropdownButton, FontAwesome, Grid, Input, MenuItem, ROOT, React, ReactBootstrap, Table, _, __, _ships, exp, expLevel, expMap, expPercent, expType, expValue, getExpInfo, i18n, join, layout, mapRow, path, rankRow, ref, relative, row, shipRow;

var React = window.React, ReactBootstrap = window.ReactBootstrap, FontAwesome = window.FontAwesome, ROOT = window.ROOT, layout = window.layout;

var _ships = window._ships, $ships = window.$ships, $shipTypes = window.$shipTypes;

var Alert = ReactBootstrap.Alert, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col, Input = ReactBootstrap.Input, DropdownButton = ReactBootstrap.DropdownButton, Table = ReactBootstrap.Table, MenuItem = ReactBootstrap.MenuItem, Button = ReactBootstrap.Button;

Object.assign(i18nDB, <?php
echo file_get_contents("i18n_expcalc.json");
?>);

window.addEventListener('layout.change', function(e) {
	layout = e.detail.layout;
	row = layout === 'horizontal' ? 6 : 3;
	shipRow = layout === 'horizontal' ? 12 : 5;
	mapRow = layout === 'horizontal' ? 9 : 5;
	return rankRow = layout === 'horizontal' ? 3 : 2;
});

var row = layout === 'horizontal' ? 6 : 3;

var shipRow = layout === 'horizontal' ? 12 : 5;

var mapRow = layout === 'horizontal' ? 9 : 5;

var rankRow = layout === 'horizontal' ? 3 : 2;

var exp = [0, 0, 100, 300, 600, 1000, 1500, 2100, 2800, 3600, 4500, 5500, 6600, 7800, 9100, 10500, 12000, 13600, 15300, 17100, 19000, 21000, 23100, 25300, 27600, 30000, 32500, 35100, 37800, 40600, 43500, 46500, 49600, 52800, 56100, 59500, 63000, 66600, 70300, 74100, 78000, 82000, 86100, 90300, 94600, 99000, 103500, 108100, 112800, 117600, 122500, 127500, 132700, 138100, 143700, 149500, 155500, 161700, 168100, 174700, 181500, 188500, 195800, 203400, 211300, 219500, 228000, 236800, 245900, 255300, 265000, 275000, 285400, 296200, 307400, 319000, 331000, 343400, 356200, 369400, 383000, 397000, 411500, 426500, 442000, 458000, 474500, 491500, 509000, 527000, 545500, 564500, 584500, 606500, 631500, 661500, 701500, 761500, 851500, 1000000, 1000000, 1010000, 1011000, 1013000, 1016000, 1020000, 1025000, 1031000, 1038000, 1046000, 1055000, 1065000, 1077000, 1091000, 1107000, 1125000, 1145000, 1168000, 1194000, 1223000, 1255000, 1290000, 1329000, 1372000, 1419000, 1470000, 1525000, 1584000, 1647000, 1714000, 1785000, 1860000, 1940000, 2025000, 2115000, 2210000, 2310000, 2415000, 2525000, 2640000, 2760000, 2887000, 3021000, 3162000, 3310000, 3465000, 3628000, 3799000, 3978000, 4165000, 4360000, 4360000];

var expValue = [30, 50, 80, 100, 150, 50, 120, 150, 200, 300, 250, 310, 320, 330, 350, 400, 310, 320, 330, 340, 200, 360, 380, 400, 420, 450, 380, 420, 100];

var expPercent = [1.2, 1.0, 1.0, 0.8, 0.7];

var expLevel = ["S", "A", "B", "C", "D"];

var expMap = ["1-1 鎮守府正面海域", "1-2 南西諸島沖", "1-3 製油所地帯沿岸", "1-4 南西諸島防衛線", "1-5 [Extra] 鎮守府近海", "1-6 [Extra Operation] 鎮守府近海航路", "2-1 カムラン半島", "2-2 バシー島沖", "2-3 東部オリョール海", "2-4 沖ノ島海域", "2-5 [Extra] 沖ノ島沖", "3-1 モーレイ海", "3-2 キス島沖", "3-3 アルフォンシーノ方面", "3-4 北方海域全域", "3-5 [Extra] 北方AL海域", "4-1 ジャム島攻略作戦", "4-2 カレー洋制圧戦", "4-3 リランカ島空襲", "4-4 カスガダマ沖海戦", "4-5 [Extra] カレー洋リランカ島沖", "5-1 南方海域前面", "5-2 珊瑚諸島沖", "5-3 サブ島沖海域", "5-4 サーモン海域", "5-5 [Extra] サーモン海域北方", "6-1 中部海域哨戒線", "6-2 MS諸島沖", "6-3 グアノ環礁沖海域"];

var expType = [__("Basic"), __("Flagship"), __("MVP"), __("MVP and flagship")];

var getExpInfo = function(shipId) {
	var goalLevel, idx;
	if (!(shipId > 0)) {
		return [1, 100, 99];
	}
	$ships = window.$ships, _ships = window._ships;
	idx = shipId;
	goalLevel = 99;
	if (_ships[idx].api_lv > 99) {
		goalLevel = 150;
	}
	if ($ships[_ships[idx].api_ship_id].api_afterlv !== 0 && $ships[_ships[idx].api_ship_id].api_afterlv > _ships[idx].api_lv) {
		goalLevel = Math.min(goalLevel, $ships[_ships[idx].api_ship_id].api_afterlv);
	}
	return [_ships[idx].api_lv, _ships[idx].api_exp[1], goalLevel];
};

ExpCalc = {
	reactClass: React.createClass({displayName: "reactClass",
		getInitialState: function() {
			return {
				lastShipId: 0,
				_ships: null,
				currentLevel: 1,
				nextExp: 100,
				goalLevel: 99,
				mapValue: 30,
				mapPercent: 1.2,
				totalExp: 1000000,
				expSecond: [Math.ceil(1000000 / 30 / 1.2), Math.ceil(1000000 / 30 / 1.2 / 1.5), Math.ceil(1000000 / 30 / 1.2 / 2.0), Math.ceil(1000000 / 30 / 1.2 / 3.0)],
				perExp: [30 * 1.2, 30 * 1.2 * 1.5, 30 * 1.2 * 2.0, 30 * 1.2 * 3.0]
			};
		},
		handleExpChange: function(_currentLevel, _nextExp, _goalLevel, _mapValue, _mapPercent) {
			var _bothType, _mvpType, _noneType, _secType, _totalExp;
			_currentLevel = parseInt(_currentLevel);
			_nextExp = parseInt(_nextExp);
			_goalLevel = parseInt(_goalLevel);
			_mapValue = parseInt(_mapValue);
			_totalExp = 0;
			_totalExp = exp[_goalLevel] - exp[_currentLevel + 1] + _nextExp;
			_noneType = Math.ceil(_totalExp / _mapValue / _mapPercent);
			_secType = Math.ceil(_totalExp / _mapValue / _mapPercent / 1.5);
			_mvpType = Math.ceil(_totalExp / _mapValue / _mapPercent / 2.0);
			_bothType = Math.ceil(_totalExp / _mapValue / _mapPercent / 3.0);
			return this.setState({
				currentLevel: _currentLevel,
				nextExp: _nextExp,
				goalLevel: _goalLevel,
				mapValue: _mapValue,
				mapPercent: _mapPercent,
				totalExp: _totalExp,
				expSecond: [_noneType, _secType, _mvpType, _bothType],
				perExp: [_mapValue * _mapPercent, _mapValue * _mapPercent * 1.5, _mapValue * _mapPercent * 2.0, _mapValue * _mapPercent * 3.0]
			});
		},
		handleShipChange: function(e) {
			var _currentLevel, _goalLevel, _nextExp, ref1;
			$ships = window.$ships;
			//_ships = this.state._ships;
			if (e && e.target.value !== this.state.lastShipId) {
				this.state.lastShipId = e.target.value;
			}
			ref1 = getExpInfo(this.state.lastShipId), _currentLevel = ref1[0], _nextExp = ref1[1], _goalLevel = ref1[2];
			return this.handleExpChange(_currentLevel, _nextExp, _goalLevel, this.state.mapValue, this.state.mapPercent);
		},
		handleCurrentLevelChange: function(e) {
			return this.handleExpChange(e.target.value, this.state.nextExp, this.state.goalLevel, this.state.mapValue, this.state.mapPercent);
		},
		handleNextExpChange: function(e) {
			return this.handleExpChange(this.state.currentLevel, e.target.value, this.state.goalLevel, this.state.mapValue, this.state.mapPercent);
		},
		handleGoalLevelChange: function(e) {
			console.log(e.target.value);
			return this.handleExpChange(this.state.currentLevel, this.state.nextExp, e.target.value, this.state.mapValue, this.state.mapPercent);
		},
		handleExpMapChange: function(e) {
			return this.handleExpChange(this.state.currentLevel, this.state.nextExp, this.state.goalLevel, e.target.value, this.state.mapPercent);
		},
		handleExpLevelChange: function(e) {
			return this.handleExpChange(this.state.currentLevel, this.state.nextExp, this.state.goalLevel, this.state.mapValue, e.target.value);
		},
		handleResponse: function(e) {
			var body, method, postBody, ref1, ships;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			switch (path) {
				case '/kcsapi/api_port/port':
					ships = Object.keys(window._ships).map(function(key) {
						return window._ships[key];
					});
					ships.sort(function(a, b) {
						return b.api_lv-a.api_lv;
					});
					this.setState({
						_ships: ships
					});
					return this.handleShipChange();
			}
		},
		componentDidMount: function() {
			return window.addEventListener('game.response', this.handleResponse);
		},
		componentWillUnmount: function() {
			return window.removeEventListener('game.response', this.handleResponse);
		},
		render: function() {
			var i, ship, shipInfo, x;
			return React.createElement("div", null, React.createElement("link", {
				"rel": "stylesheet",
				"href": "assets/css/exp-calc.css"
			}), React.createElement(Grid, null, React.createElement(Col, {
				"xs": shipRow
			}, React.createElement(Input, {
				"type": "select",
				"label": __("Ship"),
				"value": this.state.lastShipId,
				"onChange": this.handleShipChange
			}, React.createElement("option", {
				"key": 0.
			}, __("NULL")), ((function() {
				var j, len, ref1, results;
				$ships = window.$ships;
				if (this.state._ships) {
					ref1 = this.state._ships;
					results = [];
					for (i = j = 0, len = ref1.length; j < len; i = ++j) {
						ship = ref1[i];
						if (ship == null) {
							continue;
						}
						shipInfo = $ships[ship.api_ship_id];
						results.push(React.createElement("option", {
							"key": i + 1,
							"value": ship.api_id
						}, "Lv. ", ship.api_lv, " - ", shipInfo.api_name));
					}
					return results;
				}
			}).call(this)))), React.createElement(Col, {
				"xs": mapRow
			}, React.createElement(Input, {
				"type": "select",
				"label": __("Map"),
				"onChange": this.handleExpMapChange
			}, (function() {
				var j, len, results;
				results = [];
				for (i = j = 0, len = expMap.length; j < len; i = ++j) {
					x = expMap[i];
					results.push(React.createElement("option", {
						"key": i,
						"value": expValue[i]
					}, x));
				}
				return results;
			})())), React.createElement(Col, {
				"xs": rankRow
			}, React.createElement(Input, {
				"type": "select",
				"label": __("Result"),
				"onChange": this.handleExpLevelChange
			}, (function() {
				var j, len, results;
				results = [];
				for (i = j = 0, len = expLevel.length; j < len; i = ++j) {
					x = expLevel[i];
					results.push(React.createElement("option", {
						"key": i,
						"value": expPercent[i]
					}, x));
				}
				return results;
			})())), React.createElement(Col, {
				"xs": row
			}, React.createElement(Input, {
				"type": "number",
				"label": __("Actual level"),
				"value": this.state.currentLevel,
				"onChange": this.handleCurrentLevelChange
			})), React.createElement(Col, {
				"xs": row
			}, React.createElement(Input, {
				"type": "number",
				"label": __("To next"),
				"value": this.state.nextExp,
				"onChange": this.handleNextExpChange
			})), React.createElement(Col, {
				"xs": row
			}, React.createElement(Input, {
				"type": "number",
				"label": __("Goal"),
				"value": this.state.goalLevel,
				"onChange": this.handleGoalLevelChange
			})), React.createElement(Col, {
				"xs": row
			}, React.createElement(Input, {
				"type": "number",
				"label": __("Total exp"),
				"value": this.state.totalExp,
				"readOnly": true
			}))), React.createElement(Table, null, React.createElement("tbody", null, React.createElement("tr", {
				"key": 0.
			}, React.createElement("td", {
				"width": "10%"
			}, "　"), React.createElement("td", {
				"width": "30%"
			}, "　"), React.createElement("td", {
				"width": "30%"
			}, __("Per attack")), React.createElement("td", {
				"width": "30%"
			}, __("Remainder"))), (function() {
				var j, len, results;
				results = [];
				for (i = j = 0, len = expType.length; j < len; i = ++j) {
					x = expType[i];
					results.push([
						React.createElement("tr", {
							"key": i + 1
						}, React.createElement("td", {
							"width": "10%"
						}, "　"), React.createElement("td", {
							"width": "30%"
						}, expType[i]), React.createElement("td", {
							"width": "30%"
						}, this.state.perExp[i]), React.createElement("td", {
							"width": "30%"
						}, this.state.expSecond[i]))
					]);
				}
				return results;
			}).call(this))));
		}
	})
};
