var ShipInfoArea = (function(){var React, ShipInfoArea, ShipInfoCheckboxArea, ShipInfoTableArea, config;

var React = window.React, config = window.config;

var Divider = React.createClass({
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

<?php
require 'ship-info-table-area.js.php';
require 'ship-info-filter.js.php';
require 'ship-info-checkbox-area.js.php';
?>

return React.createClass({displayName: "ShipInfoArea",
	getInitialState: function() {
		return {
			sortName: "lv",
			sortOrder: 0,
			shipTypeBoxes: [1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
			lvRadio: 2,
			lockedRadio: 1,
			expeditionRadio: 0,
			modernizationRadio: 0,
			remodelRadio: 0
		};
	},
	componentWillMount: function() {
		var expeditionRadio, lockedRadio, lvRadio, modernizationRadio, remodelRadio, shipTypeBoxes, sortName, sortOrder;
		sortName = config.get("plugin.ShipInfo.sortName", this.state.sortName);
		sortOrder = config.get("plugin.ShipInfo.sortOrder", this.state.sortOrder);
		shipTypeBoxes = JSON.parse(config.get("plugin.ShipInfo.shipTypeBoxes", JSON.stringify(this.state.shipTypeBoxes)));
		lvRadio = config.get("plugin.ShipInfo.lvRadio", this.state.lvRadio);
		lockedRadio = config.get("plugin.ShipInfo.lockedRadio", this.state.lockedRadio);
		expeditionRadio = config.get("plugin.ShipInfo.expeditionRadio", this.state.expeditionRadio);
		modernizationRadio = config.get("plugin.ShipInfo.modernizationRadio", this.state.modernizationRadio);
		remodelRadio = config.get("plugin.ShipInfo.remodelRadio", this.state.remodelRadio);
		return this.setState({
			sortName: sortName,
			sortOrder: sortOrder,
			shipTypeBoxes: shipTypeBoxes,
			lvRadio: lvRadio,
			lockedRadio: lockedRadio,
			expeditionRadio: expeditionRadio,
			modernizationRadio: modernizationRadio,
			remodelRadio: remodelRadio
		});
	},
	sortRules: function(name, order) {
		config.set("plugin.ShipInfo.sortName", name);
		config.set("plugin.ShipInfo.sortOrder", order);
		return this.setState({
			sortName: name,
			sortOrder: order
		});
	},
	filterRules: function(filterType, val) {
		switch (filterType) {
			case 'type':
				config.set("plugin.ShipInfo.shipTypeBoxes", JSON.stringify(val));
				return this.setState({
					shipTypeBoxes: val
				});
			case 'lv':
				config.set("plugin.ShipInfo.lvRadio", val);
				return this.setState({
					lvRadio: val
				});
			case 'locked':
				config.set("plugin.ShipInfo.lvRadio", val);
				return this.setState({
					lockedRadio: val
				});
			case 'expedition':
				config.set("plugin.ShipInfo.expeditionRadio", val);
				return this.setState({
					expeditionRadio: val
				});
			case 'modernization':
				config.set("plugin.ShipInfo.modernizationRadio", val);
				return this.setState({
					modernizationRadio: val
				});
			case 'remodel':
				config.set("plugin.ShipInfo.remodelRadio", val);
				return this.setState({
					remodelRadio: val
				});
		}
	},
	render: function() {
		return React.createElement("div", null, React.createElement(ShipInfoCheckboxArea, {
			"sortRules": this.sortRules,
			"filterRules": this.filterRules,
			"sortKey": this.state.sortName,
			"order": this.state.sortOrder,
			"shipTypeBoxes": this.state.shipTypeBoxes,
			"lvRadio": this.state.lvRadio,
			"lockedRadio": this.state.lockedRadio,
			"expeditionRadio": this.state.expeditionRadio,
			"modernizationRadio": this.state.modernizationRadio,
			"remodelRadio": this.state.remodelRadio
		}), React.createElement(ShipInfoTableArea, {
			"sortRules": this.sortRules,
			"sortName": this.state.sortName,
			"sortOrder": this.state.sortOrder,
			"shipTypeBoxes": this.state.shipTypeBoxes,
			"lvRadio": this.state.lvRadio,
			"lockedRadio": this.state.lockedRadio,
			"expeditionRadio": this.state.expeditionRadio,
			"modernizationRadio": this.state.modernizationRadio,
			"remodelRadio": this.state.remodelRadio
		}));
	}
});

})();
