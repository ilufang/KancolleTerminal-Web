(function() {
	var $, $$, Col, Grid, OverlayTrigger, Panel, ROOT, React, ReactBootstrap, TeitokuPanel, Tooltip, _, __, __n, error, getMaterialImage, layout, log, order, path, rankName, ref, toggleModal, totalExp, warn;

	ROOT = window.ROOT, layout = window.layout, _ = window._, __ = window.__, $ = window.$, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, toggleModal = window.toggleModal;

	log = window.log, warn = window.warn, error = window.error;

	Panel = ReactBootstrap.Panel, Grid = ReactBootstrap.Grid, Col = ReactBootstrap.Col, OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip;

	order = layout === 'horizontal' || window.doubleTabbed ? [1, 3, 5, 7, 2, 4, 6, 8] : [1, 2, 3, 4, 5, 6, 7, 8];

	rankName = ['', '元帥', '大将', '中将', '少将', '大佐', '中佐', '新米中佐', '少佐', '中堅少佐', '新米少佐'];

	totalExp = [0, 100, 300, 600, 1000, 1500, 2100, 2800, 3600, 4500, 5500, 6600, 7800, 9100, 10500, 12000, 13600, 15300, 17100, 19000, 21000, 23100, 25300, 27600, 30000, 32500, 35100, 37800, 40600, 43500, 46500, 49600, 52800, 56100, 59500, 63000, 66600, 70300, 74100, 78000, 82000, 86100, 90300, 94600, 99000, 103500, 108100, 112800, 117600, 122500, 127500, 132700, 138100, 143700, 149500, 155500, 161700, 168100, 174700, 181500, 188500, 195800, 203400, 211300, 219500, 228000, 236800, 245900, 255300, 265000, 275000, 285400, 296200, 307400, 319000, 331000, 343400, 356200, 369400, 383000, 397000, 411500, 426500, 442000, 458000, 474500, 491500, 509000, 527000, 545500, 564500, 584500, 606500, 631500, 661500, 701500, 761500, 851500, 1000000, 1300000, 1600000, 1900000, 2200000, 2600000, 3000000, 3500000, 4000000, 4600000, 5200000, 5900000, 6600000, 7400000, 8200000, 9100000, 10000000, 11000000, 12000000, 13000000, 14000000, 15000000];

	getMaterialImage = function(idx) {
		return "/assets/img/material/0" + idx + ".png";
	};

	TeitokuPanel = React.createClass({displayName: "TeitokuPanel",
		getInitialState: function() {
			return {
				level: 0,
				nickname: null,
				rank: 0,
				nextExp: '?',
				exp: '?',
				shipCount: '??',
				maxChara: '??',
				slotitemCount: '??',
				maxSlotitem: '??',
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
			var body, freeShipSlot, method, ref1;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body;
			switch (path) {
				case '/kcsapi/api_get_member/basic':
					return this.setState({
						level: body.api_level,
						nickname: body.api_nickname,
						rank: body.api_rank,
						exp: body.api_experience,
						nextExp: totalExp[body.api_level] - body.api_experience,
						maxChara: body.api_max_chara,
						maxSlotitem: body.api_max_slotitem
					});
				case '/kcsapi/api_get_member/material':
					return this.setState({
						shipCount: Object.keys(window._ships).length
					});
				case '/kcsapi/api_get_member/slot_item':
					return this.setState({
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_port/port':
					return this.setState({
						shipCount: Object.keys(window._ships).length,
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kaisou/powerup':
					return this.setState({
						shipCount: Object.keys(window._ships).length,
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kousyou/createitem':
					return this.setState({
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kousyou/destroyitem2':
					return this.setState({
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kousyou/destroyship':
					return this.setState({
						shipCount: Object.keys(window._ships).length,
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kousyou/getship':
					return this.setState({
						shipCount: Object.keys(window._ships).length,
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_kousyou/remodel_slot':
					return this.setState({
						slotitemCount: Object.keys(window._slotitems).length
					});
				case '/kcsapi/api_req_mission/result':
					return this.setState({
						level: body.api_member_lv,
						exp: body.api_member_exp,
						nextExp: totalExp[body.api_member_lv] - body.api_member_exp
					});
				case '/kcsapi/api_req_practice/battle_result':
					return this.setState({
						level: body.api_member_lv,
						exp: body.api_member_exp,
						nextExp: totalExp[body.api_member_lv] - body.api_member_exp
					});
				case '/kcsapi/api_req_sortie/battleresult':
					return this.setState({
						shipCount: body.api_get_ship != null ? this.state.shipCount + 1 : this.state.shipCount,
						level: body.api_member_lv,
						exp: body.api_member_exp,
						nextExp: totalExp[body.api_member_lv] - body.api_member_exp
					});
				case '/kcsapi/api_req_combined_battle/battleresult':
					return this.setState({
						shipCount: body.api_get_ship != null ? this.state.shipCount + 1 : this.state.shipCount,
						level: body.api_member_lv,
						exp: body.api_member_exp,
						nextExp: totalExp[body.api_member_lv] - body.api_member_exp
					});
				case '/kcsapi/api_get_member/mapinfo':
					if (config.get('poi.mapstartcheck.ship')) {
						freeShipSlot = config.get('poi.mapstartcheck.freeShipSlot', 4);
						if (this.state.maxChara - this.state.shipCount < freeShipSlot) {
							setTimeout((function(_this) {
								return function() {
									return error(__("Attention! Ship Slot has only %s left.", "" + (_this.state.maxChara - _this.state.shipCount)));
								};
							})(this), 1000);
						}
					}
					if (config.get('poi.mapstartcheck.item')) {
						if (this.state.maxSlotitem - this.state.slotitemCount <= 0) {
							return setTimeout((function(_this) {
								return function() {
									return error(__("Attention! Item Slot is full."));
								};
							})(this), 1000);
						}
					}
			}
		},
		componentDidMount: function() {
			window.addEventListener('game.response', this.handleResponse);
			return window.addEventListener('view.main.visible', this.handleVisibleResponse);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			return window.removeEventListener('view.main.visible', this.handleVisibleResponse);
		},
		getHeader: function() {
			var styleCommon, styleL, styleR;
			if (this.state.nickname != null) {
				styleCommon = {
					minWidth: '60px',
					padding: '2px',
					float: 'left'
				};
				styleL = Object.assign({}, styleCommon, {
					textAlign: 'right'
				});
				styleR = Object.assign({}, styleCommon, {
					textAlign: 'left'
				});
				return React.createElement("div", null, React.createElement(OverlayTrigger, {
					"placement": "bottom",
					"overlay": (this.state.level < 120 ? React.createElement(Tooltip, {
						"id": 'teitoku-exp'
					}, React.createElement("div", {
						"style": {
							display: 'table'
						}
					}, React.createElement("div", null, React.createElement("span", {
						"style": styleL
					}, "Next."), React.createElement("span", {
						"style": styleR
					}, this.state.nextExp)), React.createElement("div", null, React.createElement("span", {
						"style": styleL
					}, "Total Exp."), React.createElement("span", {
						"style": styleR
					}, this.state.exp)))) : React.createElement(Tooltip, {
						"id": 'teitoku-exp'
					}, "Total Exp. ", this.state.exp))
				}, React.createElement("span", null, "Lv. " + this.state.level + "　" + this.state.nickname + "　[" + rankName[this.state.rank] + "]　")), __('Ships'), ": ", this.state.shipCount, " \x2F ", this.state.maxChara, "　", __('Equipment'), ": ", this.state.slotitemCount, " \x2F ", this.state.maxSlotitem);
			} else {
				return React.createElement("div", null, (__('Admiral [Not logged in]')) + "　" + (__("Ships")) + "：? / ?　" + (__("Equipment")) + "：? / ?");
			}
		},
		render: function() {
			return React.createElement(Panel, {
				"bsStyle": "default",
				"className": "teitoku-panel"
			}, this.getHeader());
		}
	});

	return TeitokuPanel;

})();
