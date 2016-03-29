(function() {
	var $, $$, Label, NdockPanel, OverlayTrigger, Panel, ROOT, React, ReactBootstrap, Table, Tooltip, _, layout, ref, resolveTime, timeToString;

	ROOT = window.ROOT, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap;

	resolveTime = window.resolveTime;

	Panel = ReactBootstrap.Panel, Table = ReactBootstrap.Table, Label = ReactBootstrap.Label, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	timeToString = function(dateTime) {
		var date;
		date = new Date(dateTime);
		return (date.getHours()) + ":" + (date.getMinutes()) + ":" + (date.getSeconds());
	};

	NdockPanel = React.createClass({displayName: "NdockPanel",
		getInitialState: function() {
			return {
				docks: [
					{
						name: __('Empty'),
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						completeTime: -1,
						countdown: -1
					}
				],
				notified: [],
				show: true
			};
		},
		shouldComponentUpdate: function(nextProps, nextState) {
			return nextState.show;
		},
		handleVisibleResponse: function(e) {
			var visible;
			visible = e.detail.visible;
			return this.setState({
				show: visible
			});
		},
		handleResponse: function(e) {
			var $ships, _ships, body, docks, id, j, k, len, len1, method, ndock, notified, path, postBody, ref1, ref2, ref3;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			$ships = window.$ships, _ships = window._ships;
			ref2 = this.state, docks = ref2.docks, notified = ref2.notified;
			switch (path) {
				case '/kcsapi/api_port/port':
					ref3 = body.api_ndock;
					for (j = 0, len = ref3.length; j < len; j++) {
						ndock = ref3[j];
						id = ndock.api_id;
						switch (ndock.api_state) {
							case -1:
								docks[id] = {
									name: __('Locked'),
									completeTime: -1,
									countdown: -1
								};
								break;
							case 0:
								docks[id] = {
									name: __('Empty'),
									completeTime: -1,
									countdown: -1
								};
								notified[id] = false;
								break;
							case 1:
								docks[id] = {
									name: $ships[_ships[ndock.api_ship_id].api_ship_id].api_name,
									completeTime: ndock.api_complete_time,
									countdown: Math.floor((ndock.api_complete_time - new Date()) / 1000)
								};
						}
					}
					return this.setState({
						docks: docks,
						notified: notified
					});
				case '/kcsapi/api_get_member/ndock':
					for (k = 0, len1 = body.length; k < len1; k++) {
						ndock = body[k];
						id = ndock.api_id;
						switch (ndock.api_state) {
							case -1:
								docks[id] = {
									name: __('Locked'),
									completeTime: -1,
									countdown: -1
								};
								break;
							case 0:
								docks[id] = {
									name: __('Empty'),
									completeTime: -1,
									countdown: -1
								};
								notified[id] = false;
								break;
							case 1:
								docks[id] = {
									name: $ships[_ships[ndock.api_ship_id].api_ship_id].api_name,
									completeTime: ndock.api_complete_time,
									countdown: Math.floor((ndock.api_complete_time - new Date()) / 1000)
								};
						}
					}
					return this.setState({
						docks: docks,
						notified: notified
					});
			}
		},
		updateCountdown: function() {
			var docks, i, j, notified, ref1;
			ref1 = this.state, docks = ref1.docks, notified = ref1.notified;
			for (i = j = 1; j <= 4; i = ++j) {
				if (docks[i].countdown > 0) {
					docks[i].countdown = Math.floor((docks[i].completeTime - new Date()) / 1000);
					if (docks[i].countdown <= 60 && !notified[i]) {
						notify(docks[i].name + " " + (__('repair completed')), {
							type: 'repair',
							icon: 'assets/img/operation/repair.png'
						});
						notified[i] = true;
					}
				}
			}
			return this.setState({
				docks: docks,
				notified: notified
			});
		},
		componentDidMount: function() {
			window.addEventListener('game.response', this.handleResponse);
			window.addEventListener('view.main.visible', this.handleVisibleResponse);
			return setInterval(this.updateCountdown, 1000);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			window.removeEventListener('view.main.visible', this.handleVisibleResponse);
			return clearInterval(this.updateCountdown, 1000);
		},
		render: function() {
			var i;
			return React.createElement("div", null, (function() {
				var j, results;
				results = [];
				for (i = j = 1; j <= 4; i = ++j) {
					if (this.state.docks[i].countdown > 60) {
						results.push(React.createElement("div", {
							"key": i,
							"className": "panel-item ndock-item"
						}, React.createElement("span", {
							"className": "ndock-name"
						}, this.state.docks[i].name), React.createElement(OverlayTrigger, {
							"placement": 'left',
							"overlay": React.createElement(Tooltip, {
								"id": "ndock-finish-by-" + i
							}, React.createElement("strong", null, __('Finish by : ')), timeToString(this.state.docks[i].completeTime))
						}, React.createElement(Label, {
							"className": "ndock-timer",
							"bsStyle": "primary"
						}, resolveTime(this.state.docks[i].countdown)))));
					} else if (this.state.docks[i].countdown > -1) {
						results.push(React.createElement("div", {
							"key": i,
							"className": "panel-item ndock-item"
						}, React.createElement("span", {
							"className": "ndock-name"
						}, this.state.docks[i].name), React.createElement(Label, {
							"className": "ndock-timer",
							"bsStyle": "success"
						}, resolveTime(this.state.docks[i].countdown))));
					} else {
						results.push(React.createElement("div", {
							"key": i,
							"className": "panel-item ndock-item"
						}, React.createElement("span", {
							"className": "ndock-name"
						}, this.state.docks[i].name), React.createElement(Label, {
							"className": "ndock-timer",
							"bsStyle": "default"
						}, resolveTime(0))));
					}
				}
				return results;
			}).call(this));
		}
	});

	return NdockPanel;

})();
