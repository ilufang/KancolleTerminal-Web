// Common Utils

var i18nDB = {};

__ = function(str) {
	if (i18nDB[str]) {
		return i18nDB[str];
	} else {
		return str;
	}
}

window.resolveTime = function(seconds) {
	var hours, minutes;
	if (seconds < 0) {
		return '';
	}
	hours = Math.floor(seconds / 3600);
	seconds -= hours * 3600;
	minutes = Math.floor(seconds / 60);
	seconds -= minutes * 60;
	seconds = Math.floor(seconds);
	if (hours < 10) {
		hours = "0" + hours;
	}
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	if (seconds < 10) {
		seconds = "0" + seconds;
	}
	return hours + ":" + minutes + ":" + seconds;
};

window.config = {
	data: JSON.parse(localStorage.getItem("poi.config.json")) || {},
	set: function(k, v) {
		this.data[k] = v;
		localStorage.setItem("poi.config.json", JSON.stringify(this.data));
	},
	get: function(k, v) {
		if (typeof this.data[k] === 'undefined' || this.data[k] === null) {
			return v;
		} else {
			return this.data[k];
		}
	}
}

if (window.innerWidth < 600) {
	window.layout = "horizontal";
} else {
	window.layout = "vertical";
}

if (!Object.assign) {
	Object.assign = function(target) {
		for (var i=1; i<arguments.length; i++) {
			for (var k in arguments[i]) {
				target[k] = arguments[i][k];
			}
		}
		return target;
	}
}

if (!Object.clone) {
	Object.clone = function(obj) {
		return JSON.parse(JSON.stringify(obj));
	};
}

// `env.coffee`: Poi interface builder - analyze data before game.response is dispatched
var resolveResponses = function(method, path, body, postBody) {
	var aa, ab, ac, ad, ae, af, afterSlot, ag, ah, ai, aj, ak, al, am, an, ao, ap, aq, ar, as, at, body, curId, deck, deckId, decks, err, event, extendShip, extendSlotitem, i, idx, item, itemId, j, k, l, len, len1, len10, len11, len12, len13, len14, len15, len16, len17, len18, len19, len2, len20, len21, len22, len23, len24, len25, len26, len27, len28, len3, len4, len5, len6, len7, len8, len9, m, map, maparea, method, mission, n, o, p, postBody, q, r, ref, ref1, ref10, ref11, ref12, ref13, ref14, ref15, ref16, ref17, ref18, ref19, ref2, ref20, ref21, ref22, ref23, ref24, ref25, ref26, ref27, ref28, ref29, ref3, ref30, ref31, ref32, ref4, ref5, ref6, ref7, ref8, ref9, removeId, ship, shipId, slotitem, slotitemtype, stype, t, u, useitem, v, w, x, y, z;
	extendShip = function(ship) {
		return _.extend(_.clone(window.$ships[ship.api_ship_id]), ship);
	};
	extendSlotitem = function(item) {
		return _.extend(_.clone(window.$slotitems[item.api_slotitem_id]), item);
	};
	locked = true;
	// This block should be wrapped in try, however, as caught errors cannot print stacktrace, just let the error go
		body = Object.clone(body);
		postBody = Object.clone(postBody);
		if ((postBody != null ? postBody.api_token : void 0) != null) {
			delete postBody.api_token;
		}
		if ((body != null ? body.api_level : void 0) != null) {
			body.api_level = parseInt(body.api_level);
		}
		if ((body != null ? body.api_member_lv : void 0) != null) {
			body.api_member_lv = parseInt(body.api_member_lv);
		}
		switch (path) {
			case '/kcsapi/api_start2':
				window.$ships = [];
				ref1 = body.api_mst_ship;
				for (k = 0, len = ref1.length; k < len; k++) {
					ship = ref1[k];
					$ships[ship.api_id] = ship;
				}
				window.$shipTypes = [];
				ref2 = body.api_mst_stype;
				for (l = 0, len1 = ref2.length; l < len1; l++) {
					stype = ref2[l];
					$shipTypes[stype.api_id] = stype;
				}
				window.$slotitems = [];
				ref3 = body.api_mst_slotitem;
				for (m = 0, len2 = ref3.length; m < len2; m++) {
					slotitem = ref3[m];
					$slotitems[slotitem.api_id] = slotitem;
				}
				window.$slotitemTypes = [];
				ref4 = body.api_mst_slotitem_equiptype;
				for (n = 0, len3 = ref4.length; n < len3; n++) {
					slotitemtype = ref4[n];
					$slotitemTypes[slotitemtype.api_id] = slotitemtype;
				}
				window.$mapareas = [];
				ref5 = body.api_mst_maparea;
				for (o = 0, len4 = ref5.length; o < len4; o++) {
					maparea = ref5[o];
					$mapareas[maparea.api_id] = maparea;
				}
				window.$maps = [];
				ref6 = body.api_mst_mapinfo;
				for (p = 0, len5 = ref6.length; p < len5; p++) {
					map = ref6[p];
					$maps[map.api_id] = map;
				}
				window.$missions = [];
				ref7 = body.api_mst_mission;
				for (q = 0, len6 = ref7.length; q < len6; q++) {
					mission = ref7[q];
					$missions[mission.api_id] = mission;
				}
				window.$useitems = [];
				ref8 = body.api_mst_useitem;
				for (r = 0, len7 = ref8.length; r < len7; r++) {
					useitem = ref8[r];
					$useitems[useitem.api_id] = useitem;
				}
				break;
			case '/kcsapi/api_get_member/basic':
				window._teitokuLv = body.api_level;
				window._nickName = body.api_nickname;
				window._nickNameId = body.api_nickname_id;
				break;
			case '/kcsapi/api_get_member/deck':
				for (t = 0, len8 = body.length; t < len8; t++) {
					deck = body[t];
					window._decks[deck.api_id - 1] = deck;
				}
				break;
			case '/kcsapi/api_get_member/ndock':
				window._ndocks = body.map(function(e) {
					return e.api_ship_id;
				});
				break;
			case '/kcsapi/api_get_member/ship_deck':
				ref9 = body.api_deck_data;
				for (u = 0, len9 = ref9.length; u < len9; u++) {
					deck = ref9[u];
					window._decks[deck.api_id - 1] = deck;
				}
				ref10 = body.api_ship_data;
				for (v = 0, len10 = ref10.length; v < len10; v++) {
					ship = ref10[v];
					_ships[ship.api_id] = extendShip(ship);
				}
				break;
			case '/kcsapi/api_get_member/ship2':
				for (w = 0, len11 = body.length; w < len11; w++) {
					ship = body[w];
					_ships[ship.api_id] = extendShip(ship);
				}
				break;
			case '/kcsapi/api_get_member/ship3':
				ref11 = body.api_deck_data;
				for (z = 0, len12 = ref11.length; z < len12; z++) {
					deck = ref11[z];
					window._decks[deck.api_id - 1] = deck;
				}
				ref12 = body.api_ship_data;
				for (aa = 0, len13 = ref12.length; aa < len13; aa++) {
					ship = ref12[aa];
					_ships[ship.api_id] = extendShip(ship);
				}
				break;
			case '/kcsapi/api_port/port':
				console.log("Resolve port");
				window._ships = {};
				ref13 = body.api_ship;
				for (ac = 0, len15 = ref13.length; ac < len15; ac++) {
					ship = ref13[ac];
					_ships[ship.api_id] = extendShip(ship);
				}
				window._decks = body.api_deck_port;
				window._ndocks = body.api_ndock.map(function(e) {
					return e.api_ship_id;
				});
				window._teitokuLv = body.api_basic.api_level;
				break;
			case '/kcsapi/api_get_member/slot_item':
				console.log("Resolve slotitems");
				window._slotitems = {};
				for (ab = 0, len14 = body.length; ab < len14; ab++) {
					item = body[ab];
					_slotitems[item.api_id] = extendSlotitem(item);
				}
				break;

			case '/kcsapi/api_req_hensei/change':
				decks = window._decks;
				deckId = parseInt(postBody.api_id) - 1;
				idx = parseInt(postBody.api_ship_idx);
				curId = decks[deckId].api_ship[idx];
				shipId = parseInt(postBody.api_ship_id);
				if (idx === -1) {
					for (i = ad = 1; ad <= 5; i = ++ad) {
						decks[deckId].api_ship[i] = -1;
					}
				} else if (curId === -1) {
					ref14 = [-1, -1], x = ref14[0], y = ref14[1];
					for (i = ae = 0, len16 = decks.length; ae < len16; i = ++ae) {
						deck = decks[i];
						ref15 = deck.api_ship;
						for (j = af = 0, len17 = ref15.length; af < len17; j = ++af) {
							ship = ref15[j];
							if (ship === shipId) {
								ref16 = [i, j], x = ref16[0], y = ref16[1];
								break;
							}
						}
					}
					decks[deckId].api_ship[idx] = shipId;
					if (x !== -1 && y !== -1) {
						if (y <= 4) {
							for (i = ag = ref17 = y; ref17 <= 4 ? ag <= 4 : ag >= 4; i = ref17 <= 4 ? ++ag : --ag) {
								decks[x].api_ship[i] = decks[x].api_ship[i + 1];
							}
						}
						decks[x].api_ship[5] = -1;
					}
				} else if (shipId === -1) {
					if (idx <= 4) {
						for (i = ah = ref18 = idx; ref18 <= 4 ? ah <= 4 : ah >= 4; i = ref18 <= 4 ? ++ah : --ah) {
							decks[deckId].api_ship[i] = decks[deckId].api_ship[i + 1];
						}
					}
					decks[deckId].api_ship[5] = -1;
				} else {
					ref19 = [-1, -1], x = ref19[0], y = ref19[1];
					for (i = ai = 0, len18 = decks.length; ai < len18; i = ++ai) {
						deck = decks[i];
						ref20 = deck.api_ship;
						for (j = aj = 0, len19 = ref20.length; aj < len19; j = ++aj) {
							ship = ref20[j];
							if (ship === shipId) {
								ref21 = [i, j], x = ref21[0], y = ref21[1];
								break;
							}
						}
					}
					decks[deckId].api_ship[idx] = shipId;
					if (x !== -1 && y !== -1) {
						decks[x].api_ship[y] = curId;
					}
				}
				break;
			case '/kcsapi/api_req_hensei/preset_select':
				decks = window._decks;
				deckId = parseInt(postBody.api_deck_id) - 1;
				decks[deckId] = body;
				break;
			case '/kcsapi/api_req_hokyu/charge':
				ref22 = body.api_ship;
				for (ak = 0, len20 = ref22.length; ak < len20; ak++) {
					ship = ref22[ak];
					_ships[ship.api_id] = _.extend(_ships[ship.api_id], ship);
				}
				break;
			case '/kcsapi/api_req_kaisou/powerup':
				ref23 = postBody.api_id_items.split(',');
				for (al = 0, len21 = ref23.length; al < len21; al++) {
					shipId = ref23[al];
					idx = parseInt(shipId);
					ref24 = _ships[idx].api_slot;
					for (am = 0, len22 = ref24.length; am < len22; am++) {
						itemId = ref24[am];
						if (itemId === -1) {
							continue;
						}
						delete _slotitems[itemId];
					}
					delete _ships[idx];
				}
				_ships[body.api_ship.api_id] = extendShip(body.api_ship);
				window._decks = body.api_deck;
				break;
			case '/kcsapi/api_req_kaisou/slotset':
				_ships[parseInt(postBody.api_id)].api_slot[parseInt(postBody.api_slot_idx)] = parseInt(postBody.api_item_id);
				break;
			case '/kcsapi/api_req_kaisou/slot_exchange_index':
				_ships[parseInt(postBody.api_id)].api_slot = body.api_slot;
				break;
			case '/kcsapi/api_req_kousyou/createitem':
				if (body.api_create_flag === 1) {
					_slotitems[body.api_slot_item.api_id] = extendSlotitem(body.api_slot_item);
				}
				break;
			case '/kcsapi/api_req_kousyou/destroyitem2':
				ref25 = postBody.api_slotitem_ids.split(',');
				for (an = 0, len23 = ref25.length; an < len23; an++) {
					itemId = ref25[an];
					delete _slotitems[parseInt(itemId)];
				}
				break;
			case '/kcsapi/api_req_kousyou/destroyship':
				decks = window._decks;
				removeId = parseInt(postBody.api_ship_id);
				ref26 = [-1, -1], x = ref26[0], y = ref26[1];
				for (i = ao = 0, len24 = decks.length; ao < len24; i = ++ao) {
					deck = decks[i];
					ref27 = deck.api_ship;
					for (j = ap = 0, len25 = ref27.length; ap < len25; j = ++ap) {
						shipId = ref27[j];
						if (shipId === removeId) {
							ref28 = [i, j], x = ref28[0], y = ref28[1];
							break;
						}
					}
				}
				if (x !== -1 && y !== -1) {
					if (y === 5) {
						decks[x].api_ship[y] = -1;
					} else {
						for (idx = aq = ref29 = y; ref29 <= 4 ? aq <= 4 : aq >= 4; idx = ref29 <= 4 ? ++aq : --aq) {
							decks[x].api_ship[idx] = decks[x].api_ship[idx + 1];
						}
						decks[x].api_ship[5] = -1;
					}
				}
				ref30 = _ships[removeId].api_slot;
				for (ar = 0, len26 = ref30.length; ar < len26; ar++) {
					itemId = ref30[ar];
					if (itemId === -1) {
						continue;
					}
					delete _slotitems[itemId];
				}
				delete _ships[removeId];
				break;
			case '/kcsapi/api_req_kousyou/getship':
				_ships[body.api_ship.api_id] = extendShip(body.api_ship);
				if (body.api_slotitem != null) {
					ref31 = body.api_slotitem;
					for (as = 0, len27 = ref31.length; as < len27; as++) {
						item = ref31[as];
						_slotitems[item.api_id] = extendSlotitem(item);
					}
				}
				break;
			case '/kcsapi/api_req_kousyou/remodel_slot':
				if (body.api_use_slot_id != null) {
					ref32 = body.api_use_slot_id;
					for (at = 0, len28 = ref32.length; at < len28; at++) {
						itemId = ref32[at];
						delete _slotitems[itemId];
					}
				}
				if (body.api_remodel_flag === 1 && (body.api_after_slot != null)) {
					afterSlot = body.api_after_slot;
					itemId = afterSlot.api_id;
					_slotitems[itemId] = extendSlotitem(afterSlot);
				}
				break;
			case '/kcsapi/api_req_mission/result':
				window._teitokuLv = body.api_member_lv;
				break;
			case '/kcsapi/api_req_nyukyo/speedchange':
				shipId = _ndocks[postBody.api_ndock_id - 1];
				_ships[shipId].api_nowhp = _ships[shipId].api_maxhp;
				_ships[shipId].api_cond = Math.max(40, _ships[shipId].api_cond);
				_ndocks[postBody.api_ndock_id - 1] = 0;
				break;
			case '/kcsapi/api_req_nyukyo/start':
				if (postBody.api_highspeed === '1') {
					shipId = parseInt(postBody.api_ship_id);
					_ships[shipId].api_nowhp = _ships[shipId].api_maxhp;
					_ships[shipId].api_cond = Math.max(40, _ships[shipId].api_cond);
				}
				break;
			case '/kcsapi/api_req_practice/battle_result':
				window._teitokuLv = body.api_member_lv;
				break;
			case '/kcsapi/api_req_sortie/battleresult':
				window._teitokuLv = body.api_member_lv;
		}
		event = new CustomEvent('game.response', {
			bubbles: true,
			cancelable: true,
			detail: {
				method: method,
				path: path,
				body: body,
				postBody: postBody
			}
		});
		window.dispatchEvent(event);


	return locked = false;
};

var poi = {
	resolve: function(e) {resolveResponses(e.detail.method, e.detail.path, e.detail.body, e.detail.postBody);}
}
