<?php
header("Content-Type: application/json;charset=utf-8");
require_once 'agectrl.php';
//tryModified("improv.json");
tryModified("i18n_itemimprov.json");
?>
var ItemImprovArea = (function() {

var Button, Col, Divider, Grid, ItemImprovementCheckboxArea, ItemInfoArea, ItemInfoTable, Nav, NavItem, Panel, React, ReactBootstrap, Row, Table, day, fs, i18n, path;

var React = window.React, ReactBootstrap = window.ReactBootstrap;

var Panel = ReactBootstrap.Panel, Button = ReactBootstrap.Button, Nav = ReactBootstrap.Nav, NavItem = ReactBootstrap.NavItem, Col = ReactBootstrap.Col, Grid = ReactBootstrap.Grid, Row = ReactBootstrap.Row, Table = ReactBootstrap.Table;

Divider = React.createClass({
	render: function() {
		return React.createElement("div", {
			"className": "divider"
		}, React.createElement("h5", null, this.props.text), React.createElement("hr", null));
	}
});

Object.assign(i18nDB, <?php
	echo file_get_contents("i18n_itemimprov.json");
?>);

var day = (new Date).getUTCDay();

if ((new Date).getUTCHours() >= 15) {
	day = (day + 1) % 7;
}

ItemImprovementCheckboxArea = React.createClass;

ItemInfoTable = React.createClass({displayName: "ItemInfoTable",
	render: function() {
		return React.createElement("tr", null, React.createElement("td", {
			"style": {
				paddingLeft: 10 + 'px'
			}
		}, React.createElement("img", {
			"src": 'assets/img/slotitem/'+this.props.icon
		}), this.props.type), React.createElement("td", null, this.props.name), React.createElement("td", null, this.props.hisho));
	}
});

return React.createClass({displayName: "ItemInfoArea",
	getList: function(_day) {
		var db, flag, hishos, i, j, k, kanmusu, key, len, len1, len2, names, pp, ref, ref1, row, rows, types;
		rows = this.state.rows;
		key = Math.pow(2, 6 - _day);
		db = <?php
			echo file_get_contents("improv.json");
		?>;
		rows = [];
		for (i = 0, len = db.length; i < len; i++) {
			types = db[i];
			ref = types.items;
			for (j = 0, len1 = ref.length; j < len1; j++) {
				names = ref[j];
				flag = 0;
				hishos = "";
				ref1 = names.hisho;
				for (k = 0, len2 = ref1.length; k < len2; k++) {
					kanmusu = ref1[k];
					if (Math.floor(kanmusu.day / key) % 2 === 1) {
						flag = 1;
						hishos = hishos + kanmusu.hisho + "　";
					}
				}
				if (flag) {
					row = {
						icon: types.icon,
						type: types.type,
						name: names.name,
						hisho: hishos
					};
					rows.push(row);
				}
			}
		}
		return this.setState({
			rows: rows
		});
	},
	getInitialState: function() {
		return {
			rows: [],
			dayName: day
		};
	},
	handleKeyChange: function(key) {
		this.getList(key);
		return this.setState({
			dayName: key
		});
	},
	componentDidMount: function() {
		day = (new Date).getUTCDay();
		if ((new Date).getUTCHours() >= 15) {
			day = (day + 1) % 7;
		}
		return this.getList(day);
	},
	componentWillUnmount: function() {
		day = (new Date).getUTCDay();
		if ((new Date).getUTCHours() >= 15) {
			day = (day + 1) % 7;
		}
		return this.getList(day);
	},
	render: function() {
		var index, printRows, row;
		return React.createElement(Grid, {
			"id": "item-info-area"
		}, React.createElement("div", {
			"id": 'item-info-settings'
		}, React.createElement(Divider, {
			"text": __("Weekday setting")
		}), React.createElement(Grid, {
			"className": 'vertical-center'
		}, React.createElement(Col, {
			"xs": 12.
		}, React.createElement(Nav, {
			"bsStyle": "pills",
			"activeKey": this.state.dayName,
			"onSelect": this.handleKeyChange
		}, React.createElement(NavItem, {
			"eventKey": 0.
		}, __("Sunday")), React.createElement(NavItem, {
			"eventKey": 1.
		}, __("Monday")), React.createElement(NavItem, {
			"eventKey": 2.
		}, __("Tuesday")), React.createElement(NavItem, {
			"eventKey": 3.
		}, __("Wednesday")), React.createElement(NavItem, {
			"eventKey": 4.
		}, __("Thursday")), React.createElement(NavItem, {
			"eventKey": 5.
		}, __("Friday")), React.createElement(NavItem, {
			"eventKey": 6.
		}, __("Saturday"))))), React.createElement(Divider, {
			"text": __("Improvement information")
		}), React.createElement(Grid, null, React.createElement(Table, {
			"striped": true,
			"condensed": true,
			"hover": true,
			"id": "main-table"
		}, React.createElement("thead", {
			"className": "item-table"
		}, React.createElement("tr", null, React.createElement("th", {
			"width": "150"
		}, "　　　", __("Type")), React.createElement("th", {
			"width": "250"
		}, __("Name")), React.createElement("th", {
			"width": "200"
		}, __("2nd Ship")))), React.createElement("tbody", null, ((function() {
			var i, j, len, len1, ref, results;
			if (this.state.rows != null) {
				printRows = [];
				ref = this.state.rows;
				for (i = 0, len = ref.length; i < len; i++) {
					row = ref[i];
					printRows.push(row);
				}
				results = [];
				for (index = j = 0, len1 = printRows.length; j < len1; index = ++j) {
					row = printRows[index];
					results.push(React.createElement(ItemInfoTable, {
						"icon": row.icon,
						"type": row.type,
						"name": row.name,
						"hisho": row.hisho
					}));
				}
				return results;
			}
		}).call(this)))))));
	}
});

})();
