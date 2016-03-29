(function() {
	var $, $$, KdockPanel, Label, OverlayTrigger, Panel, ROOT, React, ReactBootstrap, Table, Tooltip, _, getMaterialImage, join, layout, ref, resolveTime, showItemDevResultDelay, success, warn;

	ROOT = window.ROOT, layout = window.layout, _ = window._, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap;

	resolveTime = window.resolveTime, success = window.success, warn = window.warn;

	Panel = ReactBootstrap.Panel, Table = ReactBootstrap.Table, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Label = ReactBootstrap.Label;

	getMaterialImage = function(idx) {
		return "assets/img/material/0" + idx + ".png";
	};

	showItemDevResultDelay = window.config.get('poi.delayItemDevResult', false) ? 6200 : 500;

	KdockPanel = React.createClass({displayName: "KdockPanel",
		getInitialState: function() {
			return {
				docks: [
					{
						name: __('Empty'),
						material: [],
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						material: [],
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						material: [],
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						material: [],
						completeTime: -1,
						countdown: -1
					}, {
						name: __('Empty'),
						material: [],
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
			var $ships, body, docks, id, j, k, kdock, len, len1, method, notified, path, postBody, ref1, ref2, ref3;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			$ships = window.$ships;
			ref2 = this.state, docks = ref2.docks, notified = ref2.notified;
			switch (path) {
				case '/kcsapi/api_get_member/kdock':
					for (j = 0, len = body.length; j < len; j++) {
						kdock = body[j];
						id = kdock.api_id;
						switch (kdock.api_state) {
							case -1:
								docks[id] = {
									name: __('Locked'),
									material: [],
									countdown: -1,
									completeTime: -1
								};
								break;
							case 0:
								docks[id] = {
									name: __('Empty'),
									material: [],
									countdown: -1,
									completeTime: -1
								};
								notified[id] = false;
								break;
							case 2:
								docks[id] = {
									name: $ships[kdock.api_created_ship_id].api_name,
									material: [kdock.api_item1, kdock.api_item2, kdock.api_item3, kdock.api_item4, kdock.api_item5],
									completeTime: kdock.api_complete_time,
									countdown: Math.floor((kdock.api_complete_time - new Date()) / 1000)
								};
								break;
							case 3:
								docks[id] = {
									name: $ships[kdock.api_created_ship_id].api_name,
									material: [kdock.api_item1, kdock.api_item2, kdock.api_item3, kdock.api_item4, kdock.api_item5],
									completeTime: 0,
									countdown: 0
								};
						}
					}
					return this.setState({
						docks: docks,
						notified: notified
					});
				case '/kcsapi/api_req_kousyou/getship':
					ref3 = body.api_kdock;
					for (k = 0, len1 = ref3.length; k < len1; k++) {
						kdock = ref3[k];
						id = kdock.api_id;
						switch (kdock.api_state) {
							case -1:
								docks[id] = {
									name: __('Locked'),
									material: [],
									completeTime: -1,
									countdown: -1
								};
								break;
							case 0:
								docks[id] = {
									name: __('Empty'),
									material: [],
									completeTime: -1,
									countdown: -1
								};
								notified[id] = false;
								break;
							case 2:
								docks[id] = {
									name: $ships[kdock.api_created_ship_id].api_name,
									material: [kdock.api_item1, kdock.api_item2, kdock.api_item3, kdock.api_item4, kdock.api_item5],
									completeTime: kdock.api_complete_time,
									countdown: Math.floor((kdock.api_complete_time - new Date()) / 1000)
								};
								break;
							case 3:
								docks[id] = {
									name: $ships[kdock.api_created_ship_id].api_name,
									material: [kdock.api_item1, kdock.api_item2, kdock.api_item3, kdock.api_item4, kdock.api_item5],
									completeTime: 0,
									countdown: 0
								};
						}
					}
					return this.setState({
						docks: docks,
						notified: notified
					});
				case '/kcsapi/api_req_kousyou/createitem':
					if (body.api_create_flag === 0) {
						setTimeout(function() {
							notify($slotitems[parseInt(body.api_fdata.split(',')[1])].api_name+" 开发失败");
						}, 500);
					} else if (body.api_create_flag === 1) {
						setTimeout(function() {
							notify($slotitems[parseInt(body.api_slot_item.api_slotitem_id)].api_name+" 开发成功");
						}, 500);
					}
			}
		},
		updateCountdown: function() {
			var docks, i, j, notified, ref1;
			ref1 = this.state, docks = ref1.docks, notified = ref1.notified;
			for (i = j = 1; j <= 4; i = ++j) {
				if (docks[i].countdown > 0) {
					docks[i].countdown = Math.floor((docks[i].completeTime - new Date()) / 1000);
					if (docks[i].countdown <= 1 && !notified[i]) {
						notify(docks[i].name + " " + (__("built")), {
							type: 'construction',
							icon: 'assets/img/operation/build.png'
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
					results.push(React.createElement(OverlayTrigger, {
						"key": i,
						"placement": 'top',
						"overlay": React.createElement(Tooltip, {
							"id": "kdock-material-" + i
						}, (this.state.docks[i].material[0] >= 1500 && this.state.docks[i].material[1] >= 1500 && this.state.docks[i].material[2] >= 2000 || this.state.docks[i].material[3] >= 1000 ? React.createElement("span", null, React.createElement("strong", {
							"style": {
								color: '#d9534f'
							}
						}, this.state.docks[i].name), React.createElement("br", null)) : React.createElement("span", null, this.state.docks[i].name, React.createElement("br", null))), React.createElement("img", {
							"src": getMaterialImage(1),
							"className": "material-icon"
						}), " ", this.state.docks[i].material[0], React.createElement("img", {
							"src": getMaterialImage(2),
							"className": "material-icon"
						}), " ", this.state.docks[i].material[1], React.createElement("img", {
							"src": getMaterialImage(3),
							"className": "material-icon"
						}), " ", this.state.docks[i].material[2], React.createElement("img", {
							"src": getMaterialImage(4),
							"className": "material-icon"
						}), " ", this.state.docks[i].material[3], React.createElement("img", {
							"src": getMaterialImage(7),
							"className": "material-icon"
						}), " ", this.state.docks[i].material[4])
					}, (this.state.docks[i].countdown > 0 ? this.state.docks[i].material[0] >= 1500 && this.state.docks[i].material[1] >= 1500 && this.state.docks[i].material[2] >= 2000 || this.state.docks[i].material[3] >= 1000 ? React.createElement("div", {
						"className": "panel-item kdock-item"
					}, React.createElement("span", {
						"className": "kdock-name"
					}, this.state.docks[i].name), React.createElement(Label, {
						"className": "kdock-timer",
						"bsStyle": "danger"
					}, resolveTime(this.state.docks[i].countdown))) : React.createElement("div", {
						"className": "panel-item kdock-item"
					}, React.createElement("span", {
						"className": "kdock-name"
					}, this.state.docks[i].name), React.createElement(Label, {
						"className": "kdock-timer",
						"bsStyle": "primary"
					}, resolveTime(this.state.docks[i].countdown))) : this.state.docks[i].countdown === 0 ? React.createElement("div", {
						"className": "panel-item kdock-item"
					}, React.createElement("span", {
						"className": "kdock-name"
					}, this.state.docks[i].name), React.createElement(Label, {
						"className": "kdock-timer",
						"bsStyle": "success"
					}, resolveTime(this.state.docks[i].countdown))) : React.createElement("div", {
						"className": "panel-item kdock-item"
					}, React.createElement("span", {
						"className": "kdock-name"
					}, this.state.docks[i].name), React.createElement(Label, {
						"className": "kdock-timer",
						"bsStyle": "default"
					}, resolveTime(0))))));
				}
				return results;
			}).call(this));
		}
	});

	return KdockPanel;

})();
