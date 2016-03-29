(function() {
	var $, $$, Alert, OverlayTrigger, React, ReactBootstrap, Tooltip, TopAlert, _, __, getCondCountdown, getDeckMessage, getFontStyle, getSaku25, getSaku25a, getTyku, join, notify, ref, resolveTime;

	$ = window.$, $$ = window.$$, _ = window._, __ = window.__, React = window.React, ReactBootstrap = window.ReactBootstrap, resolveTime = window.resolveTime, notify = window.notify;

	OverlayTrigger = ReactBootstrap.OverlayTrigger, Tooltip = ReactBootstrap.Tooltip, Alert = ReactBootstrap.Alert;

	getFontStyle = function(theme) {
		return {
			color: '#FFF'
		}
	};

	getCondCountdown = function(deck) {
		var $ships, $slotitems, _ships, cond, countdown, i, j, len, ref1, ret, ship, shipId;
		$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships;
		countdown = [0, 0, 0, 0, 0, 0];
		cond = [49, 49, 49, 49, 49, 49];
		ref1 = deck.api_ship;
		for (i = j = 0, len = ref1.length; j < len; i = ++j) {
			shipId = ref1[i];
			if (shipId === -1) {
				countdown[i] = 0;
				cond[i] = 49;
				continue;
			}
			ship = _ships[shipId];
			cond[i] = ship.api_cond;
			countdown[i] = Math.max(countdown[i], Math.ceil((49 - cond[i]) / 3) * 180);
		}
		return ret = {
			countdown: countdown,
			cond: cond
		};
	};

	getTyku = function(deck) {
		var $ships, $slotitems, _ships, _slotitems, alvTyku, basicTyku, item, itemId, j, k, len, len1, ref1, ref2, ref3, ref4, ship, shipId, slotId, totalTyku;
		$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships, _slotitems = window._slotitems;
		basicTyku = alvTyku = totalTyku = 0;
		ref1 = deck.api_ship;
		for (j = 0, len = ref1.length; j < len; j++) {
			shipId = ref1[j];
			if (shipId === -1) {
				continue;
			}
			ship = _ships[shipId];
			ref2 = ship.api_slot;
			for (slotId = k = 0, len1 = ref2.length; k < len1; slotId = ++k) {
				itemId = ref2[slotId];
				if (!(itemId !== -1 && (_slotitems[itemId] != null))) {
					continue;
				}
				item = _slotitems[itemId];
				if ((ref3 = item.api_type[3]) === 6 || ref3 === 7 || ref3 === 8) {
					basicTyku += Math.floor(Math.sqrt(ship.api_onslot[slotId]) * item.api_tyku);
				} else if (item.api_type[3] === 10 && item.api_type[2] === 11) {
					basicTyku += Math.floor(Math.sqrt(ship.api_onslot[slotId]) * item.api_tyku);
				}
				if (item.api_type[3] === 6 && item.api_alv > 0 && item.api_alv <= 7) {
					alvTyku += [0, 1, 4, 6, 11, 16, 17, 25][item.api_alv];
				} else if (((ref4 = item.api_type[3]) === 7 || ref4 === 8) && item.api_alv === 7) {
					alvTyku += 3;
				} else if (item.api_type[3] === 10 && item.api_type[2] === 11 && item.api_alv === 7) {
					alvTyku += 9;
				}
			}
		}
		totalTyku = basicTyku + alvTyku;
		return {
			basic: basicTyku,
			alv: alvTyku,
			total: totalTyku
		};
	};

	getSaku25 = function(deck) {
		var $ships, $slotitems, _ships, _slotitems, item, itemId, j, k, len, len1, radarSaku, reconSaku, ref1, ref2, ship, shipId, shipSaku, slotId, totalSaku;
		$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships, _slotitems = window._slotitems;
		reconSaku = shipSaku = radarSaku = 0;
		ref1 = deck.api_ship;
		for (j = 0, len = ref1.length; j < len; j++) {
			shipId = ref1[j];
			if (shipId === -1) {
				continue;
			}
			ship = _ships[shipId];
			shipSaku += ship.api_sakuteki[0];
			ref2 = ship.api_slot;
			for (slotId = k = 0, len1 = ref2.length; k < len1; slotId = ++k) {
				itemId = ref2[slotId];
				if (!(itemId !== -1 && (_slotitems[itemId] != null))) {
					continue;
				}
				item = _slotitems[itemId];
				switch (item.api_type[3]) {
					case 9:
						reconSaku += item.api_saku;
						shipSaku -= item.api_saku;
						break;
					case 10:
						if (item.api_type[2] === 10) {
							reconSaku += item.api_saku;
							shipSaku -= item.api_saku;
						}
						break;
					case 11:
						radarSaku += item.api_saku;
						shipSaku -= item.api_saku;
				}
			}
		}
		reconSaku = reconSaku * 2.00;
		shipSaku = Math.sqrt(shipSaku);
		totalSaku = reconSaku + radarSaku + shipSaku;
		return {
			recon: parseFloat(reconSaku.toFixed(2)),
			radar: parseFloat(radarSaku.toFixed(2)),
			ship: parseFloat(shipSaku.toFixed(2)),
			total: parseFloat(totalSaku.toFixed(2))
		};
	};

	getSaku25a = function(deck) {
		var $ships, $slotitems, _ships, _slotitems, item, itemId, itemSaku, j, k, len, len1, ref1, ref2, ship, shipId, shipPureSaku, shipSaku, slotId, teitokuSaku, totalSaku;
		$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships, _slotitems = window._slotitems;
		totalSaku = shipSaku = itemSaku = teitokuSaku = 0;
		ref1 = deck.api_ship;
		for (j = 0, len = ref1.length; j < len; j++) {
			shipId = ref1[j];
			if (shipId === -1) {
				continue;
			}
			ship = _ships[shipId];
			shipPureSaku = ship.api_sakuteki[0];
			ref2 = ship.api_slot;
			for (slotId = k = 0, len1 = ref2.length; k < len1; slotId = ++k) {
				itemId = ref2[slotId];
				if (!(itemId !== -1 && (_slotitems[itemId] != null))) {
					continue;
				}
				item = _slotitems[itemId];
				shipPureSaku -= item.api_saku;
				switch (item.api_type[3]) {
					case 7:
						itemSaku += item.api_saku * 1.04;
						break;
					case 8:
						itemSaku += item.api_saku * 1.37;
						break;
					case 9:
						itemSaku += item.api_saku * 1.66;
						break;
					case 10:
						if (item.api_type[2] === 10) {
							itemSaku += item.api_saku * 2.00;
						} else if (item.api_type[2] === 11) {
							itemSaku += item.api_saku * 1.78;
						}
						break;
					case 11:
						if (item.api_type[2] === 12) {
							itemSaku += item.api_saku * 1.00;
						} else if (item.api_type[2] === 13) {
							itemSaku += item.api_saku * 0.99;
						}
						break;
					case 24:
						itemSaku += item.api_saku * 0.91;
				}
			}
			shipSaku += Math.sqrt(shipPureSaku) * 1.69;
		}
		teitokuSaku = 0.61 * Math.floor((window._teitokuLv + 4) / 5) * 5;
		totalSaku = shipSaku + itemSaku - teitokuSaku;
		return {
			ship: parseFloat(shipSaku.toFixed(2)),
			item: parseFloat(itemSaku.toFixed(2)),
			teitoku: parseFloat(teitokuSaku.toFixed(2)),
			total: parseFloat(totalSaku.toFixed(2))
		};
	};

	getDeckMessage = function(deck) {
		var $ships, $slotitems, _ships, j, len, ref1, ship, shipId, totalLv, totalShip;
		$ships = window.$ships, $slotitems = window.$slotitems, _ships = window._ships;
		totalLv = totalShip = 0;
		ref1 = deck.api_ship;
		for (j = 0, len = ref1.length; j < len; j++) {
			shipId = ref1[j];
			if (shipId === -1) {
				continue;
			}
			ship = _ships[shipId];
			totalLv += ship.api_lv;
			totalShip += 1;
		}
		return {
			totalLv: totalLv,
			tyku: getTyku(deck),
			saku25: getSaku25(deck),
			saku25a: getSaku25a(deck)
		};
	};

	TopAlert = React.createClass({displayName: "TopAlert",
		messages: [__('No data')],
		countdown: [0, 0, 0, 0, 0, 0],
		maxCountdown: 0,
		missionCountdown: 0,
		completeTime: 0,
		timeDelta: 0,
		cond: [0, 0, 0, 0, 0, 0],
		isMount: false,
		inBattle: false,
		getInitialState: function() {
			return {
				inMission: false
			};
		},
		handleResponse: function(e) {
			var body, deck, method, path, postBody, ref1, refreshFlag;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			refreshFlag = false;
			switch (path) {
				case '/kcsapi/api_port/port':
					if (this.props.deckIndex !== 0) {
						deck = body.api_deck_port[this.props.deckIndex];
						this.missionCountdown = -1;
						switch (deck.api_mission[0]) {
							case 0:
								this.missionCountdown = -1;
								this.completeTime = -1;
								break;
							case 1:
								this.completeTime = deck.api_mission[2];
								this.missionCountdown = Math.floor((deck.api_mission[2] - new Date()) / 1000);
								break;
							case 2:
								this.completeTime = 0;
								this.missionCountdown = 0;
						}
					}
					this.inBattle = false;
					refreshFlag = true;
					break;
				case '/kcsapi/api_req_mission/start':
					if (postBody.api_deck_id === ("" + (this.props.deckIndex + 1))) {
						this.completeTime = body.api_complatetime;
						this.missionCountdown = Math.floor((body.api_complatetime - new Date()) / 1000);
						this.inBattle = false;
						refreshFlag = true;
					}
					break;
				case '/kcsapi/api_req_mission/return_instruction':
					if (postBody.api_deck_id === this.props.deckIndex) {
						this.completeTime = body.api_mission[2];
						this.missionCountdown = Math.floor((body.api_mission[2] - new Date()) / 1000);
						this.inBattle = false;
						refreshFlag = true;
					}
					break;
				case '/kcsapi/api_req_map/start':
					this.inBattle = true;
					break;
				case '/kcsapi/api_get_member/deck':
				case '/kcsapi/api_get_member/ship_deck':
				case '/kcsapi/api_get_member/ship2':
				case '/kcsapi/api_get_member/ship3':
					refreshFlag = true;
					break;
				case '/kcsapi/api_req_hensei/change':
				case '/kcsapi/api_req_kaisou/powerup':
				case '/kcsapi/api_req_kousyou/destroyship':
					refreshFlag = true;
			}
			if (refreshFlag) {
				return this.setAlert();
			}
		},
		getState: function() {
			if (this.state.inMission) {
				return __('Expedition');
			} else {
				return __('Resting');
			}
		},
		setAlert: function() {
			var changeFlag, decks, inMission, minCond, thisMinCond, tmp;
			decks = window._decks;
			this.messages = getDeckMessage(decks[this.props.deckIndex]);
			if (!this.props.mini) {
				tmp = getCondCountdown(decks[this.props.deckIndex]);
				inMission = this.state.inMission;
				this.missionCountdown = Math.max(0, Math.floor((this.completeTime - new Date()) / 1000));
				changeFlag = false;
				if (this.missionCountdown > 0) {
					this.maxCountdown = this.missionCountdown;
					this.timeDelta = 0;
					if (!inMission) {
						changeFlag = true;
					}
					this.cond = tmp.cond;
				} else {
					this.maxCountdown = tmp.countdown.reduce(function(a, b) {
						return Math.max(a, b);
					});
					this.countdown = tmp.countdown;
					minCond = tmp.cond.reduce(function(a, b) {
						return Math.min(a, b);
					});
					thisMinCond = this.cond.reduce(function(a, b) {
						return Math.min(a, b);
					});
					if (thisMinCond !== minCond) {
						this.timeDelta = 0;
					}
					this.cond = tmp.cond;
					if (inMission) {
						changeFlag = true;
					}
				}
				if (changeFlag) {
					this.setState({
						inMission: !inMission
					});
				}
				if (this.maxCountdown > 0) {
					if (this.interval == null) {
						return this.interval = setInterval(this.updateCountdown, 1000);
					}
				} else {
					if (this.interval != null) {
						this.interval = clearInterval(this.interval);
						return this.clearCountdown();
					}
				}
			}
		},
		componentWillUpdate: function() {
			return this.setAlert();
		},
		updateCountdown: function() {
			var cond, flag;
			if (!this.props.mini) {
				flag = true;
				if (this.maxCountdown - this.timeDelta > 0) {
					flag = false;
					this.timeDelta += 1;
					if (this.isMount) {
						$("#ShipView #deck-condition-countdown-" + this.props.deckIndex + "-" + this.componentId).innerHTML = resolveTime(this.maxCountdown - this.timeDelta);
					}
					if (this.timeDelta % (3 * 60) === 0) {
						cond = this.cond.map((function(_this) {
							return function(c) {
								if (c < 49) {
									return Math.min(49, c + _this.timeDelta / 60);
								} else {
									return c;
								}
							};
						})(this));
						this.props.updateCond(cond);
					}
					if (this.maxCountdown === this.timeDelta && !this.inBattle && !this.state.inMission && window._decks[this.props.deckIndex].api_mission[0] <= 0) {
						notify(this.props.deckName + " " + (__('have recovered from fatigue')), {
							type: 'morale',
							icon: 'assets/img/operation/sortie.png'
						});
					}
				}
				if (flag || (this.inBattle && !this.state.inMission)) {
					this.interval = clearInterval(this.interval);
					return this.clearCountdown();
				}
			}
		},
		clearCountdown: function() {
			if (this.isMount) {
				return $("#ShipView #deck-condition-countdown-" + this.props.deckIndex + "-" + this.componentId).innerHTML = resolveTime(0);
			}
		},
		componentWillMount: function() {
			var deck;
			this.componentId = Math.ceil(Date.now() * Math.random());
			if (this.props.deckIndex !== 0) {
				deck = window._decks[this.props.deckIndex];
				this.missionCountdown = -1;
				switch (deck.api_mission[0]) {
					case 0:
						this.missionCountdown = -1;
						this.completeTime = -1;
						break;
					case 1:
						this.completeTime = deck.api_mission[2];
						this.missionCountdown = Math.floor((deck.api_mission[2] - new Date()) / 1000);
						break;
					case 2:
						this.completeTime = 0;
						this.missionCountdown = 0;
				}
			}
			return this.setAlert();
		},
		componentDidMount: function() {
			this.isMount = true;
			return window.addEventListener('game.response', this.handleResponse);
		},
		componentWillUnmount: function() {
			window.removeEventListener('game.response', this.handleResponse);
			if (this.interval != null) {
				return this.interval = clearInterval(this.interval);
			}
		},
		render: function() {
			return React.createElement("div", {
				"style": {
					width: '100%'
				}
			}, (this.props.mini ? React.createElement("div", {
				"style": {
					display: "flex",
					justifyContent: "space-around",
					width: '100%'
				}
			}, React.createElement("span", {
				"style": {
					flex: "none"
				}
			}, "Lv. ", this.messages.totalLv, " "), React.createElement("span", {
				"style": {
					flex: "none",
					marginLeft: 5
				}
			}, __('Fighter Power'), ": ", this.messages.tyku.total), React.createElement("span", {
				"style": {
					flex: "none",
					marginLeft: 5
				}
			}, __('LOS'), ": ", this.messages.saku25a.total)) : React.createElement(Alert, {
				"style": getFontStyle(window.theme)
			}, React.createElement("div", {
				"style": {
					display: "flex"
				}
			}, React.createElement("span", {
				"style": {
					flex: 1
				}
			}, __('Total Lv'), ". ", this.messages.totalLv), React.createElement("span", {
				"style": {
					flex: 1
				}
			}, React.createElement(OverlayTrigger, {
				"placement": 'bottom',
				"overlay": React.createElement(Tooltip, {
					"id": 'topalert-FP'
				}, React.createElement("span", null, __('Basic FP'), ": ", this.messages.tyku.basic, " ", __('Rank bonuses'), ": ", this.messages.tyku.alv))
			}, React.createElement("span", null, __('Fighter Power'), ": ", this.messages.tyku.total))), React.createElement("span", {
				"style": {
					flex: 1
				}
			}, React.createElement(OverlayTrigger, {
				"placement": 'bottom',
				"overlay": React.createElement(Tooltip, {
					"id": 'topalert-recon'
				}, React.createElement("div", null, "2-5 ", __('Autumn'), ": ", this.messages.saku25a.ship, " + ", this.messages.saku25a.item, " - ", this.messages.saku25a.teitoku, " = ", this.messages.saku25a.total), React.createElement("div", null, "2-5 ", __('Old'), ": ", this.messages.saku25.ship, " + ", this.messages.saku25.recon, " + ", this.messages.saku25.radar, " = ", this.messages.saku25.total))
			}, React.createElement("span", null, __('LOS'), ": ", this.messages.saku25a.total))), React.createElement("span", {
				"style": {
					flex: 1.5
				}
			}, this.getState(), ": ", React.createElement("span", {
				"id": "deck-condition-countdown-" + this.props.deckIndex + "-" + this.componentId
			}, resolveTime(this.maxCountdown)))))));
		}
	});

	return TopAlert;

})();
