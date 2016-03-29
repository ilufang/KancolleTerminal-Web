<?php
header("Content-Type: application/javascript; charset=utf-8");
require_once 'agectrl.php';
tryModified("i18n_prophet.json");
?>

var $, $$, APPDATA_PATH, Alert, Button, Col, Grid, Input, ProgressBar, Promise, ROOT, React, ReactBootstrap, SERVER_HOSTNAME, Table, _, __, airsuprem, async, attackMeth, battledetails, combinedFleetName, combinedMaxHp, combinedName, combinedOpenAttack, combinedRaigekiAttack, combinedStatus, combinedhougekiAttack, db, delayedError, displayError, dropCount, e, enemyEquips, enemyInformation, enemyName, enemyName_buf, enemyPath, error, formation, formationFlag, fs, getCombinedInfo, getCondStyle, getDamage, getEquipName, getHp, getHpClass, getHpStyle, getInfo, getMapEnemy, getResult, getTyku, hougekiAttack, i18n, intercept, join, jsonContent, jsonId, koukuAttack, koukuAttackCombinedPart, layout, maxHp, openAttack, path, raigekiAttack, ref, relative, request, resolveTime, shipName, sortiedFleet, sortiedFleet_buf, supportAttack, sync, tempMsg, toggleModal, updateJson;

_ = window._, $$ = window.$$, React = window.React, ReactBootstrap = window.ReactBootstrap, ROOT = window.ROOT, resolveTime = window.resolveTime, layout = window.layout, toggleModal = window.toggleModal;

var Table = ReactBootstrap.Table, ProgressBar = ReactBootstrap.ProgressBar, Grid = ReactBootstrap.Grid, Input = ReactBootstrap.Input, Col = ReactBootstrap.Col, Alert = ReactBootstrap.Alert, Button = ReactBootstrap.Button;

Object.assign(i18nDB, <?php
	echo file_get_contents("i18n_prophet.json");
?>);

var cloneObj = function(obj) {
	if (null == obj || "object" != typeof obj) return obj;
	var copy = obj.constructor();
	for (var attr in obj) {
		if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
	}
	return copy;
}


getCondStyle = function(cond) {
	if (cond > 49) {
		return {
			color: '#FFFF00'
		};
	} else if (cond < 20) {
		return {
			color: '#DD514C'
		};
	} else if (cond < 30) {
		return {
			color: '#F37B1D'
		};
	} else if (cond < 40) {
		return {
			color: '#FFC880'
		};
	} else {
		return null;
	}
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



formation = [__("Unknown Formation"), __("Line Ahead"), __("Double Line"), __("Diamond"), __("Echelon"), __("Line Abreast"), __("Cruising Formation 1"), __("Cruising Formation 2"), __("Cruising Formation 3"), __("Cruising Formation 4")];
intercept = [__("Unknown Engagement"), __("Parallel Engagement"), __("Head-on Engagement"), __("Crossing the T (Advantage)"), __("Crossing the T (Disadvantage)")];
airsuprem = ["", __("Air Supremacy"), __("Air Superiority"), __("Air Parity"), __("Air Denial")];
attackMeth = [__(""), __("1"), __("DA"), __("CI"), __("CI"), __("5"), __("CI"), __("7"), __("8"), __("9"), __("10"), __("11"), __("12"), __("13"), __("14"), __("15"), __("16")];
combinedFleetName = [__("Standard Fleet"), __("Carrier Task Force"), __("Surface Task Force")];
dropCount = [0, 1, 1, 2, 2, 3, 4];
enemyInformation = {};
jsonId = null;
jsonContent = {};
maxHp = [];
combinedMaxHp = [];
shipName = [];
combinedName = [];
battledetails = [];
enemyEquips = [];
sortiedFleet = __("Sortie Fleet");
enemyName = __("Enemy Vessel");
sortiedFleet_buf = "";
enemyName_buf = "";
combinedStatus = 0;
tempMsg = "";

displayError = function(msg) {
	// TODO
	return window.error(msg);
};

delayedError = displayError;

getTyku = function(ship, slot) {
	var $ships, $slotitems, i, item, j, k, l, len, len1, ref1, ref2, t, tmp, totalTyku;
	totalTyku = 0;
	$slotitems = window.$slotitems, $ships = window.$ships;
	for (i = k = 0, len = ship.length; k < len; i = ++k) {
		tmp = ship[i];
		if (tmp === -1) {
			continue;
		}
		ref1 = $ships[tmp].api_maxeq;
		for (j = l = 0, len1 = ref1.length; l < len1; j = ++l) {
			t = ref1[j];
			if (t === 0) {
				continue;
			}
			if (slot[i][j] === -1) {
				continue;
			}
			item = $slotitems[slot[i][j]];
			if ((ref2 = item.api_type[3]) === 6 || ref2 === 7 || ref2 === 8) {
				totalTyku += Math.floor(Math.sqrt(t) * item.api_tyku);
			} else if (item.api_type[3] === 10 && item.api_type[2] === 11) {
				totalTyku += Math.floor(Math.sqrt(t) * item.api_tyku);
			}
		}
	}
	return totalTyku;
};


getEquipName = function(idx) {
	if (idx <= 0) {
		return ' ';
	} else {
		return window.$slotitems[idx].api_name;
	}
};

getMapEnemy = function(shipName, shipLv, maxHp, nowHp, enemyFormation, enemyTyku, enemyInfo) {
	var $ships, _ships, i, k, len, ref1, tmp;
	$ships = window.$ships, _ships = window._ships;
	ref1 = enemyInfo.ship;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		tmp = ref1[i];
		if (tmp === -1) {
			continue;
		}
		maxHp[i + 6] = enemyInfo.hp[i];
		shipLv[i + 6] = enemyInfo.lv[i];
		nowHp[i + 6] = maxHp[i + 6];
		if ($ships[tmp].api_yomi !== "-") {
			shipName[i + 6] = $ships[tmp].api_name + $ships[tmp].api_yomi.substr(0, 1);
		} else {
			shipName[i + 6] = $ships[tmp].api_name;
		}
	}
	enemyFormation = enemyInfo.formation;
	enemyTyku = enemyInfo.totalTyku;
	return [shipName, shipLv, maxHp, nowHp, enemyFormation, enemyTyku];
};

getInfo = function(shipName, shipLv, friend, enemy, enemyLv, exerciseFlag, body) {
	var $ships, _ships, eParam, eSlot, enemyEquipList, i, k, l, len, len1, shipId;
	eSlot = body.api_eSlot;
	eParam = body.api_eParam;
	$ships = window.$ships, _ships = window._ships;
	enemyEquipList = [];
	for (i = k = 0, len = friend.length; k < len; i = ++k) {
		shipId = friend[i];
		if (shipId === -1) {
			continue;
		}
		shipName[i] = $ships[_ships[shipId].api_ship_id].api_name;
		shipLv[i] = _ships[shipId].api_lv;
	}
	for (i = l = 0, len1 = enemy.length; l < len1; i = ++l) {
		shipId = enemy[i];
		if (shipId === -1) {
			continue;
		}
		shipLv[i + 5] = enemyLv[i];
		if ($ships[shipId].api_yomi === "-") {
			shipName[i + 5] = $ships[shipId].api_name;
		} else {
			if (exerciseFlag === 0) {
				shipName[i + 5] = $ships[shipId].api_name + $ships[shipId].api_yomi.substr(0, 1);
			} else {
				shipName[i + 5] = $ships[shipId].api_name;
			}
		}
		enemyEquipList.push(React.createElement("tr", null, React.createElement("td", null, React.createElement("span", {
			"className": "shiptag"
		}, shipName[i + 5])), React.createElement("td", null, getEquipName(eSlot[i - 1][0])), React.createElement("td", null, getEquipName(eSlot[i - 1][1]))));
		enemyEquipList.push(React.createElement("tr", null, React.createElement("td", null, eParam[i - 1][0], "\x2F", eParam[i - 1][1], "\x2F", eParam[i - 1][2], "\x2F", eParam[i - 1][3]), React.createElement("td", null, getEquipName(eSlot[i - 1][2])), React.createElement("td", null, getEquipName(eSlot[i - 1][3]))));
	}
	enemyEquips = React.createElement("table", null, enemyEquipList);
	return [shipName, shipLv];
};

getCombinedInfo = function(shipName, shipLv, friend) {
	var $ships, _ships, i, k, len, shipId;
	$ships = window.$ships, _ships = window._ships;
	for (i = k = 0, len = friend.length; k < len; i = ++k) {
		shipId = friend[i];
		if (shipId === -1) {
			continue;
		}
		shipName[i] = $ships[_ships[shipId].api_ship_id].api_name;
		shipLv[i] = _ships[shipId].api_lv;
	}
	return [shipName, shipLv];
};

getHp = function(maxHp, nowHp, maxHps, nowHps) {
	var i, k, len, tmp;
	for (i = k = 0, len = maxHps.length; k < len; i = ++k) {
		tmp = maxHps[i];
		if (i === 0) {
			continue;
		}
		maxHp[i - 1] = tmp;
		nowHp[i - 1] = nowHps[i];
	}
	return [maxHp, nowHp];
};

getHpClass = function(nowHp, maxHp) {
	var percent;
	if (nowHp <= 0) {
		return 'shiptag completely-damaged';
	} else {
		percent = nowHp / maxHp * 100;
		if (percent <= 25) {
			return 'shiptag heavily-damaged';
		} else if (percent <= 50) {
			return 'shiptag moderately-damaged';
		} else if (percent <= 75) {
			return 'shiptag lightly-damaged';
		} else {
			return 'shiptag not-damaged';
		}
	}
};

getDamageClass = function(damage) {
	switch (damage % 1) {
		case 0.1:
			return 'dam_crit';
		default:
			if (damage <= 0) {
				return 'dam_miss';
			} else {
				return 'dam';
			}
	}
};


getResult = function(damageHp, nowHp) {
	var enemyCount, enemyDamage, enemyDrop, enemyHp, friendDamage, friendDrop, friendHp, i, k, len, tmp, tmpResult;
	friendDamage = 0.0;
	enemyDamage = 0.0;
	friendDrop = 0;
	enemyDrop = 0;
	enemyCount = 0;
	friendHp = 0.0;
	enemyHp = 0.0;
	for (i = k = 0, len = nowHp.length; k < len; i = ++k) {
		tmp = nowHp[i];
		if (tmp === -1) {
			continue;
		}
		if (i < 6 || i >= 12) {
			enemyDamage += damageHp[i];
			friendHp += tmp;
		} else {
			enemyCount += 1;
			enemyHp += tmp;
			if (nowHp[i] - damageHp[i] <= 0) {
				enemyDrop += 1;
			}
			friendDamage += Math.min(nowHp[i], damageHp[i]);
		}
	}
	tmpResult = __("Unknown");
	tmp = (friendDamage / enemyHp) / (enemyDamage / friendHp);
	if (enemyDrop === enemyCount) {
		tmpResult = "S";
	} else if (enemyDrop >= dropCount[enemyCount]) {
		tmpResult = "A";
	} else if ((nowHp[6] - damageHp[6] <= 0 || friendDamage / enemyHp >= 2.5 * enemyDamage / friendHp) && friendDamage !== 0) {
		tmpResult = "B";
	} else if ((friendDamage / enemyHp >= 1 * enemyDamage / friendHp && friendDamage / enemyHp <= 2.5 * enemyDamage / friendHp) && friendDamage !== 0) {
		tmpResult = "C";
	} else {
		tmpResult = "D";
	}
	return tmpResult;
};


koukuAttack = function(afterHp, kouku) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2, total_lost;
total_lost = 0;
allied_area = [];
if (kouku.api_stage1 != null) {
	allied_area.push(React.createElement("span", {
		"className": "line"
	}, __("Allied Planes"), " ", kouku.api_stage1.api_f_count));
	allied_area.push(React.createElement("span", {
		"className": "line"
	}, __("Fighter Shotdown"), " ", kouku.api_stage1.api_f_lostcount));
	total_lost += kouku.api_stage1.api_f_lostcount;
}
if (kouku.api_stage2 != null) {
	allied_area.push(React.createElement("span", {
		"className": "line"
	}, __("Allied Bombers"), " ", kouku.api_stage2.api_f_count));
	allied_area.push(React.createElement("span", {
		"className": "line"
	}, __("Ship Shotdown"), " ", kouku.api_stage2.api_f_lostcount));
	total_lost += kouku.api_stage2.api_f_lostcount;
}
if (kouku.api_stage3 != null) {
	ref1 = kouku.api_stage3.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i - 1], maxHp[i - 1])
		}, shipName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (kouku.api_stage1 != null) {
	enemy_area.push(React.createElement("span", {
		"className": "line"
	}, __("Enemy Planes"), " ", kouku.api_stage1.api_e_count));
	enemy_area.push(React.createElement("span", {
		"className": "line"
	}, __("Fighter Shotdown"), " ", kouku.api_stage1.api_e_lostcount));
}
if (kouku.api_stage2 != null) {
	enemy_area.push(React.createElement("span", {
		"className": "line"
	}, __("Enemy Bombers"), " ", kouku.api_stage2.api_e_count));
	enemy_area.push(React.createElement("span", {
		"className": "line"
	}, __("Ship Shotdown"), " ", kouku.api_stage2.api_e_lostcount));
}
if (kouku.api_stage3 != null) {
	ref2 = kouku.api_stage3.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
if (kouku.api_stage1 != null) {
	battledetails.push(React.createElement(Alert, null, __("Aerial Battle"), ": ", airsuprem[kouku.api_stage1.api_disp_seiku], " ", __("Bauxite Loss"), " ", total_lost * 5));
	battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
}
return afterHp;
};

koukuAttackCombinedPart = function(afterHp, kouku) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2;
allied_area = [];
if (kouku.api_stage3_combined != null) {
	ref1 = kouku.api_stage3_combined.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i - 1], combinedMaxHp[i - 1])
		}, combinedName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (kouku.api_stage3_combine != null) {
	ref2 = kouku.api_stage3_combined.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
return afterHp;
};

openAttack = function(afterHp, openingAttack) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, __("Opening Torpedo Salvo")));
allied_area = [];
if (openingAttack.api_fdam != null) {
	ref1 = openingAttack.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i - 1], maxHp[i - 1])
		}, shipName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (openingAttack.api_edam != null) {
	ref2 = openingAttack.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
return afterHp;
};

combinedOpenAttack = function(combinedAfterHp, afterHp, openingAttack) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, __("Opening Torpedo Salvo")));
allied_area = [];
if (openingAttack.api_fdam != null) {
	ref1 = openingAttack.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		combinedAfterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(combinedAfterHp[i - 1], combinedMaxHp[i - 1])
		}, combinedName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (openingAttack.api_edam != null) {
	ref2 = openingAttack.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
return [combinedAfterHp, afterHp];
};

hougekiAttack = function(afterHp, hougeki, description) {
var attackType, consecClass, damClass, damage, damageFrom, damageTo, hg_proc, i, j, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, description));
hg_proc = [];
ref1 = hougeki.api_at_list;
for (i = k = 0, len = ref1.length; k < len; i = ++k) {
	damageFrom = ref1[i];
	if (damageFrom === -1) {
		continue;
	}
	damageTo = -1;
	ref2 = hougeki.api_damage[i];
	for (j = l = 0, len1 = ref2.length; l < len1; j = ++l) {
		damage = ref2[j];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage < 0) {
			continue;
		}
		damageTo = hougeki.api_df_list[i][j];
		afterHp[damageTo - 1] -= damage;
		consecClass = "at_first";
		if (j > 0) {
			consecClass = "at_follow";
		}
		if (damageTo > 6) {
			attackType = "";
			if (hougeki.api_at_type) {
				attackType = attackMeth[hougeki.api_at_type[i]];
			}
			hg_proc.push(React.createElement("tr", null, React.createElement("td", {
				"className": consecClass
			}, React.createElement("span", {
				"className": "shiptag allied"
			}, shipName[damageFrom - 1]), " ", attackType), React.createElement("td", null, React.createElement("span", {
				"className": getHpClass(afterHp[damageTo - 1], maxHp[damageTo - 1])
			}, shipName[damageTo - 1]), React.createElement("span", {
				"className": damClass
			}, " -", damage)), React.createElement("td", null)));
		} else {
			attackType = "";
			if (hougeki.api_at_type) {
				attackType = attackMeth[hougeki.api_at_type[i]];
			}
			hg_proc.push(React.createElement("tr", null, React.createElement("td", {
				"className": consecClass
			}, React.createElement("span", {
				"className": "shiptag enemy"
			}, shipName[damageFrom - 1]), " ", attackType), React.createElement("td", null), React.createElement("td", null, React.createElement("span", {
				"className": getHpClass(afterHp[damageTo - 1], maxHp[damageTo - 1])
			}, shipName[damageTo - 1]), React.createElement("span", {
				"className": damClass
			}, " -", damage))));
		}
	}
}
battledetails.push(React.createElement("table", null, hg_proc));
return afterHp;
};

combinedhougekiAttack = function(combinedAfterHp, afterHp, hougeki, description) {
var attackType, consecClass, damClass, damage, damageFrom, damageTo, hg_proc, i, j, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, description));
hg_proc = [];
ref1 = hougeki.api_at_list;
for (i = k = 0, len = ref1.length; k < len; i = ++k) {
	damageFrom = ref1[i];
	if (damageFrom === -1) {
		continue;
	}
	damageTo = -1;
	ref2 = hougeki.api_damage[i];
	for (j = l = 0, len1 = ref2.length; l < len1; j = ++l) {
		damage = ref2[j];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage < 0) {
			continue;
		}
		damageTo = hougeki.api_df_list[i][j];
		if (damageTo - 1 < 6) {
			combinedAfterHp[damageTo - 1] -= damage;
		} else {
			afterHp[damageTo - 1] -= damage;
		}
		consecClass = "at_first";
		if (j > 0) {
			consecClass = "at_follow";
		}
		if (damageTo > 6) {
			attackType = "";
			if (hougeki.api_at_type) {
				attackType = attackMeth[hougeki.api_at_type[i]];
			}
			hg_proc.push(React.createElement("tr", null, React.createElement("td", {
				"className": consecClass
			}, React.createElement("span", {
				"className": "shiptag allied"
			}, combinedName[damageFrom - 1]), " ", attackType), React.createElement("td", null, React.createElement("span", {
				"className": getHpClass(afterHp[damageTo - 1], maxHp[damageTo - 1])
			}, shipName[damageTo - 1]), React.createElement("span", {
				"className": damClass
			}, " -", damage)), React.createElement("td", null)));
		} else {
			attackType = "";
			if (hougeki.api_at_type) {
				attackType = attackMeth[hougeki.api_at_type[i]];
			}
			hg_proc.push(React.createElement("tr", null, React.createElement("td", {
				"className": consecClass
			}, React.createElement("span", {
				"className": "shiptag enemy"
			}, shipName[damageFrom - 1]), " ", attackType), React.createElement("td", null), React.createElement("td", null, React.createElement("span", {
				"className": getHpClass(combinedAfterHp[damageTo - 1], combinedMaxHp[damageTo - 1])
			}, combinedName[damageTo - 1]), React.createElement("span", {
				"className": damClass
			}, " -", damage))));
		}
	}
}
battledetails.push(React.createElement("table", null, hg_proc));
return [combinedAfterHp, afterHp];
};

raigekiAttack = function(afterHp, raigeki) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, __("Closing Torpedo Salvo")));
allied_area = [];
if (raigeki.api_fdam != null) {
	ref1 = raigeki.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i - 1], maxHp[i - 1])
		}, shipName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (raigeki.api_edam != null) {
	ref2 = raigeki.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
return afterHp;
};

combinedRaigekiAttack = function(combinedAfterHp, afterHp, raigeki) {
var allied_area, damClass, damage, enemy_area, i, k, l, len, len1, ref1, ref2;
battledetails.push(React.createElement(Alert, null, __("Closing Torpedo Salvo")));
allied_area = [];
if (raigeki.api_fdam != null) {
	ref1 = raigeki.api_fdam;
	for (i = k = 0, len = ref1.length; k < len; i = ++k) {
		damage = ref1[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		combinedAfterHp[i - 1] -= damage;
		allied_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(combinedAfterHp[i - 1], combinedMaxHp[i - 1])
		}, combinedName[i - 1]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
enemy_area = [];
if (raigeki.api_edam != null) {
	ref2 = raigeki.api_edam;
	for (i = l = 0, len1 = ref2.length; l < len1; i = ++l) {
		damage = ref2[i];
		damClass = getDamageClass(damage);
		damage = Math.floor(damage);
		if (damage <= 0) {
			continue;
		}
		afterHp[i + 5] -= damage;
		enemy_area.push(React.createElement("div", null, React.createElement("span", {
			"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
		}, shipName[i + 5]), React.createElement("span", {
			"className": damClass
		}, " -", damage)));
	}
}
battledetails.push(React.createElement("table", null, React.createElement("tr", null, React.createElement("td", null, allied_area), React.createElement("td", null, enemy_area))));
return [combinedAfterHp, afterHp];
};

getDamage = function(damageHp, nowHp, afterHp, minHp) {
var i, k, len, tmp;
for (i = k = 0, len = afterHp.length; k < len; i = ++k) {
	tmp = afterHp[i];
	damageHp[i] = nowHp[i] - afterHp[i];
	afterHp[i] = Math.max(tmp, minHp);
}
return damageHp;
};

supportAttack = function(afterHp, damages) {
var damClass, damage, i, k, len;
battledetails.push(React.createElement(Alert, null, __("Expedition Support Fire")));
for (i = k = 0, len = damages.length; k < len; i = ++k) {
	damage = damages[i];
	damClass = getDamageClass(damage);
	damage = Math.floor(damage);
	if (damage <= 0) {
		continue;
	}
	if (i > 6) {
		continue;
	}
	afterHp[i + 5] -= damage;
	battledetails.push(React.createElement("div", null, React.createElement("span", {
		"className": getHpClass(afterHp[i + 5], maxHp[i + 5])
	}, shipName[i + 5]), React.createElement("span", {
		"className": damClass
	}, " -", damage)));
}
return afterHp;
};


formationFlag = false;

var Prophet = {
	reactClass: React.createClass({displayName: "reactClass",
		getInitialState: function() {
			return {
				afterHp: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				nowHp: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				maxHp: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				damageHp: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
				shipName: ["空", "空", "空", "空", "空", "空", "空", "空", "空", "空", "空", "空"],
				shipLv: [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
				enemyInfo: null,
				getShip: null,
				enemyFormation: 0,
				enemyTyku: 0,
				enemyIntercept: 0,
				enemyName: __("Enemy Vessel"),
				sortiedFleet: sortiedFleet,
				result: __("Unknown"),
				shipCond: [0, 0, 0, 0, 0, 0],
				deckId: 0,
				enableProphetDamaged: true,
				prophetCondShow: true,
				combinedFlag: 0,
				combinedName: ["空", "空", "空", "空", "空", "空"],
				combinedLv: [-1, -1, -1, -1, -1, -1],
				combinedNowHp: [0, 0, 0, 0, 0, 0],
				combinedMaxHp: [0, 0, 0, 0, 0, 0],
				combinedAfterHp: [0, 0, 0, 0, 0, 0],
				combinedDamageHp: [0, 0, 0, 0, 0, 0],
				battledata: [React.createElement("center", null, __("Waiting for Port"))],
				enemyEquips: []
			};
		},
		handleResponse: function(e) {
			var _deck, _decks, _ships, aa, ab, ac, ad, ae, af, afterHp, ag, ah, ai, aj, ak, al, body, combinedAfterHp, combinedDamageHp, combinedFlag, combinedLv, combinedNowHp, damageHp, deckId, enableProphetDamaged, enemyFormation, enemyInfo, enemyIntercept, enemyTyku, flag, getShip, i, k, l, len, len1, len10, len11, len12, len13, len14, len15, len16, len17, len18, len19, len2, len20, len21, len22, len3, len4, len5, len6, len7, len8, len9, m, method, n, nowHp, o, p, postBody, prophetCondShow, q, r, ref1, ref10, ref11, ref12, ref13, ref14, ref15, ref16, ref17, ref18, ref19, ref2, ref20, ref21, ref22, ref23, ref24, ref25, ref26, ref27, ref28, ref29, ref3, ref30, ref31, ref32, ref33, ref34, ref35, ref36, ref37, ref38, ref39, ref4, ref40, ref41, ref42, ref43, ref44, ref45, ref5, ref6, ref7, ref8, ref9, result, s, shipCond, shipId, shipLv, tmp, tmpHp, tmpShip, u, v, w, x, y, z;
			ref1 = e.detail, method = ref1.method, path = ref1.path, body = ref1.body, postBody = ref1.postBody;
			ref2 = this.state, afterHp = ref2.afterHp, nowHp = ref2.nowHp, maxHp = ref2.maxHp, damageHp = ref2.damageHp, shipName = ref2.shipName, shipLv = ref2.shipLv, enemyInfo = ref2.enemyInfo, getShip = ref2.getShip, enemyFormation = ref2.enemyFormation, enemyTyku = ref2.enemyTyku, enemyIntercept = ref2.enemyIntercept, enemyName = ref2.enemyName, result = ref2.result, shipCond = ref2.shipCond, deckId = ref2.deckId, enableProphetDamaged = ref2.enableProphetDamaged, prophetCondShow = ref2.prophetCondShow, combinedFlag = ref2.combinedFlag, combinedName = ref2.combinedName, combinedLv = ref2.combinedLv, combinedNowHp = ref2.combinedNowHp, combinedMaxHp = ref2.combinedMaxHp, combinedAfterHp = ref2.combinedAfterHp, combinedDamageHp = ref2.combinedDamageHp;
			enableProphetDamaged = true;
			prophetCondShow = true;
			if (path === '/kcsapi/api_req_map/start' || formationFlag) {
				this.setState({
					enemyFormation: 0,
					enemyInformation: 0,
					enemyTyku: 0,
					enemyIntercept: 0,
					enemyName: "" + body.api_maparea_id + "-" + body.api_mapinfo_no + String.fromCharCode(64 + body.api_no),
					result: __("Unknown")
				});
				formationFlag = false;
			}
			flag = false;
			switch (path) {
				case '/kcsapi/api_req_map/start':
					battledetails = [];
					enemyEquips = [];
					enemyName = "" + body.api_maparea_id + "-" + body.api_mapinfo_no + String.fromCharCode(64 + body.api_no);
					enemyName_buf = enemyName;
					jsonId = null;
					flag = true;
					for (i = k = 0; k <= 11; i = ++k) {
						shipLv[i] = -1;
					}
					_deck = window._decks[postBody.api_deck_id - 1];
					if (postBody.api_deck_id === "1") {
						combinedFlag = combinedStatus;
					}
					if (combinedFlag !== 0) {
						sortiedFleet = combinedFleetName[combinedFlag];
						ref3 = getCombinedInfo(combinedName, combinedLv, window._decks[1].api_ship), combinedName = ref3[0], combinedLv = ref3[1];
					} else {
						sortiedFleet = _deck.api_name;
					}
					_ships = window._ships;
					ref4 = _deck.api_ship;
					for (i = l = 0, len = ref4.length; l < len; i = ++l) {
						shipId = ref4[i];
						if (shipId === -1) {
							continue;
						}
						shipName[i] = _ships[shipId].api_name;
						shipLv[i] = _ships[shipId].api_lv;
						maxHp[i] = _ships[shipId].api_maxhp;
						nowHp[i] = _ships[shipId].api_nowhp;
						deckId = postBody.api_deck_id - 1;
					}
					for (i = m = 0, len1 = shipLv.length; m < len1; i = ++m) {
						tmp = shipLv[i];
						damageHp[i] = 0;
					}
					getShip = null;
					if (body.api_enemy != null) {
						if (enemyInformation[body.api_enemy.api_enemy_id] != null) {
							ref5 = getMapEnemy(shipName, shipLv, maxHp, nowHp, enemyFormation, enemyTyku, enemyInformation[body.api_enemy.api_enemy_id]), shipName = ref5[0], shipLv = ref5[1], maxHp = ref5[2], nowHp = ref5[3], enemyFormation = ref5[4], enemyTyku = ref5[5];
						} else {
							jsonId = body.api_enemy.api_enemy_id;
						}
					}
					afterHp = cloneObj(nowHp);
					break;
				case '/kcsapi/api_req_map/next':
					enemyEquips = [];
					enemyName = "" + body.api_maparea_id + "-" + body.api_mapinfo_no + String.fromCharCode(64 + body.api_no);
					enemyName_buf = enemyName;
					battledetails = [];
					jsonId = null;
					flag = true;
					for (i = n = 0, len2 = shipLv.length; n < len2; i = ++n) {
						tmp = shipLv[i];
						damageHp[i] = 0;
					}
					getShip = null;
					for (i = o = 6; o <= 11; i = ++o) {
						shipLv[i] = -1;
					}
					nowHp = cloneObj(afterHp);
					if (body.api_enemy != null) {
						if (enemyInformation[body.api_enemy.api_enemy_id] != null) {
							ref6 = getMapEnemy(shipName, shipLv, maxHp, nowHp, enemyFormation, enemyTyku, enemyInformation[body.api_enemy.api_enemy_id]), shipName = ref6[0], shipLv = ref6[1], maxHp = ref6[2], nowHp = ref6[3], enemyFormation = ref6[4], enemyTyku = ref6[5];
						} else {
							jsonId = body.api_enemy.api_enemy_id;
						}
					}
					break;
				case "/kcsapi/api_req_combined_battle/airbattle":
					battledetails = [];
					for (i = p = 0, len3 = shipLv.length; p < len3; i = ++p) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					for (i = q = 0, len4 = combinedLv.length; q < len4; i = ++q) {
						tmp = combinedLv[i];
						combinedLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref7 = getInfo(shipName, shipLv, _decks[0].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref7[0], shipLv = ref7[1];
					ref8 = getCombinedInfo(combinedName, combinedLv, _decks[1].api_ship), combinedName = ref8[0], combinedLv = ref8[1];
					ref9 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref9[0], nowHp = ref9[1];
					ref10 = getHp(combinedMaxHp, combinedNowHp, body.api_maxhps_combined, body.api_nowhps_combined), combinedMaxHp = ref10[0], combinedNowHp = ref10[1];
					afterHp = cloneObj(nowHp);
					combinedAfterHp = cloneObj(combinedNowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if (body.api_kouku != null) {
						combinedAfterHp = koukuAttackCombinedPart(combinedAfterHp, body.api_kouku);
					}
					if (body.api_kouku2 != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku2);
					}
					if (body.api_kouku2 != null) {
						combinedAfterHp = koukuAttackCombinedPart(combinedAfterHp, body.api_kouku2);
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					combinedDamageHp = getDamage(combinedDamageHp, combinedNowHp, combinedAfterHp, 0);
					result = getResult(damageHp.concat(combinedDamageHp), nowHp.concat(combinedNowHp));
					nowHp = cloneObj(afterHp);
					combinedNowHp = cloneObj(combinedAfterHp);
					break;
				case "/kcsapi/api_req_combined_battle/battle":
					battledetails = [];
					for (i = r = 0, len5 = shipLv.length; r < len5; i = ++r) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					for (i = s = 0, len6 = combinedLv.length; s < len6; i = ++s) {
						tmp = combinedLv[i];
						combinedLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref11 = getInfo(shipName, shipLv, _decks[0].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref11[0], shipLv = ref11[1];
					ref12 = getCombinedInfo(combinedName, combinedLv, _decks[1].api_ship), combinedName = ref12[0], combinedLv = ref12[1];
					ref13 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref13[0], nowHp = ref13[1];
					ref14 = getHp(combinedMaxHp, combinedNowHp, body.api_maxhps_combined, body.api_nowhps_combined), combinedMaxHp = ref14[0], combinedNowHp = ref14[1];
					afterHp = cloneObj(nowHp);
					combinedAfterHp = cloneObj(combinedNowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if (body.api_kouku != null) {
						combinedAfterHp = koukuAttackCombinedPart(combinedAfterHp, body.api_kouku);
					}
					if (body.api_support_info != null) {
						if (body.api_support_info.api_support_airatack != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_airatack.api_stage3.api_edam);
						} else if (body.api_support_info.api_support_hourai != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_hourai.api_damage);
						} else {
							afterHp = supportAttack(afterHp, body.api_support_info.api_damage);
						}
					}
					if (body.api_opening_atack != null) {
						ref15 = combinedOpenAttack(combinedAfterHp, afterHp, body.api_opening_atack), combinedAfterHp = ref15[0], afterHp = ref15[1];
					}
					if (body.api_hougeki1 != null) {
						ref16 = combinedhougekiAttack(combinedAfterHp, afterHp, body.api_hougeki1, __("1st Shelling")), combinedAfterHp = ref16[0], afterHp = ref16[1];
					}
					if (body.api_raigeki != null) {
						ref17 = combinedRaigekiAttack(combinedAfterHp, afterHp, body.api_raigeki), combinedAfterHp = ref17[0], afterHp = ref17[1];
					}
					if (body.api_hougeki2 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki2, __("2nd Shelling"));
					}
					if (body.api_hougeki3 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki3, __("3rd Shelling"));
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					combinedDamageHp = getDamage(combinedDamageHp, combinedNowHp, combinedAfterHp, 0);
					result = getResult(damageHp.concat(combinedDamageHp), nowHp.concat(combinedNowHp));
					nowHp = cloneObj(afterHp);
					combinedNowHp = cloneObj(combinedAfterHp);
					break;
				case "/kcsapi/api_req_combined_battle/midnight_battle":
					for (i = u = 0, len7 = shipLv.length; u < len7; i = ++u) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					for (i = v = 0, len8 = combinedLv.length; v < len8; i = ++v) {
						tmp = combinedLv[i];
						combinedLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref18 = getInfo(shipName, shipLv, _decks[0].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref18[0], shipLv = ref18[1];
					ref19 = getCombinedInfo(combinedName, combinedLv, _decks[1].api_ship), combinedName = ref19[0], combinedLv = ref19[1];
					ref20 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref20[0], nowHp = ref20[1];
					ref21 = getHp(combinedMaxHp, combinedNowHp, body.api_maxhps_combined, body.api_nowhps_combined), combinedMaxHp = ref21[0], combinedNowHp = ref21[1];
					afterHp = cloneObj(nowHp);
					combinedAfterHp = cloneObj(combinedNowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (body.api_hougeki != null) {
						ref22 = combinedhougekiAttack(combinedAfterHp, afterHp, body.api_hougeki, __("Night Combat")), combinedAfterHp = ref22[0], afterHp = ref22[1];
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					combinedDamageHp = getDamage(combinedDamageHp, combinedNowHp, combinedAfterHp, 0);
					result = getResult(damageHp.concat(combinedDamageHp), nowHp.concat(combinedNowHp));
					nowHp = cloneObj(afterHp);
					combinedNowHp = cloneObj(combinedAfterHp);
					break;
				case "/kcsapi/api_req_combined_battle/sp_midnight":
					battledetails = [];
					for (i = w = 0, len9 = shipLv.length; w < len9; i = ++w) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					for (i = x = 0, len10 = combinedLv.length; x < len10; i = ++x) {
						tmp = combinedLv[i];
						combinedLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref23 = getInfo(shipName, shipLv, _decks[0].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref23[0], shipLv = ref23[1];
					ref24 = getCombinedInfo(combinedName, combinedLv, _decks[1].api_ship), combinedName = ref24[0], combinedLv = ref24[1];
					ref25 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref25[0], nowHp = ref25[1];
					ref26 = getHp(combinedMaxHp, combinedNowHp, body.api_maxhps_combined, body.api_nowhps_combined), combinedMaxHp = ref26[0], combinedNowHp = ref26[1];
					afterHp = cloneObj(nowHp);
					combinedAfterHp = cloneObj(combinedNowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (body.api_hougeki != null) {
						ref27 = combinedhougekiAttack(combinedAfterHp, afterHp, body.api_hougeki, __("Night Combat")), combinedAfterHp = ref27[0], afterHp = ref27[1];
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					combinedDamageHp = getDamage(combinedDamageHp, combinedNowHp, combinedAfterHp, 0);
					result = getResult(damageHp.concat(combinedDamageHp), nowHp.concat(combinedNowHp));
					nowHp = cloneObj(afterHp);
					combinedNowHp = cloneObj(combinedAfterHp);
					break;
				case "/kcsapi/api_req_combined_battle/battle_water":
					battledetails = [];
					for (i = y = 0, len11 = shipLv.length; y < len11; i = ++y) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					for (i = z = 0, len12 = combinedLv.length; z < len12; i = ++z) {
						tmp = combinedLv[i];
						combinedLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref28 = getInfo(shipName, shipLv, _decks[0].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref28[0], shipLv = ref28[1];
					ref29 = getCombinedInfo(combinedName, combinedLv, _decks[1].api_ship), combinedName = ref29[0], combinedLv = ref29[1];
					ref30 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref30[0], nowHp = ref30[1];
					ref31 = getHp(combinedMaxHp, combinedNowHp, body.api_maxhps_combined, body.api_nowhps_combined), combinedMaxHp = ref31[0], combinedNowHp = ref31[1];
					afterHp = cloneObj(nowHp);
					combinedAfterHp = cloneObj(combinedNowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if ((body.api_kouku != null) && (body.api_kouku.api_stage3_combined != null)) {
						combinedAfterHp = koukuAttackCombinedPart(combinedAfterHp, body.api_kouku);
					}
					if (body.api_support_info != null) {
						if (body.api_support_info.api_support_airatack != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_airatack.api_stage3.api_edam);
						} else if (body.api_support_info.api_support_hourai != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_hourai.api_damage);
						} else {
							afterHp = supportAttack(afterHp, body.api_support_info.api_damage);
						}
					}
					if (body.api_opening_atack != null) {
						ref32 = combinedOpenAttack(combinedAfterHp, afterHp, body.api_opening_atack), combinedAfterHp = ref32[0], afterHp = ref32[1];
					}
					if (body.api_hougeki1 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki1, __("1st Shelling"));
					}
					if (body.api_hougeki2 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki2, __("2nd Shelling"));
					}
					if (body.api_hougeki3 != null) {
						ref33 = combinedhougekiAttack(combinedAfterHp, afterHp, body.api_hougeki3, __("3rd Shelling")), combinedAfterHp = ref33[0], afterHp = ref33[1];
					}
					if (body.api_raigeki != null) {
						ref34 = combinedRaigekiAttack(combinedAfterHp, afterHp, body.api_raigeki), combinedAfterHp = ref34[0], afterHp = ref34[1];
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					combinedDamageHp = getDamage(combinedDamageHp, combinedNowHp, combinedAfterHp, 0);
					result = getResult(damageHp.concat(combinedDamageHp), nowHp.concat(combinedNowHp));
					nowHp = cloneObj(afterHp);
					combinedNowHp = cloneObj(combinedAfterHp);
					break;
				case "/kcsapi/api_req_combined_battle/battleresult":
					flag = true;
					result = body.api_win_rank;
					notify(enemyName + "战斗结束: " + result);
					enemyName = enemyName_buf + " " + body.api_enemy_info.api_deck_name;
					tmpShip = " ";
					for (i = aa = 0, len13 = nowHp.length; aa < len13; i = ++aa) {
						tmpHp = nowHp[i];
						if (i < 6 && tmpHp < (maxHp[i] * 0.2500001)) {
							tmpShip = tmpShip + " " + shipName[i];
						}
					}
					for (i = ab = 0, len14 = combinedNowHp.length; ab < len14; i = ++ab) {
						tmpHp = combinedNowHp[i];
						if (tmpHp < (combinedMaxHp[i] * 0.2500001)) {
							tmpShip = tmpShip + " " + combinedName[i];
						}
					}
					if (tmpShip !== " ") {
						delayedError((tmpShip + " ") + __("Heavily damaged"));
					}
					if (body.api_get_ship != null) {
						enemyInfo = body.api_enemy_info;
						getShip = body.api_get_ship;
					} else {
						enemyInfo = null;
						getShip = null;
					}
					formationFlag = true;
					break;
				case '/kcsapi/api_req_sortie/battle':
					battledetails = [];
					for (i = ac = 0, len15 = shipLv.length; ac < len15; i = ++ac) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					ref35 = getInfo(shipName, shipLv, _decks[body.api_dock_id - 1].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref35[0], shipLv = ref35[1];
					ref36 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref36[0], nowHp = ref36[1];
					afterHp = cloneObj(nowHp);
					getShip = null;
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (jsonId != null) {
						jsonContent.ship = cloneObj(body.api_ship_ke);
						jsonContent.ship.splice(0, 1);
						jsonContent.lv = cloneObj(body.api_ship_lv);
						jsonContent.lv.splice(0, 1);
						jsonContent.formation = body.api_formation[1];
						jsonContent.totalTyku = getTyku(jsonContent.ship, body.api_eSlot);
						jsonContent.hp = cloneObj(maxHp);
						jsonContent.hp.splice(0, 6);
						enemyFormation = jsonContent.formation;
						enemyTyku = jsonContent.totalTyku;
					}
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if (body.api_kouku != null) {
						combinedAfterHp = koukuAttackCombinedPart(combinedAfterHp, body.api_kouku);
					}
					if (body.api_support_info != null) {
						if (body.api_support_info.api_support_airatack != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_airatack.api_stage3.api_edam);
						} else if (body.api_support_info.api_support_hourai != null) {
							afterHp = supportAttack(afterHp, body.api_support_info.api_support_hourai.api_damage);
						} else {
							afterHp = supportAttack(afterHp, body.api_support_info.api_damage);
						}
					}
					if (body.api_opening_atack != null) {
						afterHp = openAttack(afterHp, body.api_opening_atack);
					}
					if (body.api_hougeki1 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki1, __("1st Shelling"));
					}
					if (body.api_hougeki2 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki2, __("2nd Shelling"));
					}
					if (body.api_hougeki3 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki3, __("3rd Shelling"));
					}
					if (body.api_raigeki != null) {
						afterHp = raigekiAttack(afterHp, body.api_raigeki);
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_battle_midnight/sp_midnight':
					battledetails = [];
					for (i = ad = 0, len16 = shipLv.length; ad < len16; i = ++ad) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref37 = getInfo(shipName, shipLv, _decks[body.api_deck_id - 1].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref37[0], shipLv = ref37[1];
					ref38 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref38[0], nowHp = ref38[1];
					afterHp = cloneObj(nowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (jsonId != null) {
						jsonContent.ship = cloneObj(body.api_ship_ke);
						jsonContent.ship.splice(0, 1);
						jsonContent.lv = cloneObj(body.api_ship_lv);
						jsonContent.lv.splice(0, 1);
						jsonContent.formation = body.api_formation[1];
						jsonContent.totalTyku = getTyku(jsonContent.ship, body.api_eSlot);
						jsonContent.hp = cloneObj(maxHp);
						jsonContent.hp.splice(0, 6);
						enemyFormation = jsonContent.formation;
						enemyTyku = jsonContent.totalTyku;
					}
					if (body.api_hougeki != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki, __("Night Combat"));
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_sortie/airbattle':
					battledetails = [];
					for (i = ae = 0, len17 = shipLv.length; ae < len17; i = ++ae) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					ref39 = getInfo(shipName, shipLv, _decks[body.api_dock_id - 1].api_ship, body.api_ship_ke, body.api_ship_lv, 0, body), shipName = ref39[0], shipLv = ref39[1];
					ref40 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref40[0], nowHp = ref40[1];
					afterHp = cloneObj(nowHp);
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					if (jsonId != null) {
						jsonContent.ship = cloneObj(body.api_ship_ke);
						jsonContent.ship.splice(0, 1);
						jsonContent.lv = cloneObj(body.api_ship_lv);
						jsonContent.lv.splice(0, 1);
						jsonContent.formation = body.api_formation[1];
						jsonContent.totalTyku = getTyku(jsonContent.ship, body.api_eSlot);
						jsonContent.hp = cloneObj(maxHp);
						jsonContent.hp.splice(0, 6);
						enemyFormation = jsonContent.formation;
						enemyTyku = jsonContent.totalTyku;
					}
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if (body.api_kouku2 != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku2);
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_battle_midnight/battle':
					flag = true;
					nowHp = cloneObj(afterHp);
					if (body.api_hougeki != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki, __("Night Combat"));
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_member/get_practice_enemyinfo':
					flag = true;
					enemyName_buf = body.api_nickname + ": " + body.api_deckname;
					break;
				case '/kcsapi/api_req_practice/battle':
					battledetails = [];
					enemyName = enemyName_buf;
					for (i = af = 0, len18 = shipLv.length; af < len18; i = ++af) {
						tmp = shipLv[i];
						shipLv[i] = -1;
					}
					_decks = window._decks;
					flag = true;
					getShip = null;
					sortiedFleet = _decks[body.api_dock_id - 1].api_name;
					ref41 = getInfo(shipName, shipLv, _decks[body.api_dock_id - 1].api_ship, body.api_ship_ke, body.api_ship_lv, 1, body), shipName = ref41[0], shipLv = ref41[1];
					ref42 = getHp(maxHp, nowHp, body.api_maxhps, body.api_nowhps), maxHp = ref42[0], nowHp = ref42[1];
					if (body.api_formation != null) {
						enemyFormation = body.api_formation[1];
						enemyIntercept = body.api_formation[2];
					}
					afterHp = cloneObj(nowHp);
					if (body.api_kouku != null) {
						afterHp = koukuAttack(afterHp, body.api_kouku);
					}
					if (body.api_opening_atack != null) {
						afterHp = openAttack(afterHp, body.api_opening_atack);
					}
					if (body.api_hougeki1 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki1, __("1st Shelling"));
					}
					if (body.api_hougeki2 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki2, __("2nd Shelling"));
					}
					if (body.api_hougeki3 != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki3, __("3rd Shelling"));
					}
					if (body.api_raigeki != null) {
						afterHp = raigekiAttack(afterHp, body.api_raigeki);
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_practice/midnight_battle':
					flag = true;
					nowHp = cloneObj(afterHp);
					if (body.api_hougeki != null) {
						afterHp = hougekiAttack(afterHp, body.api_hougeki, __("Night Combat"));
					}
					damageHp = getDamage(damageHp, nowHp, afterHp, 0);
					result = getResult(damageHp, nowHp);
					nowHp = cloneObj(afterHp);
					break;
				case '/kcsapi/api_req_practice/battle_result':
					flag = true;
					result = body.api_win_rank;
					notify("演习结束: " + result);
					break;
				case '/kcsapi/api_req_sortie/battleresult':
					flag = true;
					result = body.api_win_rank;
					notify(enemyName + "战斗结束: " + result);
					enemyName = enemyName_buf + " " + body.api_enemy_info.api_deck_name;
					tmpShip = " ";
					for (i = ag = 0, len19 = nowHp.length; ag < len19; i = ++ag) {
						tmpHp = nowHp[i];
						if (i < 6 && tmpHp < (maxHp[i] * 0.2500001)) {
							tmpShip = tmpShip + " " + shipName[i];
						}
					}
					if (tmpShip !== " ") {
						delayedError((tmpShip + " ") + __("Heavily damaged"));
					}
					if (jsonId != null) {
						updateJson(jsonId, jsonContent);
					}
					if (body.api_get_ship != null) {
						enemyInfo = body.api_enemy_info;
						getShip = body.api_get_ship;
					} else {
						enemyInfo = null;
						getShip = null;
					}
					formationFlag = true;
					break;
				case '/kcsapi/api_port/port':
					battledetails = [];
					enemyEquips = [];
					sortiedFleet = body.api_basic.api_nickname;
					enemyName = "";
					flag = true;
					if (body.api_combined_flag != null) {
						combinedStatus = body.api_combined_flag;
					} else {
						combinedStatus = 0;
					}
					combinedFlag = 0;
					_ships = window._ships;
					enemyFormation = 0;
					for (i = ah = 0; ah <= 11; i = ++ah) {
						shipLv[i] = -1;
					}
					if (combinedStatus !== 0) {
						ref43 = window._decks[0].api_ship;
						for (i = ai = 0, len20 = ref43.length; ai < len20; i = ++ai) {
							shipId = ref43[i];
							if (shipId === -1) {
								continue;
							}
							shipName[i] = _ships[shipId].api_name;
							shipLv[i] = _ships[shipId].api_lv;
							maxHp[i] = _ships[shipId].api_maxhp;
							nowHp[i] = _ships[shipId].api_nowhp;
							shipCond[i] = _ships[shipId].api_cond;
							damageHp[i] = 0;
						}
						ref44 = window._decks[1].api_ship;
						for (i = aj = 0, len21 = ref44.length; aj < len21; i = ++aj) {
							shipId = ref44[i];
							if (shipId === -1) {
								continue;
							}
							combinedName[i] = _ships[shipId].api_name;
							combinedLv[i] = _ships[shipId].api_lv;
							combinedMaxHp[i] = _ships[shipId].api_maxhp;
							combinedNowHp[i] = _ships[shipId].api_nowhp;
						}
					} else {
						_deck = window._decks[deckId];
						for (i = ak = 0; ak <= 11; i = ++ak) {
							shipLv[i] = -1;
						}
						ref45 = _deck.api_ship;
						for (i = al = 0, len22 = ref45.length; al < len22; i = ++al) {
							shipId = ref45[i];
							if (shipId === -1) {
								continue;
							}
							shipName[i] = _ships[shipId].api_name;
							shipLv[i] = _ships[shipId].api_lv;
							maxHp[i] = _ships[shipId].api_maxhp;
							nowHp[i] = _ships[shipId].api_nowhp;
							damageHp[i] = 0;
							shipCond[i] = _ships[shipId].api_cond;
						}
					}
			}
			if (!flag) {
				return;
			}
			return this.setState({
				afterHp: afterHp,
				nowHp: nowHp,
				maxHp: maxHp,
				shipName: shipName,
				shipLv: shipLv,
				enemyInfo: enemyInfo,
				getShip: getShip,
				enemyFormation: enemyFormation,
				enemyTyku: enemyTyku,
				enemyIntercept: enemyIntercept,
				enemyName: enemyName,
				result: result,
				shipCond: shipCond,
				deckId: deckId,
				enableProphetDamaged: enableProphetDamaged,
				prophetCondShow: prophetCondShow,
				combinedFlag: combinedFlag,
				combinedName: combinedName,
				combinedLv: combinedLv,
				combinedNowHp: combinedNowHp,
				combinedMaxHp: combinedMaxHp,
				combinedAfterHp: combinedAfterHp,
				combinedDamageHp: combinedDamageHp,
				sortiedFleet: sortiedFleet,
				enemyEquips: enemyEquips,
				battledata: battledetails
			});
		},
		componentDidMount: function() {
			return window.addEventListener('game.response', this.handleResponse);
		},
		render: function() {
			var i, j, list, tmpName;

			if (layout === 'horizontal') {
				return React.createElement("div", null, React.createElement("link", {
					"rel": "stylesheet",
					"href": "assets/css/prophet.css"
				}), React.createElement(Alert, null, (this.state.combinedFlag === 0 ? React.createElement(Grid, null, React.createElement(Col, {
					"xs": 6.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 6.
				}, __("HP"))) : React.createElement(Grid, null, React.createElement(Col, {
					"xs": 3.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 3.
				}, __("HP")), React.createElement(Col, {
					"xs": 3.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 3.
				}, __("HP"))))), React.createElement(Table, null, React.createElement("tbody", null, (function() {
					var k, len, ref1, results;
					ref1 = this.state.shipName;
					results = [];
					for (i = k = 0, len = ref1.length; k < len; i = ++k) {
						tmpName = ref1[i];
						if (this.state.shipLv[i] === -1) {
							continue;
						}
						if (!(i < 6)) {
							continue;
						}
						if (this.state.combinedFlag === 0) {
							results.push(React.createElement("tr", {
								"key": i + 1
							}, React.createElement("td", null, "\t\t\t\t\t\t\t\t\t\t\tLv ", this.state.shipLv[i], " - ", tmpName, (this.state.prophetCondShow && this.state.combinedFlag === 0 ? React.createElement("span", {
								"style": getCondStyle(this.state.shipCond[i])
							}, React.createElement("span", { // TODO FA
								"key": 1.,
								"name": 'star'
							}), this.state.shipCond[i]) : void 0)), React.createElement("td", {
								"className": "hp-progress"
							}, React.createElement(ProgressBar, {
								"bsStyle": getHpStyle(this.state.nowHp[i] / this.state.maxHp[i] * 100),
								"now": this.state.nowHp[i] / this.state.maxHp[i] * 100,
								"label": (this.state.damageHp[i] > 0 ? this.state.nowHp[i] + " / " + this.state.maxHp[i] + " (-" + this.state.damageHp[i] + ")" : this.state.nowHp[i] + " / " + this.state.maxHp[i])
							}))));
						} else {
							results.push(React.createElement("tr", {
								"key": i + 1
							}, React.createElement("td", null, "\t\t\t\t\t\t\t\t\t\t\tLv ", this.state.shipLv[i], " - ", tmpName, (this.state.prophetCondShow && this.state.combinedFlag === 0 ? React.createElement("span", {
								"style": getCondStyle(this.state.shipCond[i])
							}, React.createElement("span", { // TODO FA
								"key": 1.,
								"name": 'star'
							}), this.state.shipCond[i]) : void 0)), React.createElement("td", {
								"className": "hp-progress"
							}, React.createElement(ProgressBar, {
								"bsStyle": getHpStyle(this.state.nowHp[i] / this.state.maxHp[i] * 100),
								"now": this.state.nowHp[i] / this.state.maxHp[i] * 100,
								"label": (this.state.damageHp[i] > 0 ? this.state.nowHp[i] + " / " + this.state.maxHp[i] + " (-" + this.state.damageHp[i] + ")" : this.state.nowHp[i] + " / " + this.state.maxHp[i])
							})), React.createElement("td", null, "\t\t\t\t\t\t\t\t\t\t\tLv ", this.state.combinedLv[i], " - ", this.state.combinedName[i]), React.createElement("td", {
								"className": "hp-progress"
							}, React.createElement(ProgressBar, {
								"bsStyle": getHpStyle(this.state.combinedNowHp[i] / this.state.combinedMaxHp[i] * 100),
								"now": this.state.combinedNowHp[i] / this.state.combinedMaxHp[i] * 100,
								"label": (this.state.combinedDamageHp[i] > 0 ? this.state.combinedNowHp[i] + " / " + this.state.combinedMaxHp[i] + " (-" + this.state.combinedDamageHp[i] + ")" : this.state.combinedNowHp[i] + " / " + this.state.combinedMaxHp[i])
							}))));
						}
					}
					return results;
				}).call(this))), React.createElement(Alert, null, React.createElement(Grid, null, React.createElement(Col, {
					"xs": 6.
				}, this.state.enemyName), React.createElement(Col, {
					"xs": 6.
				}, __("HP")))), React.createElement(Table, null, React.createElement("tbody", null, (function() {
					var k, len, ref1, results;
					ref1 = this.state.shipName;
					results = [];
					for (i = k = 0, len = ref1.length; k < len; i = ++k) {
						tmpName = ref1[i];
						if (this.state.shipLv[i] === -1) {
							continue;
						}
						if (!(i >= 6)) {
							continue;
						}
						results.push(React.createElement("tr", {
							"key": i
						}, React.createElement("td", null, "Lv ", this.state.shipLv[i], " - ", tmpName), React.createElement("td", {
							"className": "hp-progress"
						}, React.createElement(ProgressBar, {
							"bsStyle": getHpStyle(this.state.nowHp[i] / this.state.maxHp[i] * 100),
							"now": this.state.nowHp[i] / this.state.maxHp[i] * 100,
							"label": (this.state.damageHp[i] > 0 ? this.state.nowHp[i] + " / " + this.state.maxHp[i] + " (-" + this.state.damageHp[i] + ")" : this.state.nowHp[i] + " / " + this.state.maxHp[i])
						}))));
					}
					return results;
				}).call(this))), ((this.state.getShip != null) && (this.state.enemyInfo != null) ? React.createElement(Alert, null, (this.state.result + " ") + __("New Ship: ") + (" " + this.state.getShip.api_ship_type + "「" + this.state.getShip.api_ship_name + "」")) : this.state.enemyFormation !== 0 ? React.createElement(Alert, null, " " + formation[this.state.enemyFormation] + " " + intercept[this.state.enemyIntercept] + " - " + this.state.result) : void 0), React.createElement("div", null, this.state.enemyEquips), React.createElement("div", null, this.state.battledata));
			} else {
				return React.createElement("div", null, React.createElement("link", {
					"rel": "stylesheet",
					"href": 'assets/css/prophet.css'
				}), React.createElement(Alert, null, (this.state.combinedFlag === 0 ? React.createElement(Grid, null, React.createElement(Col, {
					"xs": 3.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 3.
				}, __("HP")), React.createElement(Col, {
					"xs": 3.
				}, this.state.enemyName), React.createElement(Col, {
					"xs": 3.
				}, __("HP"))) : React.createElement(Grid, null, React.createElement(Col, {
					"xs": 2.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 2.
				}, __("HP")), React.createElement(Col, {
					"xs": 2.
				}, this.state.sortiedFleet), React.createElement(Col, {
					"xs": 2.
				}, __("HP")), React.createElement(Col, {
					"xs": 2.
				}, this.state.enemyName), React.createElement(Col, {
					"xs": 2.
				}, __("HP"))))), React.createElement(Table, null, React.createElement("tbody", null, (function() {
					var k, l, len, m, ref1, results;
					ref1 = this.state.shipName;
					results = [];
					for (i = k = 0, len = ref1.length; k < len; i = ++k) {
						tmpName = ref1[i];
						if (this.state.shipLv[i] === -1 && this.state.shipLv[i + 6] === -1) {
							continue;
						}
						if (i >= 6) {
							continue;
						}
						list = [];
						if (this.state.shipLv[i] === -1) {
							for (j = l = 0; l <= 1; j = ++l) {
								list.push(React.createElement("td", null, "\u3000"));
							}
						} else {
							list.push(React.createElement("td", null, "\t\t\t\t\t\t\t\t\t\tLv ", this.state.shipLv[i], " - ", tmpName, (this.state.prophetCondShow && this.state.combinedFlag === 0 ? React.createElement("span", {
								"style": getCondStyle(this.state.shipCond[i])
							}, React.createElement("span", { // TODO FA
								"key": 1.,
								"name": 'star'
							}), this.state.shipCond[i]) : void 0)));
							list.push(React.createElement("td", {
								"className": "hp-progress"
							}, React.createElement(ProgressBar, {
								"bsStyle": getHpStyle(this.state.nowHp[i] / this.state.maxHp[i] * 100),
								"now": this.state.nowHp[i] / this.state.maxHp[i] * 100,
								"label": (this.state.damageHp[i] > 0 ? this.state.nowHp[i] + " / " + this.state.maxHp[i] + " (-" + this.state.damageHp[i] + ")" : this.state.nowHp[i] + " / " + this.state.maxHp[i])
							})));
							if (this.state.combinedFlag !== 0) {
								list.push(React.createElement("td", null, "\t\t\t\t\t\t\t\t\t\t\tLv ", this.state.combinedLv[i], " - ", this.state.combinedName[i]));
								list.push(React.createElement("td", {
									"className": "hp-progress"
								}, React.createElement(ProgressBar, {
									"bsStyle": getHpStyle(this.state.combinedNowHp[i] / this.state.combinedMaxHp[i] * 100),
									"now": this.state.combinedNowHp[i] / this.state.combinedMaxHp[i] * 100,
									"label": (this.state.combinedDamageHp[i] > 0 ? this.state.combinedNowHp[i] + " / " + this.state.combinedMaxHp[i] + " (-" + this.state.combinedDamageHp[i] + ")" : this.state.combinedNowHp[i] + " / " + this.state.combinedMaxHp[i])
								})));
							}
						}
						if (this.state.shipLv[i + 6] === -1) {
							for (j = m = 0; m <= 1; j = ++m) {
								list.push(React.createElement("td", null, "\u3000"));
							}
						} else {
							list.push(React.createElement("td", null, "Lv ", this.state.shipLv[i + 6], " - ", this.state.shipName[i + 6]));
							list.push(React.createElement("td", {
								"className": "hp-progress"
							}, React.createElement(ProgressBar, {
								"bsStyle": getHpStyle(this.state.nowHp[i + 6] / this.state.maxHp[i + 6] * 100),
								"now": this.state.nowHp[i + 6] / this.state.maxHp[i + 6] * 100,
								"label": (this.state.damageHp[i + 6] > 0 ? this.state.nowHp[i + 6] + " / " + this.state.maxHp[i + 6] + " (-" + this.state.damageHp[i + 6] + ")" : this.state.nowHp[i + 6] + " / " + this.state.maxHp[i + 6])
							})));
						}
						if (this.state.shipLv[i] === -1 && this.state.shipLv[i + 6] === -1) {
							continue;
						}
						results.push(React.createElement("tr", {
							"key": i
						}, list));
					}
					return results;
				}).call(this))), ((this.state.getShip != null) && (this.state.enemyInfo != null) ? React.createElement(Alert, null, (this.state.result + " ") + __("New Ship: ") + (" " + this.state.getShip.api_ship_type + "「" + this.state.getShip.api_ship_name + "」")) : this.state.enemyFormation !== 0 ? React.createElement(Alert, null, " " + formation[this.state.enemyFormation] + " " + intercept[this.state.enemyIntercept] + " - " + this.state.result) : void 0), React.createElement("div", null, this.state.enemyEquips), React.createElement("div", null, this.state.battledata));
			}
		}
	})
};

